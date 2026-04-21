<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Student;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Helpers\PasswordHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 15, 20, 25, 50]) ? (int) $perPage : 20;

        $query = Student::with(['program', 'division', 'academicSession', 'user', 'fees']);

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->filled('status')) {
            $query->where('student_status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->query('sort', 'created_at');
        $sortDir = $request->query('dir', 'desc');
        $allowedSorts = ['first_name', 'last_name', 'admission_number', 'roll_number', 'email', 'created_at', 'student_status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';

        $students = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->appends($request->query());
        $programs = Program::where('is_active', true)->get();

        return view('dashboard.students.index', compact('students', 'programs', 'sortBy', 'sortDir', 'perPage'));
    }

    public function show(Student $student)
    {
        // Load student with related data including fees
        $student->load(['program', 'division', 'academicSession', 'guardians']);
        
        // Load fee details
        $feeRecords = \App\Models\Fee\StudentFee::where('student_id', $student->id)
            ->with(['feeStructure.feeHead', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalFees = $feeRecords->sum('final_amount');
        $totalPaid = $feeRecords->sum('paid_amount');
        $totalOutstanding = $feeRecords->sum('outstanding_amount');
        
        return view('dashboard.students.show', compact('student', 'feeRecords', 'totalFees', 'totalPaid', 'totalOutstanding'));
    }

    public function create()
    {
        $programs = Program::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();
        return view('dashboard.students.create', compact('programs', 'divisions', 'sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'           => ['required','string','max:100','regex:/^[a-zA-Z\s]+$/'],
            'middle_name'          => ['nullable','string','max:100','regex:/^[a-zA-Z\s]+$/'],
            'last_name'            => ['required','string','max:100','regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth'        => 'required|date|before:today|after:1990-01-01',
            'gender'               => 'required|in:male,female,other',
            'blood_group'          => ['nullable','regex:/^(A|B|AB|O)[+-]$/'],
            'religion'             => 'nullable|string|max:50',
            'category'             => 'required|in:general,obc,sc,st,vjnt,nt,ews',
            'aadhar_number'        => 'nullable|digits:12',
            'mobile_number'        => 'required|regex:/^[6-9]\d{9}$/',
            'email'                => 'required|email|max:255|unique:students,email',
            'current_address'      => 'required|string|min:10|max:500',
            'permanent_address'    => 'nullable|string|min:10|max:500',
            'program_id'           => 'required|exists:standards,id',
            'division_id'          => 'required|exists:divisions,id',
            'academic_session_id'  => 'required|exists:academic_sessions,id,is_active,1',
            'academic_year'        => 'required|string|max:20',
            'admission_date'       => 'required|date|before_or_equal:today',
            'roll_number'          => 'nullable|string|max:20|unique:students,roll_number',
            'photo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cast_certificate'     => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'marksheet'            => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'aadhar'               => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'income_certificate'   => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'domicile_certificate' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'student_status'       => 'required|in:active,graduated,dropped,suspended',
        ]);

        $numbers = $this->generateStudentNumbers($validated['program_id'], $validated['academic_year']);
        $validated['admission_number']      = $numbers['admission_number'];
        $validated['roll_number']           = $numbers['roll_number'];
        $validated['prn']                   = null;
        $validated['university_seat_number'] = null;

        // Generate password for student
        $generatedPassword = PasswordHelper::generate(10);
        $hashedPassword = Hash::make($generatedPassword);

        // Create user account for student with generated password FIRST
        $studentEmail = $request->input('email') ?? strtolower($validated['first_name'] . '.' . $validated['last_name'] . '@student.schoolerp.com');
        
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $studentEmail,
            'password' => $hashedPassword,
            'temp_password' => $generatedPassword,
            'password_generated_at' => now(),
            'email_verified_at' => now(),
            'role' => 'student',
        ]);
        
        // Remove user_id from validated data and set proper user_id
        $validated['user_id'] = $user->id;

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('uploads/students/photos', 'public');
        }
        if ($request->hasFile('signature')) {
            $validated['signature_path'] = $request->file('signature')->store('uploads/students/signatures', 'public');
        }
        if ($request->hasFile('cast_certificate')) {
            $validated['cast_certificate_path'] = $request->file('cast_certificate')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('marksheet')) {
            $validated['marksheet_path'] = $request->file('marksheet')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('aadhar')) {
            $validated['aadhar_path'] = $request->file('aadhar')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('income_certificate')) {
            $validated['income_certificate_path'] = $request->file('income_certificate')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('domicile_certificate')) {
            $validated['domicile_certificate_path'] = $request->file('domicile_certificate')->store('uploads/students/documents', 'public');
        }

        // Create student with user_id already set
        $student = Student::create($validated);

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'Student created successfully with Admission No: ' . $validated['admission_number'])
            ->with('password', $generatedPassword);
    }

    public function edit(Student $student)
    {
        $programs = Program::where('is_active', true)->get();
        $divisions = Division::where('is_active', true)->get();
        $sessions = AcademicSession::where('is_active', true)->get();
        return view('dashboard.students.edit', compact('student', 'programs', 'divisions', 'sessions'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name'              => ['required','string','max:100','regex:/^[a-zA-Z\s]+$/'],
            'middle_name'             => ['nullable','string','max:100','regex:/^[a-zA-Z\s]+$/'],
            'last_name'               => ['required','string','max:100','regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth'           => 'required|date|before:today',
            'gender'                  => 'required|in:male,female,other',
            'blood_group'             => ['nullable','regex:/^(A|B|AB|O)[+-]$/'],
            'religion'                => 'nullable|string|max:50',
            'category'                => 'required|in:general,obc,sc,st,vjnt,nt,ews',
            'mobile_number'           => 'nullable|regex:/^[0-9\+\s\-]+$/|max:15',
            'email'                   => 'nullable|email|max:255|unique:students,email,' . $student->id,
            'current_address'         => 'nullable|string|max:500',
            'permanent_address'       => 'nullable|string|max:500',
            'program_id'              => 'required|exists:standards,id',
            'division_id'             => 'required|exists:divisions,id',
            'academic_session_id'     => 'required|exists:academic_sessions,id,is_active,1',
            'academic_year'           => 'required|string|max:20',
            'admission_date'          => 'required|date|before_or_equal:today',
            'roll_number'             => 'nullable|string|max:20|unique:students,roll_number,' . $student->id,
            'prn'                     => 'nullable|string|max:50|unique:students,prn,' . $student->id,
            'university_seat_number'  => 'nullable|string|max:20',
            'photo'                   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cast_certificate'        => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'marksheet'               => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'aadhar'                  => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'income_certificate'      => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'domicile_certificate'    => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'student_status'          => 'required|in:active,graduated,dropped,suspended',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo_path && Storage::disk('public')->exists($student->photo_path)) {
                Storage::disk('public')->delete($student->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('uploads/students/photos', 'public');
        }
        if ($request->hasFile('signature')) {
            if ($student->signature_path && Storage::disk('public')->exists($student->signature_path)) {
                Storage::disk('public')->delete($student->signature_path);
            }
            $validated['signature_path'] = $request->file('signature')->store('uploads/students/signatures', 'public');
        }
        if ($request->hasFile('cast_certificate')) {
            if ($student->cast_certificate_path && Storage::disk('public')->exists($student->cast_certificate_path)) {
                Storage::disk('public')->delete($student->cast_certificate_path);
            }
            $validated['cast_certificate_path'] = $request->file('cast_certificate')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('marksheet')) {
            if ($student->marksheet_path && Storage::disk('public')->exists($student->marksheet_path)) {
                Storage::disk('public')->delete($student->marksheet_path);
            }
            $validated['marksheet_path'] = $request->file('marksheet')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('aadhar')) {
            if ($student->aadhar_path && Storage::disk('public')->exists($student->aadhar_path)) {
                Storage::disk('public')->delete($student->aadhar_path);
            }
            $validated['aadhar_path'] = $request->file('aadhar')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('income_certificate')) {
            if ($student->income_certificate_path && Storage::disk('public')->exists($student->income_certificate_path)) {
                Storage::disk('public')->delete($student->income_certificate_path);
            }
            $validated['income_certificate_path'] = $request->file('income_certificate')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('domicile_certificate')) {
            if ($student->domicile_certificate_path && Storage::disk('public')->exists($student->domicile_certificate_path)) {
                Storage::disk('public')->delete($student->domicile_certificate_path);
            }
            $validated['domicile_certificate_path'] = $request->file('domicile_certificate')->store('uploads/students/documents', 'public');
        }

        $validated['admission_number'] = $student->admission_number;
        $validated['roll_number']      = $student->roll_number;
        $validated['user_id']          = $student->user_id;

        $student->update($validated);

        // Also update the user's email if it was changed AND the new email is unique in users table
        if ($student->user && isset($validated['email']) && $validated['email'] !== $student->user->email) {
            // Check if email already exists for another user
            $existingUser = User::where('email', $validated['email'])->where('id', '!=', $student->user->id)->first();
            if (!$existingUser) {
                $student->user->update(['email' => $validated['email']]);
            }
        }

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if ($student->fees()->exists()) {
            return redirect()->route('dashboard.students.index')
                ->with('error', 'Student has fee records. Please clear fees before deletion.');
        }
        if ($student->attendances()->exists()) {
            return redirect()->route('dashboard.students.index')
                ->with('error', 'Student has attendance records. Cannot delete.');
        }

        foreach (['photo_path', 'signature_path', 'cast_certificate_path', 'marksheet_path', 'aadhar_path', 'income_certificate_path', 'domicile_certificate_path'] as $field) {
            if ($student->$field && Storage::disk('public')->exists($student->$field)) {
                Storage::disk('public')->delete($student->$field);
            }
        }

        $student->delete();

        return redirect()->route('dashboard.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    private function generateStudentNumbers(int $programId, string $academicYear): array
    {
        $program = Program::find($programId);
        $programCode = strtoupper(substr($program?->code ?? 'STU', 0, 3));
        $yearSuffix = is_numeric($academicYear) ? substr($academicYear, -2) : strtoupper($academicYear);

        $lastStudent = Student::where('program_id', $programId)
            ->where('academic_year', $academicYear)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastStudent ? (intval(substr($lastStudent->admission_number, -4)) + 1) : 1;

        do {
            $rollNumber = strtoupper($academicYear) . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $exists = Student::where('roll_number', $rollNumber)->exists();
            if ($exists) $sequence++;
        } while ($exists && $sequence <= 9999);

        if ($sequence > 9999) {
            throw new \Exception('Maximum student capacity reached for this program and academic year.');
        }

        return [
            'admission_number' => $programCode . $yearSuffix . str_pad($sequence, 4, '0', STR_PAD_LEFT),
            'roll_number'      => $rollNumber,
        ];
    }

    public function bulkAction(Request $request)
    {
        $ids    = $request->student_ids;
        $action = $request->action;

        if (!$ids) {
            return redirect()->back()->with('error', 'No students selected');
        }

        if ($action == 'delete') {
            $studentsWithFees       = Student::whereIn('id', $ids)->whereHas('fees')->pluck('id');
            $studentsWithAttendance = Student::whereIn('id', $ids)->whereHas('attendances')->pluck('id');
            $protectedIds           = $studentsWithFees->merge($studentsWithAttendance)->unique();
            $deletableIds           = collect($ids)->diff($protectedIds);

            if ($deletableIds->isNotEmpty()) {
                $students = Student::whereIn('id', $deletableIds)->get();
                foreach ($students as $student) {
                    foreach (['photo_path', 'signature_path'] as $field) {
                        if ($student->$field && Storage::disk('public')->exists($student->$field)) {
                            Storage::disk('public')->delete($student->$field);
                        }
                    }
                }
                Student::whereIn('id', $deletableIds)->delete();
            }

            if ($protectedIds->isNotEmpty()) {
                return redirect()->back()->with('warning', 'Some students could not be deleted due to related records (fees/attendance)');
            }
            return redirect()->back()->with('success', 'Selected students deleted successfully');
        }

        if ($action == 'activate') {
            Student::whereIn('id', $ids)->update(['student_status' => 'active']);
            return redirect()->back()->with('success', 'Selected students activated successfully');
        }

        if ($action == 'deactivate') {
            Student::whereIn('id', $ids)->update(['student_status' => 'suspended']);
            return redirect()->back()->with('success', 'Selected students deactivated successfully');
        }

        return redirect()->back()->with('error', 'Invalid action');
    }
}

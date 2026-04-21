<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Admission;
use App\Services\AdmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    protected AdmissionService $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function index(Request $request)
    {
        $admissions = Admission::with(['program', 'division'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('application_no', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('admissions.index', compact('admissions'));
    }

    public function show(Admission $admission)
    {
        $admission->load(['program', 'division', 'documents']);
        return view('admissions.show', compact('admission'));
    }

    public function showApplyForm()
    {
        $programs = \App\Models\Academic\Program::where('is_active', true)->get();
        $sessions = \App\Models\Academic\AcademicSession::where('is_active', true)->get();

        // Divisions will be loaded dynamically via API based on selected program
        return view('admissions.apply', compact('programs', 'sessions'));
    }

    public function apply(Request $request)
    {
        // Custom validation for email uniqueness across both users and students tables
        $request->validate([
            'first_name' => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
            'middle_name' => 'nullable|regex:/^[a-zA-Z\s]+$/|max:255',
            'last_name' => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
            'date_of_birth' => 'required|date|before:today|after:1990-01-01',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'religion' => 'nullable|string|max:50',
            'category' => 'required|in:general,obc,sc,st,ews',
            'aadhar_number' => 'nullable|digits:12',
            'mobile_number' => 'required|regex:/^[6-9]\d{9}$/',
            'email' => 'required|email',
            'current_address' => 'required|string|min:10|max:500',
            'permanent_address' => 'nullable|string|min:10|max:500',
            'program_id' => 'required|exists:standards,id',
            'division_id' => 'nullable|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'academic_year' => 'required|in:FY,SY,TY',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'twelfth_marksheet' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
            'cast_certificate' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        // Additional custom validation for email uniqueness across both tables
        $email = $request->email;
        if (\App\Models\User::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'This email is already registered in the system.'])->withInput();
        }
        if (\App\Models\User\Student::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'This email is already registered as a student.'])->withInput();
        }

        // Use all input data since validation passed
        $studentData = $request->all();

        // If permanent address is empty, use current address
        if (empty($studentData['permanent_address'])) {
            $studentData['permanent_address'] = $studentData['current_address'];
        }

        // Handle file uploads - save directly to students table
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $studentData['photo_path'] = $request->file('photo')->store('uploads/students/photos', 'public');
        }
        if ($request->hasFile('signature') && $request->file('signature')->isValid()) {
            $studentData['signature_path'] = $request->file('signature')->store('uploads/students/signatures', 'public');
        }
        if ($request->hasFile('twelfth_marksheet') && $request->file('twelfth_marksheet')->isValid()) {
            $studentData['marksheet_path'] = $request->file('twelfth_marksheet')->store('uploads/students/documents', 'public');
        }
        if ($request->hasFile('cast_certificate') && $request->file('cast_certificate')->isValid()) {
            $studentData['cast_certificate_path'] = $request->file('cast_certificate')->store('uploads/students/documents', 'public');
        }

        // Generate unique admission number
        $year = date('Y');
        $baseNumber = 'ADM' . $year;
        $studentData['admission_date'] = date('Y-m-d');
        $studentData['student_status'] = 'active';

        // Get all existing admission numbers for this year
        $existingNumbers = \App\Models\User\Student::where('admission_number', 'like', $baseNumber . '%')
            ->pluck('admission_number')
            ->toArray();

        // Start from 1 and find first available number
        $nextNumber = 1;
        $admissionNumber = null;
        
        // Try up to 10000 numbers to find an unused one
        while ($nextNumber <= 10000) {
            $candidate = $baseNumber . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            if (!in_array($candidate, $existingNumbers)) {
                $admissionNumber = $candidate;
                break;
            }
            $nextNumber++;
        }
        
        // Ultimate fallback: use timestamp + random if somehow all 10000 are taken
        if (!$admissionNumber) {
            $admissionNumber = $baseNumber . date('YmdHis') . rand(100, 999);
        }
        
        $studentData['admission_number'] = $admissionNumber;

        // Create student
        $student = $this->admissionService->createStudentFromAdmission($studentData);

        // Load relationships for program and division
        $student->load(['program', 'division', 'user']);

        // Get the temp password from the user record
        $user = $student->user;
        $tempPassword = $user->temp_password;

        // Prepare student details for the display with login information
        $studentDetails = [
            'admission_number' => $student->admission_number,
            'full_name' => $student->full_name,
            'email' => $student->email,
            'mobile_number' => $student->mobile_number,
            'program' => $student->program ? $student->program->name : 'N/A',
            'division' => $student->division ? $student->division->division_name : 'N/A',
            'academic_year' => $student->academic_year,
            'admission_date' => $student->admission_date->format('d M Y'),
            'login_url' => url('/student/login'),
            'dashboard_url' => url('/student/dashboard'),
        ];

        return redirect()->route('admissions.apply.form')
            ->with('success', 'Admission submitted successfully! Your Admission No. is: ' . $student->admission_number . '. Please save it for tracking.')
            ->with('student_email', $student->email)
            ->with('temp_password', $tempPassword)
            ->with('student_details', $studentDetails)
            ->with('login_credentials', [
                'username' => $student->email,
                'password' => $tempPassword,
                'login_url' => url('/student/login'),
                'dashboard_url' => url('/student/dashboard'),
            ]);
    }

    public function verify(Request $request, Admission $admission)
    {
        try {
            $this->admissionService->verifyAdmission($admission, $request->notes ?? null);
            
            return redirect()->back()
                ->with('success', 'Admission verified successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            $this->admissionService->rejectAdmission($admission, $validated['rejection_reason']);
            
            return redirect()->back()
                ->with('success', 'Admission rejected.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Enroll a verified admission as a student
     */
    public function enroll(Admission $admission)
    {
        try {
            $student = $this->admissionService->enrollStudent($admission);
            
            return response()->json([
                'success' => true,
                'message' => 'Student enrolled successfully!',
                'student_id' => $student->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}

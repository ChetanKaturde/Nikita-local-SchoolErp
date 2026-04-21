<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\ApiResponse;
use App\Http\Requests\Api\StudentRequest;
use App\Models\User\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * Display a listing of students with pagination and eager loading.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->get('per_page', 25), 100); // Max 100 per page
            
            $students = Student::with(['program', 'division', 'academicSession', 'guardians', 'user'])
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('roll_number', 'like', "%{$search}%")
                          ->orWhere('admission_number', 'like', "%{$search}%");
                    });
                })
                ->when($request->program_id, function ($query, $programId) {
                    $query->where('program_id', $programId);
                })
                ->when($request->division_id, function ($query, $divisionId) {
                    $query->where('division_id', $divisionId);
                })
                ->when($request->student_status, function ($query, $status) {
                    $query->where('student_status', $status);
                })
                ->paginate($perPage);

            return ApiResponse::paginated($students, 'Students retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch students', ['error' => $e->getMessage()]);
            
            return ApiResponse::error(
                'Failed to retrieve students. Please try again.',
                null,
                500
            );
        }
    }

    /**
     * Display the specified student with eager loaded relationships.
     */
    public function show(Student $student)
    {
        try {
            $student->load([
                'program',
                'division',
                'academicSession',
                'guardians',
                'admission.documents',
                'user'
            ]);

            return ApiResponse::success($student, 'Student retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch student', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            
            return ApiResponse::error('Failed to retrieve student', null, 500);
        }
    }

    /**
     * Store a newly created student with validation and exception handling.
     */
    public function store(StudentRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Generate admission number
            $year = date('Y');
            $lastStudent = Student::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();
            $sequence = $lastStudent ? (intval(substr($lastStudent->admission_number, -4)) + 1) : 1;
            $admissionNumber = $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Generate roll number
            $rollNumber = $validated['academic_year'] . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            $studentData = array_merge($validated, [
                'admission_number' => $admissionNumber,
                'roll_number' => $rollNumber,
                'student_status' => 'active',
            ]);

            $student = Student::create($studentData);
            $student->load(['program', 'division', 'academicSession', 'guardians']);

            DB::commit();

            Log::info('Student created successfully', [
                'student_id' => $student->id,
                'admission_number' => $student->admission_number
            ]);

            return ApiResponse::created($student, 'Student created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create student', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return ApiResponse::error(
                'Failed to create student. Please try again.',
                null,
                500
            );
        }
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'mobile_number' => 'nullable|string|max:15',
            'program_id' => 'required|exists:standards,id',
            'division_id' => 'required|exists:divisions,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'academic_year' => 'required|string|in:FY,SY,TY',
            'academic_session_id' => 'required|exists:academic_sessions,id,is_active,1',
            'admission_date' => 'required|date',
            'category' => 'required|string|in:general,obc,sc,st'
        ]);

        $student->update($validated);
        $student->load(['program', 'division', 'academicSession', 'guardians']);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }

    /**
     * Remove the specified student.
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }
}
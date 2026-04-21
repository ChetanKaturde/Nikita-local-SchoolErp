<?php

namespace App\Http\Requests\Attendance;

use App\Models\Academic\Division;
use App\Models\User\Student;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for Marking Attendance
 * 
 * Validates that ALL students in the division have attendance marked
 */
class MarkAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow any authenticated user to mark attendance
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'division_id' => ['required', 'exists:divisions,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'students' => ['required', 'array', 'min:1'],
            'students.*.student_id' => ['required', 'exists:students,id'],
            'students.*.status' => ['required', 'in:Present,Absent,Late,Excused'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'division_id.required' => 'Please select a division.',
            'division_id.exists' => 'Selected division does not exist.',
            'academic_session_id.required' => 'Please select an academic session.',
            'date.required' => 'Attendance date is required.',
            'date.before_or_equal' => 'Attendance date cannot be in the future.',
            'students.required' => 'At least one student is required.',
            'students.array' => 'Students must be provided as an array.',
            'students.*.student_id.required' => 'Student ID is required.',
            'students.*.status.required' => 'Attendance status is required for each student.',
            'students.*.status.in' => 'Status must be Present, Absent, Late, or Excused.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'division_id' => 'division',
            'academic_session_id' => 'academic session',
            'date' => 'date',
            'students' => 'students',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that ALL students in the division have attendance marked
            $this->validateAllStudentsMarked($validator);
        });
    }

    /**
     * Validate that all students in the division have attendance marked
     */
    protected function validateAllStudentsMarked($validator): void
    {
        if (!$this->filled('division_id') || !$this->filled('date')) {
            return;
        }

        // Get all active students in the division
        $allStudentsInDivision = Student::where('division_id', $this->division_id)
            ->where('student_status', 'active')
            ->pluck('id')
            ->toArray();

        // Get students with attendance marked
        $markedStudentIds = collect($this->students ?? [])
            ->pluck('student_id')
            ->toArray();

        // Check if all students have attendance marked
        $missingStudents = array_diff($allStudentsInDivision, $markedStudentIds);

        if (!empty($missingStudents)) {
            // Get names of missing students
            $missingStudentNames = Student::whereIn('id', $missingStudents)
                ->pluck('full_name')
                ->take(5)
                ->implode(', ');

            $countMissing = count($missingStudents);
            $message = "Please mark attendance for all students. {$countMissing} student(s) missing attendance.";
            
            if ($countMissing <= 5) {
                $message = "Please mark attendance for all students. Missing: {$missingStudentNames}";
            }

            $validator->errors()->add('students', $message);
        }
    }
}

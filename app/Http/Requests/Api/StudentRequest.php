<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Student API operations
 */
class StudentRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\User\Student::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Personal Information
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            
            // Contact Information
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
            'mobile_number' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            
            // Academic Information
            'program_id' => ['required', 'integer', 'exists:standards,id'],
            'division_id' => ['required', 'integer', 'exists:divisions,id'],
            'academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id'],
            'academic_year' => ['required', 'string', 'max:20'],
            
            // Guardian Information (optional)
            'guardians' => ['nullable', 'array', 'max:2'],
            'guardians.*.name' => ['required_with:guardians', 'string', 'max:100'],
            'guardians.*.relation' => ['required_with:guardians', Rule::in(['father', 'mother', 'guardian'])],
            'guardians.*.mobile_number' => ['required_with:guardians', 'string', 'regex:/^[6-9]\d{9}$/'],
            'guardians.*.email' => ['nullable', 'email', 'max:255'],
            
            // Status
            'student_status' => ['required', Rule::in(['active', 'graduated', 'dropped', 'suspended'])],
        ];

        // Add unique rule for update
        if ($this->route('student')) {
            $studentId = $this->route('student')->id;
            $rules['email'][2] = 'unique:students,email,' . $studentId;
            $rules['mobile_number'][2] = 'unique:students,mobile_number,' . $studentId;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Student first name is required.',
            'last_name.required' => 'Student last name is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'gender.required' => 'Gender is required.',
            'program_id.required' => 'Please select a program.',
            'division_id.required' => 'Please select a division.',
            'academic_session_id.required' => 'Please select an academic session.',
            'mobile_number.regex' => 'Please enter a valid 10-digit mobile number.',
        ];
    }
}

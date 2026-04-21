<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Updating Existing Student
 * 
 * Similar to StoreStudentRequest but with modifications for updates:
 * - Unique validation excludes current student
 * - Some fields may be optional or restricted
 * - PRN and university seat number can be updated
 */
class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $student = $this->route('student');
        return $this->user()->can('update', $student);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $studentId = $this->route('student')->id;

        return [
            // Personal Details
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'religion' => ['nullable', 'string', 'max:50'],
                        'category' => ['required', Rule::in(['general', 'obc', 'sc', 'st', 'vjnt', 'nt', 'ews', 'sbc'])],
            'aadhar_number' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/', Rule::unique('students')->ignore($studentId)],

            // Contact Information
            'mobile_number' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/', Rule::unique('students')->ignore($studentId)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('students')->ignore($studentId)],
            'current_address' => ['nullable', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],

            // Academic Information
            'program_id' => ['required', 'integer', 'exists:standards,id'],
            'division_id' => ['required', 'integer', 'exists:divisions,id'],
            'academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id,is_active,1'],
            'academic_year' => ['required', 'string', 'max:20'],
            'admission_date' => ['required', 'date', 'before_or_equal:today'],

            // University Details (can be updated)
            'prn' => ['nullable', 'string', 'max:50', Rule::unique('students')->ignore($studentId)],
            'university_seat_number' => ['nullable', 'string', 'max:20'],

            // Files
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:1024'],

            // Status
            'student_status' => ['required', Rule::in(['active', 'graduated', 'dropped', 'suspended', 'tc_issued'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Student first name is required.',
            'first_name.regex' => 'First name should contain only letters and spaces.',
            'prn.unique' => 'This PRN is already assigned to another student.',
            'aadhar_number.unique' => 'This Aadhar number is already registered.',
            'mobile_number.unique' => 'This mobile number is already registered.',
            'email.unique' => 'This email is already registered.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => $this->first_name ? trim($this->first_name) : null,
            'middle_name' => $this->middle_name ? trim($this->middle_name) : null,
            'last_name' => $this->last_name ? trim($this->last_name) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'prn' => $this->prn ? strtoupper(trim($this->prn)) : null,
        ]);
    }
}

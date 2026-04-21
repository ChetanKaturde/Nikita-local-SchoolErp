<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for Creating New Student
 * 
 * This class handles all validation logic for student creation.
 * Benefits:
 * - Keeps controllers clean
 * - Reusable validation rules
 * - Automatic validation before controller method
 * - Custom error messages
 * - Authorization logic in one place
 */
class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        // Check if user has permission to create students
        // You can customize this based on your role/permission system
        return $this->user()->can('create', \App\Models\User\Student::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Personal Details
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:' . now()->subYears(100)->format('Y-m-d')],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'religion' => ['nullable', 'string', 'max:50'],
                        'category' => ['required', Rule::in(['general', 'obc', 'sc', 'st', 'vjnt', 'nt', 'ews', 'sbc'])],
            'aadhar_number' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/', 'unique:students,aadhar_number'],

            // Contact Information
            'mobile_number' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/', 'unique:students,mobile_number'],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
            'current_address' => ['nullable', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],

            // Academic Information
            'program_id' => ['required', 'integer', 'exists:standards,id'],
            'division_id' => ['required', 'integer', 'exists:divisions,id'],
            'academic_session_id' => ['required', 'integer', 'exists:academic_sessions,id,is_active,1'],
            'academic_year' => ['required', 'string', 'max:20'],
            'admission_date' => ['required', 'date', 'before_or_equal:today'],

            // Files
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // 2MB max
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:1024'], // 1MB max

            // Status
            'student_status' => ['required', Rule::in(['active', 'graduated', 'dropped', 'suspended'])],

            // Guardian Information (optional during creation)
            'guardians' => ['nullable', 'array', 'max:2'],
            'guardians.*.name' => ['required_with:guardians', 'string', 'max:100'],
            'guardians.*.relation' => ['required_with:guardians', Rule::in(['father', 'mother', 'guardian'])],
            'guardians.*.mobile_number' => ['required_with:guardians', 'string', 'regex:/^[6-9]\d{9}$/'],
            'guardians.*.email' => ['nullable', 'email', 'max:255'],
            'guardians.*.occupation' => ['nullable', 'string', 'max:100'],
            'guardians.*.annual_income' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Student first name is required.',
            'first_name.regex' => 'First name should contain only letters and spaces.',
            'last_name.required' => 'Student last name is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be male, female, or other.',
            'aadhar_number.size' => 'Aadhar number must be exactly 12 digits.',
            'aadhar_number.regex' => 'Aadhar number must contain only digits.',
            'aadhar_number.unique' => 'This Aadhar number is already registered.',
            'mobile_number.regex' => 'Mobile number must be a valid 10-digit Indian number.',
            'mobile_number.unique' => 'This mobile number is already registered.',
            'email.unique' => 'This email is already registered.',
            'program_id.required' => 'Program selection is required.',
            'program_id.exists' => 'Selected program does not exist.',
            'division_id.required' => 'Division selection is required.',
            'division_id.exists' => 'Selected division does not exist.',
            'academic_session_id.required' => 'Academic session is required.',
            'photo.image' => 'Photo must be an image file.',
            'photo.mimes' => 'Photo must be in JPEG, PNG, or JPG format.',
            'photo.max' => 'Photo size must not exceed 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'middle_name' => 'middle name',
            'last_name' => 'last name',
            'date_of_birth' => 'date of birth',
            'mobile_number' => 'mobile number',
            'program_id' => 'program',
            'division_id' => 'division',
            'academic_session_id' => 'academic session',
            'academic_year' => 'academic year',
        ];
    }

    /**
     * Prepare the data for validation.
     * This runs before validation
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string fields
        $this->merge([
            'first_name' => $this->first_name ? trim($this->first_name) : null,
            'middle_name' => $this->middle_name ? trim($this->middle_name) : null,
            'last_name' => $this->last_name ? trim($this->last_name) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
        ]);
    }

    /**
     * Handle a passed validation attempt.
     * This runs after successful validation
     */
    protected function passedValidation(): void
    {
        // You can add additional logic here after validation passes
        // For example, logging, notifications, etc.
    }
}

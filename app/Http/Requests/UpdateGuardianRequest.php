<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Personal Details
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'relation' => 'required|in:father,mother,guardian,uncle,aunt,grandfather,grandmother,other',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'occupation' => 'nullable|string|max:100',
            'annual_income' => 'nullable|numeric|min:0|max:99999999',
            'education_qualification' => 'nullable|string|max:100',

            // Contact Details
            'mobile_number' => [
                'required',
                'regex:/^[6-9]\d{9}$/',
                'size:10'
            ],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',

            // Photo
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Primary Contact
            'is_primary_contact' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Guardian first name is required.',
            'first_name.regex' => 'Guardian first name should only contain letters and spaces.',
            'last_name.required' => 'Guardian last name is required.',
            'last_name.regex' => 'Guardian last name should only contain letters and spaces.',
            'relation.required' => 'Please select the relation to student.',
            'relation.in' => 'Please select a valid relation.',
            'gender.required' => 'Please select guardian gender.',
            'mobile_number.required' => 'Guardian mobile number is required.',
            'mobile_number.regex' => 'Mobile number must be 10 digits starting with 6-9 (e.g., 9876543210).',
            'mobile_number.size' => 'Mobile number must be exactly 10 digits.',
            'email.email' => 'Please enter a valid email address.',
            'photo.image' => 'Guardian photo must be an image file.',
            'photo.mimes' => 'Guardian photo must be in JPG, PNG, or GIF format.',
            'photo.max' => 'Guardian photo size should not exceed 2MB.',
            'annual_income.numeric' => 'Annual income must be a number.',
            'annual_income.min' => 'Annual income cannot be negative.',
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'guardian first name',
            'last_name' => 'guardian last name',
            'mobile_number' => 'guardian mobile number',
            'email' => 'guardian email',
            'photo' => 'guardian photo',
        ];
    }
}
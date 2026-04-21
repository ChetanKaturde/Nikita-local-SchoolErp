<?php

namespace App\Http\Requests\Fee;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for Fee Structure Operations
 */
class StoreFeeStructureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage fee structures', \App\Models\Fee\FeeStructure::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'fee_head_id' => ['required', 'exists:fee_heads,id'],
            'program_id' => ['required', 'exists:standards,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'frequency' => ['required', Rule::in(['one-time', 'monthly', 'quarterly', 'half-yearly', 'yearly'])],
            'due_date' => ['nullable', 'date'],
            'late_fee' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'fee_head_id.required' => 'Please select a fee head.',
            'program_id.required' => 'Please select a program.',
            'academic_session_id.required' => 'Please select an academic session.',
            'amount.required' => 'Fee amount is required.',
            'amount.min' => 'Fee amount cannot be negative.',
            'frequency.required' => 'Fee frequency is required.',
            'frequency.in' => 'Invalid fee frequency selected.',
            'late_fee.min' => 'Late fee cannot be negative.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'fee_head_id' => 'fee head',
            'program_id' => 'program',
            'division_id' => 'division',
            'academic_session_id' => 'academic session',
            'amount' => 'fee amount',
            'late_fee' => 'late fee',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => $this->description ? trim($this->description) : null,
        ]);
    }
}

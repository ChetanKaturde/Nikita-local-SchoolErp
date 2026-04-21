<?php

namespace App\Models\Fee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_id', 'installment_number', 'receipt_number', 'amount', 'payment_mode', 
        'transaction_id', 'payment_date', 'due_date', 'status', 'remarks'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date'
    ];

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(
            \App\Models\User\Student::class,
            StudentFee::class,
            'id', // Foreign key on student_fees table
            'id', // Foreign key on students table
            'student_fee_id', // Local key on fee_payments table
            'student_id' // Local key on student_fees table
        );
    }

    public function feeStructure(): HasOneThrough
    {
        return $this->hasOneThrough(
            FeeStructure::class,
            StudentFee::class,
            'id',
            'id',
            'student_fee_id',
            'fee_structure_id'
        );
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }
}
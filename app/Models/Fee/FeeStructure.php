<?php

namespace App\Models\Fee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Academic\Program;
use App\Models\Academic\Division;

class FeeStructure extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\FeeStructureFactory::new();
    }

    protected $fillable = ['program_id', 'division_id', 'academic_year', 'fee_head_id', 'amount', 'installments', 'is_active'];

    protected $casts = [
        'amount' => 'decimal:2',
        'installments' => 'integer',
        'is_active' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function feeHead(): BelongsTo
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
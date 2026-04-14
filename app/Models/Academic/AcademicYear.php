<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\AcademicYearFactory::new();
    }

    protected $fillable = [
        'program_id',
        'year_number',
        'year_name',
        'semester_start',
        'semester_end',
        'is_active',
        'start_date',
        'end_date',
        'name',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'year_number' => 'integer',
        'semester_start' => 'integer',
        'semester_end' => 'integer',
    ];

    // Relationships
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Get the name attribute
     */
    public function getNameAttribute(): string
    {
        return $this->year_name ?? 'Year ' . $this->year_number;
    }

    /**
     * Get current academic year ID
     */
    public static function getCurrentAcademicYearId(): ?int
    {
        $year = self::getCurrentAcademicYear();
        return $year?->id;
    }

    /**
     * Get current academic year
     */
    public static function getCurrentAcademicYear(): ?self
    {
        return self::where('is_active', true)
            ->orderBy('year_number', 'desc')
            ->first();
    }

    /**
     * Scope for active academic years
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
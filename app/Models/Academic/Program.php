<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $table = 'standards';
    protected $fillable = [
        'name',
        'short_name',
        'code',
        'department_id',
        'duration_years',
        'total_semesters',
        'total_seats',
        'program_type',
        'university_affiliation',
        'university_program_code',
        'default_grade_scale_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_years' => 'integer',
        'total_seats' => 'integer'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(\App\Models\User\Student::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Get enrolled student count
    public function getEnrolledCountAttribute()
    {
        return $this->students()->where('student_status', 'active')->count();
    }
    
    // Get available seats
    public function getAvailableSeatsAttribute()
    {
        return max(0, ($this->total_seats ?? 0) - $this->enrolled_count);
    }
    
    // Check if seats available
    public function hasAvailableSeats()
    {
        return $this->available_seats > 0;
    }
}
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\DivisionFactory::new();
    }

    protected $fillable = [
        'program_id',
        'session_id',
        'academic_year_id',
        'division_name',
        'max_students',
        'class_teacher_id',
        'classroom',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_students' => 'integer',
    ];

    // Relationships
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\AcademicSession::class, 'session_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\AcademicSession::class, 'academic_year_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(\App\Models\User\Student::class);
    }

    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'class_teacher_id');
    }

    /**
     * Get all teachers assigned to this division via timetable
     */
    public function teachers(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\User::class,
            \App\Models\Academic\Timetable::class,
            'division_id',
            'id',
            'id',
            'teacher_id'
        )->distinct();
    }

    /**
     * Get all subjects for this division via timetable
     */
    public function subjects(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\Academic\Subject::class,
            \App\Models\Academic\Timetable::class,
            'division_id',
            'id',
            'id',
            'subject_id'
        )->distinct();
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(\App\Models\Academic\Timetable::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('division_name', 'like', "%{$search}%")
            ->orWhereHas('program', fn($q) => $q->where('name', 'like', "%{$search}%"));
    }

    // Accessors
    public function getCurrentCountAttribute(): int
    {
        return $this->students()->where('student_status', 'active')->count();
    }

    public function getAvailableSeatsAttribute(): int
    {
        return $this->max_students - $this->current_count;
    }

    public function getCapacityPercentageAttribute(): float
    {
        return $this->max_students > 0 ? ($this->current_count / $this->max_students) * 100 : 0;
    }

    public function getCapacityStatusAttribute(): string
    {
        $percentage = $this->capacity_percentage;
        if ($percentage >= 90) return 'danger';
        if ($percentage >= 50) return 'warning';
        return 'success';
    }

    // Methods
    public function hasCapacity(int $count = 1): bool
    {
        return $this->available_seats >= $count;
    }

    public function canAssignStudents(array $studentIds): bool
    {
        return $this->hasCapacity(count($studentIds));
    }

    // Accessor for name attribute used in forms
    public function getNameAttribute(): string
    {
        return $this->division_name;
    }
}
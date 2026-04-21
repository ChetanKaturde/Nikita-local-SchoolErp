<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'temp_password',
        'password_generated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'temp_password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'password_generated_at' => 'datetime',
        ];
    }

    public function reportExports()
    {
        return $this->hasMany(\App\Models\Reports\ReportExport::class);
    }

    public function reportTemplates()
    {
        return $this->hasMany(\App\Models\Reports\ReportTemplate::class, 'created_by');
    }

    // 👇 ADD THIS RELATIONSHIP
    /**
     * A user can have one student profile
     */
    public function student(): HasOne
    {
        return $this->hasOne(\App\Models\User\Student::class);
    }

    /**
     * A user can have one teacher profile
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /**
     * Get divisions assigned to this teacher
     */
    public function teacherDivisions()
    {
        return $this->belongsToMany(
            \App\Models\Academic\Division::class,
            'teacher_divisions',
            'teacher_id',
            'division_id'
        )
            ->withPivot(['is_class_teacher', 'is_active', 'academic_session_id'])
            ->withTimestamps();
    }

    /**
     * A teacher can be assigned to one division as class teacher
     * This checks both the divisions.class_teacher_id field and teacher_assignments table
     * Note: This returns the actual division, not a relationship
     */
    public function getAssignedDivisionAttribute()
    {
        // First try to get from teacher_assignments table (active assignment)
        $assignment = \App\Models\TeacherAssignment::where('teacher_id', $this->id)
            ->where('assignment_type', 'division')
            ->where('is_active', true)
            ->first();

        if ($assignment && $assignment->division) {
            return $assignment->division;
        }

        // Fallback to class_teacher_id field
        return \App\Models\Academic\Division::where('class_teacher_id', $this->id)->first();
    }

    /**
     * Get all assigned divisions for a teacher (as a list attribute)
     */
    public function getAssignedDivisionsListAttribute()
    {
        return \App\Models\TeacherAssignment::where('teacher_id', $this->id)
            ->where('is_active', true)
            ->with('division')
            ->get()
            ->filter(function($assignment) {
                return $assignment->division !== null;
            });
    }

    /**
     * Relationship: Divisions where this user is class teacher
     */
    public function classTeacherDivisions()
    {
        return $this->hasMany(\App\Models\Academic\Division::class, 'class_teacher_id');
    }

    /**
     * A user (teacher) can have many timetable entries
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(\App\Models\Academic\Timetable::class, 'teacher_id');
    }
    
    /**
     * Get all permissions (from role and direct user permissions)
     */
    public function getAllPermissionsAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getAllPermissions();
    }
    
    /**
     * Check if user has permission to access a module
     */
    public function canAccessModule(string $module): bool
    {
        return $this->can($module . '.view') || 
               $this->can($module . '.create') || 
               $this->can($module . '.edit') || 
               $this->can($module . '.delete') ||
               $this->can($module . '.manage');
    }
    
    /**
     * Get user's role names
     */
    public function getRoleNamesListAttribute(): string
    {
        return $this->getRoleNames()->implode(', ');
    }
    
    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}

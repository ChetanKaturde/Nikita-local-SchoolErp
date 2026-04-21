<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User\Student;
use App\Models\User;

/**
 * Unified Attendance Model
 * 
 * Represents a student's attendance record for a specific date/class.
 * This is the canonical model - all attendance records should use this.
 * 
 * @property int $id
 * @property int $student_id
 * @property int|null $division_id
 * @property int|null $academic_session_id
 * @property int|null $timetable_id
 * @property \Carbon\Carbon $date
 * @property string|null $check_in_time
 * @property string|null $check_out_time
 * @property string $status (present, absent, late)
 * @property int|null $marked_by
 * @property string|null $remarks
 * @property string|null $ip_address
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Attendance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'attendance';

    /**
     * Map 'date' attribute to 'date' column (no mapping needed)
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_time' => 'datetime:H:i:s',
            'check_out_time' => 'datetime:H:i:s',
        ];
    }

    /**
     * Remove date accessor - column is already named 'date'
     */

    /**
     * Remove date mutator - column is already named 'date'
     */

    /**
     * Status constants
     */
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'division_id',
        'academic_session_id',
        'timetable_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'marked_by',
        'remarks',
        'ip_address',
    ];

/**
     * Mutator: Automatically convert status to lowercase before saving
     * This fixes case mismatch between request (Present) and database (present)
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    /**
     * Get the student for this attendance record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the division for this attendance record.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the academic session for this attendance record.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the timetable entry (lecture) for this attendance.
     */
    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    /**
     * Get the subject for this attendance (via timetable).
     */
    public function getSubjectAttribute()
    {
        return $this->timetable ? $this->timetable->subject : null;
    }

    /**
     * Get the teacher who marked this attendance.
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Scope: Filter by date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope: Filter by division.
     */
    public function scopeByDivision($query, $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    /**
     * Scope: Filter by student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: Filter by academic session.
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Check if attendance is marked as present.
     */
    public function isPresent(): bool
    {
        return $this->status === self::STATUS_PRESENT;
    }

    /**
     * Check if attendance is marked as absent.
     */
    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_ABSENT;
    }

    /**
     * Check if attendance is marked as late.
     */
    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }

    /**
     * Calculate attendance percentage for a student
     */
    public static function getPercentageForStudent(int $studentId, int $divisionId): float
    {
        $totalRecords = self::where('student_id', $studentId)
            ->where('division_id', $divisionId)
            ->count();

        if ($totalRecords === 0) {
            return 0.0;
        }

        $presentRecords = self::where('student_id', $studentId)
            ->where('division_id', $divisionId)
            ->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE])
            ->count();

        return round(($presentRecords / $totalRecords) * 100, 2);
    }

    /**
     * Get the color for the status badge
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PRESENT => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_LATE => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get the icon for the status badge
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PRESENT => 'check-circle',
            self::STATUS_ABSENT => 'x-circle',
            self::STATUS_LATE => 'exclamation-circle',
            default => 'question-circle',
        };
    }
}

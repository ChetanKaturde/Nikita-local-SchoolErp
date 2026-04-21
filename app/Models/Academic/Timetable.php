<?php

namespace App\Models\Academic;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Academic\AcademicYear;

/**
 * Timetable Model
 * 
 * Represents a class schedule entry in the academic timetable system.
 * Supports multiple conflict detection for divisions, teachers, and rooms.
 * 
 * @property int $id
 * @property int $division_id
 * @property int $subject_id
 * @property int|null $teacher_id
 * @property int|null $room_id
 * @property string $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property string|null $period_name
 * @property string|null $room_number
 * @property int $academic_year_id
 * @property bool $is_break_time
 * @property bool $is_active
 * @property string $status
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @package App\Models\Academic
 */
class Timetable extends Model
{
    use HasFactory;

    /**
     * Status constants
     * 
     * - closed: Past date (yesterday or older) - cannot mark attendance
     * - active: Today's date - can mark attendance
     * - upcoming: Future date - cannot mark attendance yet
     * - active: Weekly timetable (no specific date) - can mark attendance
     * - cancelled: Manually cancelled
     * - completed: Completed
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_UPCOMING = 'upcoming';
    const STATUS_CLOSED = 'closed';

    /**
     * Days of the week
     */
    const DAYS = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
    ];

    /**
     * Color codes for days (for UI display)
     */
    const DAY_COLORS = [
        'monday' => '#3B82F6',    // Blue
        'tuesday' => '#8B5CF6',  // Purple
        'wednesday' => '#10B981', // Green
        'thursday' => '#F59E0B',  // Amber
        'friday' => '#EF4444',    // Red
        'saturday' => '#6366F1',  // Indigo
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'division_id',
        'subject_id',
        'teacher_id',
        'room_id',
        'day_of_week',
        'date',
        'start_time',
        'end_time',
        'period_name',
        'room_number',
        'academic_year_id',
        'is_break_time',
        'is_active',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_break_time' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Default attribute values
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'upcoming',
        'is_active' => true,
        'is_break_time' => false,
    ];

    /**
     * ============================================================
     * AUTOMATIC STATUS BASED ON DATE
     * ============================================================
     */

    /**
     * Determine the appropriate status based on the timetable date.
     * 
     * - Past date (yesterday or older): 'closed'
     * - Today's date: 'active'
     * - Future date: 'upcoming'
     * - No date (weekly timetable): 'active'
     *
     * @return string
     */
    public function getComputedStatusAttribute(): string
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->copy()->subDay()->format('Y-m-d');

        // If has a specific date, compute based on that date
        if (!empty($this->date)) {
            $timetableDate = $this->date instanceof \Carbon\Carbon 
                ? $this->date->format('Y-m-d') 
                : $this->date;
            
            // If date is yesterday or older, it's closed
            if ($timetableDate <= $yesterday) {
                return self::STATUS_CLOSED;
            }
            
            // If date is today, it's active
            if ($timetableDate == $today) {
                return self::STATUS_ACTIVE;
            }
            
            // If date is in the future, it's upcoming
            if ($timetableDate > $today) {
                return self::STATUS_UPCOMING;
            }
        }
        
        // If using day_of_week instead of specific date
        if (!empty($this->day_of_week)) {
            $todayDay = strtolower(now()->format('l'));
            $timetableDay = strtolower($this->day_of_week);
            
            // If today matches the day_of_week, it's active
            if ($todayDay === $timetableDay) {
                return self::STATUS_ACTIVE;
            }
            
            // Otherwise, it's upcoming (will be active on the scheduled day)
            return self::STATUS_UPCOMING;
        }
        
        // Fallback to stored status or active
        return $this->status ?? self::STATUS_ACTIVE;
    }

    /**
     * Check if attendance can be marked
     *
     * Attendance can be marked when:
     * - Timetable date is today (for date-based timetables)
     * - Timetable day_of_week is today (for day-based timetables)
     * - Status is NOT closed
     *
     * @return bool
     */
    public function isActiveForAttendance(): bool
    {
        // Use status directly (computed_status is an accessor that may not exist)
        $status = $this->status;

        // If status is closed, cannot mark attendance
        if ($status === self::STATUS_CLOSED) {
            return false;
        }

        // If has a specific date, check if date is today
        if (!empty($this->date)) {
            $timetableDate = $this->date instanceof \Carbon\Carbon
                ? $this->date->format('Y-m-d')
                : $this->date;

            $today = now()->format('Y-m-d');

            // If date is in the past, cannot mark attendance
            if ($timetableDate < $today) {
                return false;
            }

            // If date is in the future, cannot mark attendance
            if ($timetableDate > $today) {
                return false;
            }

            // Date is today - allow attendance
            return true;
        }

        // If using day_of_week instead of specific date
        if (!empty($this->day_of_week)) {
            $today = strtolower(now()->format('l'));
            $timetableDay = strtolower($this->day_of_week);

            // If today matches the day_of_week, allow attendance
            return $today === $timetableDay;
        }

        // Fallback: allow if not closed (for timetables without date or day_of_week)
        return $status !== self::STATUS_CLOSED;
    }

    /**
     * Check if the timetable is closed (past date)
     * 
     * @return bool
     */
    public function isClosed(): bool
    {
        if (empty($this->date)) {
            return false;
        }
        
        $timetableDate = $this->date instanceof \Carbon\Carbon 
            ? $this->date->format('Y-m-d') 
            : $this->date;
        
        $yesterday = now()->subDay()->format('Y-m-d');
        
        return $timetableDate <= $yesterday || $this->status === self::STATUS_CLOSED;
    }

    /**
     * Get human-readable status text
     * 
     * @return string
     */
    public function getStatusTextAttribute(): string
    {
        if (!empty($this->date)) {
            $timetableDate = $this->date instanceof \Carbon\Carbon 
                ? $this->date->format('Y-m-d') 
                : $this->date;
            
            $today = now()->format('Y-m-d');
            $yesterday = now()->subDay()->format('Y-m-d');
            
            if ($timetableDate <= $yesterday) {
                return 'Closed';
            }
            if ($timetableDate == $today) {
                return 'Active';
            }
            return 'Upcoming';
        }
        
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_UPCOMING => 'Upcoming',
            self::STATUS_CLOSED => 'Closed',
            default => ucfirst($this->status),
        };
    }

    /**
     * ============================================================
     * RELATIONSHIPS
     * ============================================================
     */

    /**
     * Get the division this timetable belongs to
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the subject for this timetable
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher for this timetable
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the room for this timetable
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the academic year for this timetable
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all timetable entries for the same division on the same day
     */
    public function sameDayEntries(): HasMany
    {
        return $this->hasMany(Timetable::class, 'division_id', 'division_id')
            ->where('day_of_week', $this->day_of_week)
            ->where('id', '!=', $this->id);
    }

    /**
     * ============================================================
     * SCOPES
     * ============================================================
     */

    /**
     * Scope: Only active timetables
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by division
     */
    public function scopeByDivision(Builder $query, int $divisionId): Builder
    {
        return $query->where('division_id', $divisionId);
    }

    /**
     * Scope: Filter by teacher
     */
    public function scopeByTeacher(Builder $query, int $teacherId): Builder
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope: Filter by room
     */
    public function scopeByRoom(Builder $query, int $roomId): Builder
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Scope: Filter by day of week
     */
    public function scopeByDay(Builder $query, string $day): Builder
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Scope: Filter by academic year
     */
    public function scopeByAcademicYear(Builder $query, ?int $yearId = null): Builder
    {
        if ($yearId === null) {
            // Use current academic year if no yearId provided
            $yearId = AcademicYear::getCurrentAcademicYearId();
        }

        if ($yearId === null) {
            return $query; // No filter applied if no academic year found
        }

        return $query->where('academic_year_id', $yearId);
    }

    /**
     * Scope: Filter by date
     */
    public function scopeByDate(Builder $query, $date): Builder
    {
        if ($date instanceof \Carbon\Carbon) {
            $date = $date->format('Y-m-d');
        }
        return $query->whereDate('date', $date);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        if ($startDate instanceof \Carbon\Carbon) {
            $startDate = $startDate->format('Y-m-d');
        }
        if ($endDate instanceof \Carbon\Carbon) {
            $endDate = $endDate->format('Y-m-d');
        }
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by specific date OR day of week
     */
    public function scopeByDateOrDay(Builder $query, $date, $dayOfWeek): Builder
    {
        if ($date instanceof \Carbon\Carbon) {
            $date = $date->format('Y-m-d');
        }
        return $query->where(function ($q) use ($date, $dayOfWeek) {
            $q->whereDate('date', $date)
              ->orWhere('day_of_week', $dayOfWeek);
        });
    }

    /**
     * Scope: Exclude break times
     */
    public function scopeNotBreakTime(Builder $query): Builder
    {
        return $query->where('is_break_time', false);
    }

    /**
     * Scope: Order by day of week and time
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->orderBy('start_time');
    }

    /**
     * Scope: Search across subject, teacher, room
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('subject', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        })
            ->orWhereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhere('room_number', 'like', "%{$search}%");
    }

    /**
     * Scope: With all relationships (eager loading)
     */
    public function scopeWithRelationships(Builder $query): Builder
    {
        return $query->with(['division', 'subject', 'teacher', 'room', 'academicYear']);
    }

    /**
     * ============================================================
     * CONFLICT DETECTION METHODS
     * ============================================================
     */

    /**
     * Check if there's a division conflict (same division, same day, overlapping time)
     *
     * @param int $divisionId
     * @param string $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return bool
     */
    public static function checkDivisionConflict(
        int $divisionId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $query = self::where('division_id', $divisionId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_break_time', false)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function ($q) use ($startTime, $endTime) {
                // Check for overlapping time ranges
                $q->where(function ($q2) use ($startTime, $endTime) {
                    // New time starts during existing time
                    $q2->where('start_time', '<', $endTime)
                        ->where('start_time', '>=', $startTime);
                })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        // New time ends during existing time
                        $q2->where('end_time', '>', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        // New time completely contains existing time
                        $q2->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        // Existing time completely contains new time
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if there's a teacher conflict (same teacher, same day, overlapping time)
     *
     * @param int $teacherId
     * @param string $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return bool
     */
    public static function checkTeacherConflict(
        int $teacherId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $query = self::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_break_time', false)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                        ->where('start_time', '>=', $startTime);
                })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('end_time', '>', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if there's a room conflict (same room, same day, overlapping time)
     *
     * @param int $roomId
     * @param string $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return bool
     */
    public static function checkRoomConflict(
        int $roomId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $query = self::where('room_id', $roomId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_break_time', false)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                        ->where('start_time', '>=', $startTime);
                })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('end_time', '>', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if there's any type of conflict
     *
     * @param array $data
     * @param int|null $excludeId
     * @return array Array of conflicts found ['division' => bool, 'teacher' => bool, 'room' => bool]
     */
    public static function checkAllConflicts(array $data, ?int $excludeId = null): array
    {
        return [
            'division' => self::checkDivisionConflict(
                $data['division_id'],
                $data['day_of_week'],
                $data['start_time'],
                $data['end_time'],
                $excludeId
            ),
            'teacher' => self::checkTeacherConflict(
                $data['teacher_id'],
                $data['day_of_week'],
                $data['start_time'],
                $data['end_time'],
                $excludeId
            ),
            'room' => isset($data['room_id']) ? self::checkRoomConflict(
                $data['room_id'],
                $data['day_of_week'],
                $data['start_time'],
                $data['end_time'],
                $excludeId
            ) : false,
        ];
    }

    /**
     * Check if there's a division conflict for a specific date
     *
     * @param int $divisionId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return bool
     */
    public static function checkDateDivisionConflict(
        int $divisionId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $query = self::where('division_id', $divisionId)
            ->whereDate('date', $date)
            ->where('is_break_time', false)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                        ->where('start_time', '>=', $startTime);
                })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('end_time', '>', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if there's a teacher conflict for a specific date
     *
     * @param int $teacherId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return bool
     */
    public static function checkTeacherDateConflict(
        int $teacherId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $query = self::where('teacher_id', $teacherId)
            ->whereDate('date', $date)
            ->where('is_break_time', false)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                        ->where('start_time', '>=', $startTime);
                })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('end_time', '>', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if there's a room conflict for a specific date
     *
     * @param int $roomId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId
     * @return bool
     */
    public static function checkRoomDateConflict(
        int $roomId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $query = self::where('room_id', $roomId)
            ->whereDate('date', $date)
            ->where('is_break_time', false)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                        ->where('start_time', '>=', $startTime);
                })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('end_time', '>', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '>=', $startTime)
                            ->where('end_time', '<=', $endTime);
                    })
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * ============================================================
     * LEGACY METHODS (for backward compatibility)
     * ============================================================
     */

    /**
     * Legacy method - check for overlapping time slots
     * 
     * @deprecated Use checkDivisionConflict instead
     */
    public static function checkOverlap(
        int $divisionId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        return self::checkDivisionConflict($divisionId, $dayOfWeek, $startTime, $endTime, $excludeId);
    }

    /**
     * ============================================================
     * ACCESSORS & MUTATORS
     * ============================================================
     */

    /**
     * Get formatted time range
     * Example: "09:00 - 10:00"
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        $start = $this->start_time instanceof \DateTime 
            ? $this->start_time->format('H:i') 
            : substr($this->start_time, 0, 5);
        $end = $this->end_time instanceof \DateTime 
            ? $this->end_time->format('H:i') 
            : substr($this->end_time, 0, 5);
        
        return "{$start} - {$end}";
    }

    /**
     * Get the day display name
     */
    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }

    /**
     * Get the day color for UI
     */
    public function getDayColorAttribute(): string
    {
        return self::DAY_COLORS[$this->day_of_week] ?? '#6B7280';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'badge-success',
            self::STATUS_CANCELLED => 'badge-danger',
            self::STATUS_COMPLETED => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    /**
     * Check if this timetable is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if this timetable is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->is_active;
    }

    /**
     * ============================================================
     * HELPER METHODS
     * ============================================================
     */

    /**
     * Get all conflicts for this timetable entry
     *
     * @return array Array of conflict information
     */
    public function getConflicts(): array
    {
        $conflicts = [];

        // Check division conflicts
        if (self::checkDivisionConflict(
            $this->division_id,
            $this->day_of_week,
            $this->start_time,
            $this->end_time,
            $this->id
        )) {
            $conflicts[] = [
                'type' => 'division',
                'message' => 'Division has another class at this time',
            ];
        }

        // Check teacher conflicts
        if ($this->teacher_id && self::checkTeacherConflict(
            $this->teacher_id,
            $this->day_of_week,
            $this->start_time,
            $this->end_time,
            $this->id
        )) {
            $conflicts[] = [
                'type' => 'teacher',
                'message' => 'Teacher is assigned to another class at this time',
            ];
        }

        // Check room conflicts
        if ($this->room_id && self::checkRoomConflict(
            $this->room_id,
            $this->day_of_week,
            $this->start_time,
            $this->end_time,
            $this->id
        )) {
            $conflicts[] = [
                'type' => 'room',
                'message' => 'Room is already booked at this time',
            ];
        }

        return $conflicts;
    }

    /**
     * Cancel this timetable entry
     *
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Restore this timetable entry
     *
     * @return bool
     */
    public function restore(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get display information for this timetable entry
     *
     * @return array
     */
    public function getDisplayInfoAttribute(): array
    {
        return [
            'subject' => $this->subject->name ?? 'N/A',
            'subject_code' => $this->subject->code ?? 'N/A',
            'teacher' => $this->teacher->name ?? 'No Teacher',
            'room' => $this->room->room_number ?? $this->room_number ?? 'TBA',
            'time' => $this->formatted_time_range,
            'day' => $this->day_name,
            'status' => $this->status,
        ];
    }

    /**
     * Copy this timetable to another academic year
     *
     * @param int $newAcademicYearId
     * @param int|null $newDivisionId (optional) - copy to different division
     * @return Timetable|null
     */
    public function copyToAcademicYear(int $newAcademicYearId, ?int $newDivisionId = null): ?Timetable
    {
        return self::create([
            'division_id' => $newDivisionId ?? $this->division_id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'room_id' => $this->room_id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'period_name' => $this->period_name,
            'room_number' => $this->room_number,
            'academic_year_id' => $newAcademicYearId,
            'is_break_time' => $this->is_break_time,
            'status' => self::STATUS_ACTIVE,
            'notes' => $this->notes,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Academic\Attendance;
use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\User;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

/**
 * Teacher Attendance Controller
 * 
 * Handles all attendance-related operations for teachers:
 * - Mark attendance for lectures
 * - View attendance history
 * - Generate attendance reports
 */
class AttendanceController extends Controller
{
    /**
     * Holiday service instance
     */
    protected HolidayService $holidayService;

    /**
     * Constructor - Apply middleware
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }
    /**
     * Display attendance dashboard for teacher
     * Shows today's lectures and quick actions
     */
    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $today = now()->format('Y-m-d');
        
        // Get teacher's divisions
        $divisions = Division::whereHas('timetables', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();
        
        // Get today's timetable for this teacher (both weekly and date-specific)
        $today = now()->format('Y-m-d');
        $todayDayOfWeek = strtolower(now()->format('l'));
        
        // Get weekly schedule (based on day_of_week)
        $weeklySchedule = Timetable::where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayDayOfWeek)
            ->whereNull('date')
            ->with(['division', 'subject', 'room'])
            ->orderBy('start_time')
            ->get();
        
        // Get today's date-specific schedule
        $dateSpecificSchedule = Timetable::where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->with(['division', 'subject', 'room'])
            ->orderBy('start_time')
            ->get();
        
        // Combine both schedules
        $todaySchedule = $dateSpecificSchedule->concat($weeklySchedule);
        
        // Check if attendance is already marked for each lecture
        foreach ($todaySchedule as $lecture) {
            $lecture->attendance_marked = Attendance::where('timetable_id', $lecture->id)
                ->whereDate('date', $today)
                ->exists();

            $lecture->attendance_count = Attendance::where('timetable_id', $lecture->id)
                ->whereDate('date', $today)
                ->count();
        }
        
        // Get recent attendance marked by teacher
        $recentAttendance = Attendance::where('marked_by', $teacher->id)
            ->with(['student', 'timetable.division'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('teacher.attendance.index', compact(
            'todaySchedule',
            'divisions',
            'recentAttendance'
        ));
    }

    /**
     * Show form to mark attendance for a specific lecture
     */
    public function create(int $timetableId): View|RedirectResponse
    {
        $teacher = Auth::user();
        $timetable = Timetable::with(['division.students', 'subject'])
            ->findOrFail($timetableId);

        // Verify teacher is assigned to this timetable
        if ($timetable->teacher_id != $teacher->id) {
            abort(403, 'You are not authorized to mark attendance for this lecture.');
        }

        // Get date from request or default to today
        $date = request()->query->get('date', now()->format('Y-m-d'));

        // Get all students in the division with pagination
        $students = $timetable->division->students()
            ->where('student_status', 'active')
            ->orderBy('roll_number')
            ->paginate(10);

        // Get existing attendance for this lecture and date
        $existingAttendance = Attendance::where('timetable_id', $timetableId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        // Get all dates with attendance for this timetable
        $attendanceDates = Attendance::where('timetable_id', $timetableId)
            ->select('date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');

        return view('teacher.attendance.mark', compact(
            'timetable',
            'students',
            'existingAttendance',
            'date',
            'attendanceDates'
        ));
    }

    /**
     * Store attendance records
     */
    public function store(Request $request, int $timetableId): RedirectResponse
    {
        $teacher = Auth::user();

        // Validate request
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late',
            'attendances.*.remarks' => 'nullable|string|max:255',
        ]);

        $timetable = Timetable::findOrFail($timetableId);

        // Verify teacher is assigned to this timetable
        if ($timetable->teacher_id != $teacher->id) {
            return back()->with('error', 'You are not authorized to mark attendance for this class. Please contact admin if you think this is an error.');
        }

        // Check if this is an update (attendance already exists for this date)
        $existingAttendance = Attendance::where('timetable_id', $timetableId)
            ->whereDate('date', $validated['date'])
            ->first();

        $isUpdate = $existingAttendance !== null;

        try {
            DB::beginTransaction();

            $updated = 0;
            $created = 0;

            foreach ($validated['attendances'] as $attendanceData) {
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'timetable_id' => $timetableId,
                        'date' => $validated['date'],
                    ],
                    [
                        'marked_by' => $teacher->id,
                        'status' => $attendanceData['status'],
                        'remarks' => $attendanceData['remarks'] ?? null,
                        'ip_address' => $request->ip(),
                    ]
                );
                
                // Track if this was an update (existing record) or new
                if ($attendance->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            DB::commit();

            // Build appropriate message
            if ($isUpdate) {
                $message = "✓ Updated attendance for {$updated} students in " . $timetable->division->division_name;
                if ($created > 0) {
                    $message .= " ({$created} new)";
                }
                \Log::info('Attendance updated: ' . $message);
            } else {
                $message = "✓ Attendance marked successfully for {$created} students in " . $timetable->division->division_name;
                \Log::info('Attendance created: ' . $message);
            }

            // Redirect with success message
            return redirect()->route('teacher.attendance.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Attendance save error: ' . $e->getMessage());
            return back()->with('error', 'Failed to mark attendance: ' . $e->getMessage());
        }
    }

    /**
     * Show attendance history for a division
     */
    public function history(Request $request): View
    {
        $teacher = Auth::user();

        // Get teacher's divisions
        $divisions = Division::whereHas('timetables', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();

        $divisionId = $request->get('division_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $perPage = $request->get('per_page', 20);

        $attendances = collect();
        $selectedDivision = null;

        if ($divisionId) {
            $selectedDivision = Division::findOrFail($divisionId);

            // Get attendance records for this division with pagination
            $attendances = Attendance::whereHas('timetable', function($q) use ($divisionId, $teacher) {
                    $q->where('division_id', $divisionId)
                      ->where('teacher_id', $teacher->id);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->with(['student', 'timetable.subject'])
                ->latest('date')
                ->paginate($perPage);
        }

        return view('teacher.attendance.history', compact(
            'divisions',
            'attendances',
            'selectedDivision',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Download attendance report as Excel
     */
    public function downloadExcel(Request $request)
    {
        $teacher = Auth::user();
        $divisionId = $request->get('division_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        if (!$divisionId) {
            return back()->with('error', 'Please select a division');
        }

        $selectedDivision = Division::findOrFail($divisionId);

        // Get attendance records
        $attendances = Attendance::whereHas('timetable', function($q) use ($divisionId, $teacher) {
                $q->where('division_id', $divisionId)
                  ->where('teacher_id', $teacher->id);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['student', 'timetable.subject'])
            ->latest('date')
            ->get();

        // Create Excel file
        $fileName = 'Attendance_Report_' . $selectedDivision->division_name . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new AttendanceExport($attendances, $selectedDivision, $startDate, $endDate), $fileName);
    }

    /**
     * Download attendance report as PDF
     */
    public function downloadPDF(Request $request)
    {
        $teacher = Auth::user();
        $divisionId = $request->get('division_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        if (!$divisionId) {
            return back()->with('error', 'Please select a division');
        }

        $selectedDivision = Division::findOrFail($divisionId);

        // Get attendance records
        $attendances = Attendance::whereHas('timetable', function($q) use ($divisionId, $teacher) {
                $q->where('division_id', $divisionId)
                  ->where('teacher_id', $teacher->id);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['student', 'timetable.subject'])
            ->latest('date')
            ->get();

        // Calculate statistics
        $totalRecords = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $attendancePercentage = $totalRecords > 0 ? round(($presentCount + $lateCount) / $totalRecords * 100, 2) : 0;

        $pdf = PDF::loadView('teacher.attendance.pdf-report', compact(
            'attendances',
            'selectedDivision',
            'startDate',
            'endDate',
            'totalRecords',
            'presentCount',
            'absentCount',
            'lateCount',
            'attendancePercentage'
        ));

        $fileName = 'Attendance_Report_' . $selectedDivision->division_name . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($fileName);
    }

    /**
     * Show attendance report with statistics
     */
    public function report(Request $request): View
    {
        $teacher = Auth::user();
        
        // Get teacher's divisions
        $divisions = Division::whereHas('timetables', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();
        
        $divisionId = $request->get('division_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $stats = null;
        $studentWiseStats = null;
        
        if ($divisionId) {
            $selectedDivision = Division::findOrFail($divisionId);
            
            // Get total lectures conducted
            $totalLectures = Timetable::where('division_id', $divisionId)
                ->where('teacher_id', $teacher->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            // Get attendance statistics
            $totalPresent = Attendance::whereHas('timetable', function($q) use ($divisionId, $teacher) {
                    $q->where('division_id', $divisionId)
                      ->where('teacher_id', $teacher->id);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count();
            
            $totalAbsent = Attendance::whereHas('timetable', function($q) use ($divisionId, $teacher) {
                    $q->where('division_id', $divisionId)
                      ->where('teacher_id', $teacher->id);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->count();
            
            $totalLate = Attendance::whereHas('timetable', function($q) use ($divisionId, $teacher) {
                    $q->where('division_id', $divisionId)
                      ->where('teacher_id', $teacher->id);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'late')
                ->count();
            
            $totalMarked = $totalPresent + $totalAbsent + $totalLate;
            
            $stats = [
                'total_lectures' => $totalLectures,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'total_late' => $totalLate,
                'total_marked' => $totalMarked,
                'present_percentage' => $totalMarked > 0 ? round(($totalPresent / $totalMarked) * 100, 2) : 0,
                'absent_percentage' => $totalMarked > 0 ? round(($totalAbsent / $totalMarked) * 100, 2) : 0,
                'late_percentage' => $totalMarked > 0 ? round(($totalLate / $totalMarked) * 100, 2) : 0,
            ];
            
            // Get student-wise attendance
            $studentWiseStats = User::select(
                    'users.id',
                    'users.name',
                    'users.email',
                    DB::raw('COUNT(CASE WHEN a.status = "present" THEN 1 END) as present_count'),
                    DB::raw('COUNT(CASE WHEN a.status = "absent" THEN 1 END) as absent_count'),
                    DB::raw('COUNT(CASE WHEN a.status = "late" THEN 1 END) as late_count'),
                    DB::raw('COUNT(a.id) as total')
                )
                ->join('students', 'users.id', '=', 'students.user_id')
                ->leftJoin('attendance as a', 'users.id', '=', 'a.student_id')
                ->leftJoin('timetables', 'a.timetable_id', '=', 'timetables.id')
                ->where('students.division_id', $divisionId)
                ->where(function($q) use ($teacher, $startDate, $endDate) {
                    $q->whereNull('timetables.teacher_id')
                      ->orWhere('timetables.teacher_id', $teacher->id);
                })
                ->whereBetween('a.date', [$startDate, $endDate])
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderBy('users.name')
                ->get();
        }
        
        return view('teacher.attendance.report', compact(
            'divisions',
            'stats',
            'studentWiseStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Edit existing attendance record
     */
    public function edit(int $attendanceId): View
    {
        $teacher = Auth::user();
        $attendance = Attendance::with(['student', 'timetable.division', 'timetable.subject'])
            ->findOrFail($attendanceId);
        
        // Verify teacher marked this attendance
        if ($attendance->marked_by != $teacher->id) {
            abort(403, 'You can only edit attendance that you marked.');
        }
        
        return view('teacher.attendance.edit', compact('attendance'));
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, int $attendanceId): RedirectResponse
    {
        $teacher = Auth::user();
        
        $validated = $request->validate([
            'status' => 'required|in:present,absent,late',
            'remarks' => 'nullable|string|max:255',
        ]);
        
        $attendance = Attendance::findOrFail($attendanceId);
        
        // Verify teacher marked this attendance
        if ($attendance->marked_by != $teacher->id) {
            abort(403, 'Unauthorized');
        }
        
        $attendance->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
        ]);
        
        return redirect()->route('teacher.attendance.history')
            ->with('success', 'Attendance updated successfully.');
    }
}

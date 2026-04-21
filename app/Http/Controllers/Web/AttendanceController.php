<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\MarkAttendanceRequest;
use App\Http\Requests\Attendance\AttendanceReportRequest;
use App\Models\Academic\Attendance;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User\Student;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Holiday service instance
     */
    protected HolidayService $holidayService;

    /**
     * Constructor
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    public function index()
    {
        $divisions = Division::where('is_active', true)
            ->with(['academicYear', 'session'])
            ->get();
        $sessions = AcademicSession::where('is_active', true)->get();

        // Check if today is a holiday
        $today = now()->format('Y-m-d');
        $todayHoliday = null;
        $isSunday = false;
        $hasTimetableToday = true;
        $academicYearId = \App\Models\Academic\AcademicYear::getCurrentAcademicYearId();
        
        // Check if today is Sunday
        if (now()->dayOfWeek === 0) {
            $isSunday = true;
        }
        
        if ($academicYearId) {
            $todayHoliday = \App\Models\Holiday::where('is_active', true)
                ->where('academic_year_id', $academicYearId)
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->first();
                
            // Check if there's any timetable entry for today
            $dayOfWeek = strtolower(now()->format('l'));
            $hasTimetableToday = \App\Models\Academic\Timetable::where('academic_year_id', $academicYearId)
                ->where(function($query) use ($dayOfWeek, $today) {
                    $query->where('day_of_week', $dayOfWeek)
                          ->orWhereDate('date', $today);
                })
                ->exists();
        }

        return view('academic.attendance.index', compact('divisions', 'sessions', 'todayHoliday', 'isSunday', 'hasTimetableToday'));
    }

    public function create()
    {
        $divisions = Division::where('is_active', true)
            ->with(['academicYear', 'session'])
            ->get();
        $sessions = AcademicSession::where('is_active', true)->get();

        return view('academic.attendance.create', compact('divisions', 'sessions'));
    }

    /**
     * Show mark attendance form with holiday validation
     */
    public function mark(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'date' => 'required|date'
        ]);

        // Check if the selected date is a Sunday (weekly off day)
        $selectedDate = Carbon::parse($validated['date']);
        if ($selectedDate->dayOfWeek === Carbon::SUNDAY) {
            return redirect()->route('academic.attendance.index')
                ->with('error', 'Sundays are weekly off days. Attendance cannot be marked on Sundays.');
        }
        
        $division = Division::with('academicYear')->findOrFail($validated['division_id']);
        
        // Check if the selected date is a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            $division->academic_year_id
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            $holidayName = $holidayCheck['holiday_title'] ?? 'Holiday';
            return redirect()->route('academic.attendance.index')
                ->with('error', "Today is a Holiday ({$holidayName}). Attendance cannot be marked on holidays.");
        }

        // Check if there's timetable for the selected date
        $dayOfWeek = strtolower($selectedDate->format('l'));
        $selectedDateStr = $validated['date'];
        $hasTimetable = \App\Models\Academic\Timetable::where('academic_year_id', $division->academic_year_id)
            ->where(function($query) use ($dayOfWeek, $selectedDateStr) {
                $query->where('day_of_week', $dayOfWeek)
                      ->orWhereDate('date', $selectedDateStr);
            })
            ->exists();
            
        if (!$hasTimetable) {
            return redirect()->route('academic.attendance.index')
                ->with('error', 'No timetable entry found for the selected date. Cannot mark attendance.');
        }

        // Get ALL students of that division
        $students = Student::where('division_id', $validated['division_id'])
                          ->where('student_status', 'active')
                          ->orderBy('roll_number')
                          ->get();

        // Check if attendance already exists
        $existingAttendance = Attendance::where('division_id', $validated['division_id'])
                                      ->whereDate('date', $validated['date'])
                                      ->pluck('status', 'student_id');

        // If attendance exists, redirect to edit page
        if ($existingAttendance->isNotEmpty()) {
            return redirect()->route('academic.attendance.edit', [
                'division_id' => $validated['division_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'date' => $validated['date']
            ]);
        }

        return view('academic.attendance.mark', compact('division', 'students', 'validated', 'existingAttendance', 'holidayCheck'));
    }

    /**
     * Edit attendance for a specific date and division
     */
    public function edit(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'date' => 'required|date'
        ]);

        // Check if the selected date is a Sunday (weekly off day)
        $selectedDate = Carbon::parse($validated['date']);
        if ($selectedDate->dayOfWeek === Carbon::SUNDAY) {
            return redirect()->route('academic.attendance.index')
                ->with('error', 'Sundays are weekly off days. Attendance cannot be edited on Sundays.');
        }
        
        $division = Division::with('academicYear')->findOrFail($validated['division_id']);
        
        // Check if the selected date is a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            $division->academic_year_id
        );

        // Get ALL students of that division
        $students = Student::where('division_id', $validated['division_id'])
                          ->where('student_status', 'active')
                          ->orderBy('roll_number')
                          ->get();

        // Get existing attendance
        $existingAttendance = Attendance::where('division_id', $validated['division_id'])
                                      ->whereDate('date', $validated['date'])
                                      ->pluck('status', 'student_id');

        return view('academic.attendance.edit', compact('division', 'students', 'validated', 'existingAttendance', 'holidayCheck'));
    }

    /**
     * Update attendance for a specific date and division
     */
    public function update(MarkAttendanceRequest $request)
    {
        $validated = $request->validated();
        
        // Validate that the date is not a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            Division::find($validated['division_id'])?->academic_year_id
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $holidayCheck['message'] . ': ' . ($holidayCheck['holiday_title'] ?? ''));
        }

        DB::transaction(function () use ($validated) {
            // Update existing attendance records
            foreach ($validated['students'] as $studentData) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentData['student_id'],
                        'division_id' => $validated['division_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'academic_session_id' => $validated['academic_session_id'],
                        'status' => $studentData['status']
                    ]
                );
            }
        });

        return redirect()->route('academic.attendance.index')
                         ->with('success', 'Attendance updated successfully.');
    }

    /**
     * Delete attendance for a specific date and division
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'date' => 'required|date'
        ]);

        $deleted = Attendance::where('division_id', $validated['division_id'])
                           ->whereDate('date', $validated['date'])
                           ->delete();

        if ($deleted) {
            return redirect()->route('academic.attendance.index')
                             ->with('success', 'Attendance deleted successfully.');
        }

        return redirect()->route('academic.attendance.index')
                         ->with('error', 'No attendance records found to delete.');
    }

    /**
     * Store attendance with holiday validation
     */
    public function store(MarkAttendanceRequest $request)
    {
        $validated = $request->validated();
        
        // Validate that the date is not a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            Division::find($validated['division_id'])?->academic_year_id
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $holidayCheck['message'] . ': ' . ($holidayCheck['holiday_title'] ?? ''));
        }

        DB::transaction(function () use ($validated) {
            // Use updateOrCreate for each student - ensures unique constraint
            foreach ($validated['students'] as $studentData) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentData['student_id'],
                        'division_id' => $validated['division_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'academic_session_id' => $validated['academic_session_id'],
                        'status' => $studentData['status'],
                        'marked_by' => auth()->id(),
                    ]
                );
            }
        });

        return redirect()->route('academic.attendance.index')
                         ->with('success', 'Attendance marked successfully.');
    }

    /**
     * AJAX endpoint to check if a date is a holiday
     */
    public function checkHoliday(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'division_id' => 'nullable|exists:divisions,id'
        ]);

        $date = Carbon::parse($request->date);
        $academicYearId = null;

        if ($request->filled('division_id')) {
            $academicYearId = Division::find($request->division_id)?->academic_year_id;
        }

        $holidayCheck = $this->holidayService->validateAttendanceDate($date, $academicYearId);

        return response()->json([
            'success' => true,
            'is_holiday' => $holidayCheck['is_holiday'],
            'valid' => $holidayCheck['valid'],
            'message' => $holidayCheck['message'],
            'holiday_title' => $holidayCheck['holiday_title'] ?? null,
            'holiday_type' => $holidayCheck['holiday_type'] ?? null,
        ]);
    }

    public function report(Request $request)
    {
        $divisions = Division::where('is_active', true)->get();

        $attendanceData = null;
        $holidayDates = [];
        
        if ($request->filled(['division_id', 'start_date', 'end_date'])) {
            $validated = $request->validate([
                'division_id' => 'required|exists:divisions,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            // Load attendance with student relationship
            $attendanceRecords = Attendance::with(['student.user'])
                ->where('division_id', $validated['division_id'])
                ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
                ->get();

            // Group by date
            $attendanceData = $attendanceRecords->groupBy('date');
            
            // Get holiday dates in the range for display
            $division = Division::find($validated['division_id']);
            $holidayDates = $this->holidayService->getHolidayDatesInRange(
                $validated['start_date'],
                $validated['end_date'],
                $division?->academic_year_id
            );
        }

        return view('academic.attendance.report', compact('divisions', 'attendanceData', 'holidayDates'));
    }

    /**
     * Get students by division for AJAX request
     */
    public function getStudentsByDivision(Division $division): JsonResponse
    {
        $students = Student::where('division_id', $division->id)
            ->where('student_status', 'active')
            ->orderBy('roll_number', 'asc')
            ->get()
            ->map(function ($student) {
                return [
                    'name' => $student->full_name,
                    'roll_no' => $student->roll_number
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Students fetched successfully',
            'data' => $students
        ]);
    }

    /**
     * Download attendance report as PDF
     */
    public function downloadReport(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $division = Division::with(['academicYear', 'program'])->findOrFail($request->division_id);
        
        $attendanceRecords = Attendance::with(['student', 'student.studentProfile', 'markedBy', 'division'])
            ->where('division_id', $request->division_id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get();

        $html = view('pdf.attendance-report', [
            'division' => $division,
            'attendanceRecords' => $attendanceRecords,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date
        ])->render();

        $pdf = \PDF::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('attendance-report-' . $division->division_name . '-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Download attendance report as Excel
     */
    public function downloadExcel(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $division = Division::findOrFail($request->division_id);
        
        $attendanceRecords = Attendance::with(['student', 'student.studentProfile', 'markedBy', 'division'])
            ->where('division_id', $request->division_id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get();

        $data = [];
        $data[] = ['Date', 'Division', 'Roll No', 'Student Name', 'DOB', 'Gender', 'Father Name', 'Father Phone', 'Mother Name', 'Guardian Name', 'Guardian Phone', 'Status', 'Teacher Name'];

        foreach ($attendanceRecords as $record) {
            $data[] = [
                $record->date,
                $record->division->division_name ?? 'N/A',
                $record->student->roll_number ?? 'N/A',
                $record->student->full_name ?? 'N/A',
                $record->student->date_of_birth ?? 'N/A',
                ucfirst($record->student->gender ?? 'N/A'),
                $record->student->studentProfile->father_name ?? 'N/A',
                $record->student->studentProfile->father_phone ?? 'N/A',
                $record->student->studentProfile->mother_name ?? 'N/A',
                $record->student->studentProfile->guardian_name ?? 'N/A',
                $record->student->studentProfile->guardian_phone ?? 'N/A',
                ucfirst($record->status),
                $record->markedBy->name ?? 'N/A'
            ];
        }

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 'attendance-report-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\User;
use App\Models\Academic\Department;
use App\Models\Academic\Division;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use App\Models\Academic\Timetable;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Attendance;
use App\Models\Fee\FeePayment;
use App\Models\Fee\StudentFee;
use App\Models\TeacherAssignment;
use App\Models\Result\Examination;
use App\Models\Result\StudentMark;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrincipalDashboardController extends Controller
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

    public function index(Request $request)
    {
        // Total Students (Active only)
        $totalStudents = Student::where('student_status', 'active')
            ->whereNull('deleted_at')
            ->count();

        // Total Teachers (Users with teacher role)
        $totalTeachers = User::role('teacher')->count();

        // Total Classes (Active Divisions)
        $totalDivisions = Division::where('is_active', true)->count();

        $totalPrograms = Program::where('is_active', true)->count();

        $totalDepartments = Department::count();

        $totalExaminations = Examination::count();

        $totalSubjects = Subject::count();

        // Today's Attendance
        $attendanceToday = Attendance::whereDate('date', Carbon::today())
            ->select(
                DB::raw('COUNT(CASE WHEN status = "present" THEN 1 END) as present'),
                DB::raw('COUNT(CASE WHEN status = "absent" THEN 1 END) as absent'),
                DB::raw('COUNT(*) as total')
            )
            ->first();

        $attendancePercentage = $attendanceToday && $attendanceToday->total > 0
            ? round(($attendanceToday->present / $attendanceToday->total) * 100, 2)
            : 0;

        $totalClasses = $totalDivisions; // Alias for blade compatibility
        $todayAttendance = $attendanceToday ? $attendanceToday->total : 0;

        // Get recently created users with passwords (for admin panel)
        $recentUsersWithPasswords = User::whereNotNull('temp_password')
            ->orderBy('password_generated_at', 'desc')
            ->limit(10)
            ->get();

        // Fee collection - Current month
        $feeCollection = FeePayment::whereMonth('created_at', Carbon::now())
            ->whereYear('created_at', Carbon::now())
            ->select(
                DB::raw('SUM(amount) as total_collected'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->first();

        // Pending fees (outstanding)
        $pendingFees = StudentFee::where('outstanding_amount', '>', 0)
            ->sum('outstanding_amount');

        // Timetable Management
        $divisions = Division::where('is_active', true)
            ->with(['program', 'timetables' => function($query) {
                $query->with(['subject', 'teacher', 'academicYear'])
                    ->orderBy('start_time');
            }])
            ->get();

        $selectedDivisionId = $request->input('division_id');
        $selectedDivision = $selectedDivisionId ? Division::with(['program', 'timetables' => function($query) {
            $query->with(['subject', 'teacher', 'academicYear'])
                ->orderBy('day_of_week')
                ->orderBy('start_time');
        }])->find($selectedDivisionId) : null;

        $timetables = $selectedDivision && $selectedDivision->timetables->isNotEmpty()
            ? $selectedDivision->timetables->groupBy('day_of_week')
            : collect();

        $timeSlots = TimeSlot::orderBy('start_time')->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $days = $this->getDaysArray();

        // Recent Activities (last 5)
        $recentActivities = $this->getRecentActivities();

        return view('dashboard.principal', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalDivisions',
            'totalPrograms',
            'totalSubjects',
            'totalDepartments',
            'totalExaminations',
            'attendanceToday',
            'todayAttendance',
            'attendancePercentage',
            'feeCollection',
            'pendingFees',
            'recentActivities',
            'divisions',
            'selectedDivision',
            'timetables',
            'timeSlots',
            'subjects',
            'teachers',
            'academicYears',
            'days',
            'recentUsersWithPasswords'
        ));
    }

    /**
     * Get days array for timetable
     */
    private function getDaysArray(): array
    {
        return [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
        ];
    }

    /**
     * Store timetable entry from dashboard
     */
    public function storeTimetable(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_number' => 'nullable|string|max:50',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        // Check if the selected date is a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            $validated['academic_year_id']
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'Cannot create timetable on holiday: ' . ($holidayCheck['holiday_title'] ?? 'Holiday'));
        }

        // Check for overlapping time slots for the same division and date
        if (Timetable::checkDateDivisionConflict(
            $validated['division_id'],
            $validated['date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'A class already exists for this division and date at this time slot.');
        }

        // Check for teacher double-booking
        if (Timetable::checkTeacherDateConflict(
            $validated['teacher_id'],
            $validated['date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'This teacher is already booked for another class at this time on this date.');
        }

        Timetable::create([
            'division_id' => $validated['division_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'day_of_week' => $validated['day_of_week'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room_number' => $validated['room_number'] ?? null,
            'academic_year_id' => $validated['academic_year_id'],
        ]);

        return redirect()->route('dashboard.principal')
            ->with('success', 'Timetable entry added successfully!');
    }

    /**
     * Delete timetable entry from dashboard
     */
    public function deleteTimetable($timetableId)
    {
        try {
            $timetable = Timetable::findOrFail($timetableId);
            $timetable->delete();

            return redirect()->route('dashboard.principal')
                ->with('success', 'Timetable entry deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'Failed to delete timetable entry: ' . $e->getMessage());
        }
    }

    /**
     * Update timetable entry
     */
    public function updateTimetable(Request $request, $id)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_number' => 'nullable|string|max:50',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        // Check if the selected date is a holiday
        $holidayCheck = $this->holidayService->validateAttendanceDate(
            $validated['date'],
            $validated['academic_year_id']
        );

        if (!$holidayCheck['valid'] && $holidayCheck['is_holiday']) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'Cannot update timetable on holiday: ' . ($holidayCheck['holiday_title'] ?? 'Holiday'));
        }

        // Check for overlapping time slots (excluding current entry)
        if (Timetable::checkDateDivisionConflict(
            $validated['division_id'],
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $id
        )) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'A class already exists for this division and date at this time slot.');
        }

        // Check for teacher double-booking (excluding current entry)
        if (Timetable::checkTeacherDateConflict(
            $validated['teacher_id'],
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $id
        )) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'This teacher is already booked for another class at this time on this date.');
        }

        $timetable = Timetable::findOrFail($id);
        $timetable->update([
            'division_id' => $validated['division_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'day_of_week' => $validated['day_of_week'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room_number' => $validated['room_number'] ?? null,
            'academic_year_id' => $validated['academic_year_id'],
        ]);

        return redirect()->route('dashboard.principal')
            ->with('success', 'Timetable entry updated successfully!');
    }

    /**
     * Display timetable in table format for Principal Dashboard
     */
    public function timetableIndex(Request $request)
    {
        $query = Timetable::with(['division', 'subject', 'teacher', 'academicYear'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time');

        // Apply filters
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $timetables = $query->paginate(20);
        $divisions = Division::where('is_active', true)->with('program')->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('dashboard.principal-timetable', compact(
            'timetables',
            'divisions',
            'subjects',
            'teachers',
            'academicYears'
        ));
    }

    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent fee payments (last 5)
        $recentPayments = FeePayment::with(['studentFee.student'])
            ->latest()
            ->limit(3)
            ->get();

        foreach ($recentPayments as $payment) {
            if ($payment->studentFee && $payment->studentFee->student) {
                $student = $payment->studentFee->student;
                $activities[] = [
                    'icon' => 'bi-cash-coin text-success',
                    'title' => 'Fee Payment Received',
                    'description' => $student->first_name . ' ' . $student->last_name . ' paid ₹' . number_format($payment->amount, 2),
                    'time' => $payment->created_at->diffForHumans()
                ];
            }
        }

        // Recent admissions (last 2)
        $recentAdmissions = Student::with(['division', 'program'])
            ->latest()
            ->limit(2)
            ->get();

        foreach ($recentAdmissions as $student) {
            $activities[] = [
                'icon' => 'bi-person-plus-fill text-primary',
                'title' => 'New Student Admission',
                'description' => $student->first_name . ' ' . $student->last_name . ' admitted to ' . ($student->division->division_name ?? 'N/A'),
                'time' => $student->created_at->diffForHumans()
            ];
        }

        // Sort by time and return top 5
        usort($activities, function ($a, $b) {
            return 0; // Already sorted
        });

        return array_slice($activities, 0, 5);
    }

    /**
     * Assign division to teacher
     */
    public function assignDivision(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'division_id' => 'required|exists:divisions,id',
            'assignment_type' => 'required|in:division,subject',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            // Check if assignment already exists
            $existingAssignment = TeacherAssignment::where('teacher_id', $validated['teacher_id'])
                ->where('division_id', $validated['division_id'])
                ->where('assignment_type', $validated['assignment_type'])
                ->first();

            if ($existingAssignment) {
                // Update existing assignment
                $existingAssignment->update([
                    'is_active' => $validated['is_active'],
                    'notes' => $validated['notes'] ?? null
                ]);

                return redirect()->route('dashboard.principal')
                    ->with('success', 'Division assignment updated successfully!');
            }

            // Create new assignment
            TeacherAssignment::create([
                'teacher_id' => $validated['teacher_id'],
                'division_id' => $validated['division_id'],
                'assignment_type' => $validated['assignment_type'],
                'is_active' => $validated['is_active'],
                'notes' => $validated['notes'] ?? null
            ]);

            return redirect()->route('dashboard.principal')
                ->with('success', 'Division assigned to teacher successfully!');

        } catch (\Exception $e) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'Failed to assign division: ' . $e->getMessage());
        }
    }

    /**
     * Remove division assignment
     */
    public function removeAssignment($assignmentId)
    {
        try {
            $assignment = TeacherAssignment::findOrFail($assignmentId);
            $assignment->delete();

            return redirect()->route('dashboard.principal')
                ->with('success', 'Assignment removed successfully!');

        } catch (\Exception $e) {
            return redirect()->route('dashboard.principal')
                ->with('error', 'Failed to remove assignment: ' . $e->getMessage());
        }
    }

    /**
     * View all student results (Admin/Principal only)
     */
    public function results(Request $request)
    {
        // Get all divisions
        $divisions = Division::where('is_active', true)
            ->with(['program', 'session'])
            ->get();
        
        // Get all active examinations
        $examinations = \App\Models\Result\Examination::active()->get();
        
        $selectedDivision = null;
        $selectedExam = null;
        $results = collect();
        $students = collect();
        
        if ($request->filled('division_id') && $request->filled('examination_id')) {
            $selectedDivision = Division::find($request->division_id);
            $selectedExam = Examination::find($request->examination_id);
            
            // Get students in the division
            $students = Student::where('division_id', $request->division_id)
                ->where('student_status', 'active')
                ->orderBy('roll_number')
                ->paginate(20);
            
            // Get ALL marks for the division (not just paginated students)
            // This ensures marks are available for all students regardless of page
            $results = StudentMark::where('examination_id', $request->examination_id)
                ->whereIn('student_id', Student::where('division_id', $request->division_id)
                    ->where('student_status', 'active')
                    ->pluck('id'))
                ->with(['subject'])
                ->get();
        }
        
        return view('principal.results.index', compact(
            'divisions', 
            'examinations', 
            'selectedDivision', 
            'selectedExam',
            'results',
            'students'
        ));
    }
}

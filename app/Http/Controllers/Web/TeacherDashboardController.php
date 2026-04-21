<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Student;
use App\Models\Academic\Attendance;
use App\Models\Academic\Timetable;
use App\Models\Academic\AcademicSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();
        $assignedDivision = $teacher->assignedDivision;
        
        // Dashboard Statistics
        $totalStudents = $assignedDivision ? 
            Student::where('division_id', $assignedDivision->id)
                   ->where('student_status', 'active')
                   ->count() : 0;
        
        $totalSubjects = Timetable::where('teacher_id', $teacher->id)
                                 ->distinct('subject_id')
                                 ->count();
        
        $todayAttendance = $assignedDivision ?
            Attendance::where('division_id', $assignedDivision->id)
                     ->whereDate('date', today())
                     ->count() : 0;
        
        $activeSession = AcademicSession::where('is_active', true)->first();
        
        // Recent Students (if class teacher)
        $recentStudents = $assignedDivision ? 
            Student::where('division_id', $assignedDivision->id)
                   ->where('student_status', 'active')
                   ->with(['division'])
                   ->latest()
                   ->limit(5)
                   ->get() : collect();
        
        // Teacher's Timetable Today (both weekly and date-specific)
        $today = now()->format('Y-m-d');
        $todayDayOfWeek = strtolower(Carbon::today()->format('l'));
        
        // Get weekly schedule
        $weeklySchedule = Timetable::where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayDayOfWeek)
            ->whereNull('date')
            ->with(['subject', 'division'])
            ->orderBy('start_time')
            ->get();
        
        // Get today's date-specific schedule
        $dateSpecificSchedule = Timetable::where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->with(['subject', 'division'])
            ->orderBy('start_time')
            ->get();
        
        // Combine both
        $todaySchedule = $dateSpecificSchedule->concat($weeklySchedule);
        
        return view('teacher.dashboard', compact(
            'teacher',
            'assignedDivision',
            'totalStudents',
            'totalSubjects',
            'todayAttendance',
            'activeSession',
            'recentStudents',
            'todaySchedule'
        ));
    }
    
    public function students(Request $request)
    {
        $teacher = auth()->user();
        $assignedDivision = $teacher->assignedDivision;
        
        if (!$assignedDivision) {
            return redirect()->route('teacher.dashboard')
                           ->with('error', 'You are not assigned as a class teacher.');
        }
        
        $query = Student::where('division_id', $assignedDivision->id)
                       ->where('student_status', 'active')
                       ->with(['division']);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('roll_number', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $students = $query->paginate(15);
        
        return view('teacher.students', compact('students', 'assignedDivision'));
    }
    
    public function attendance(Request $request)
    {
        $teacher = auth()->user();
        $assignedDivision = $teacher->assignedDivision;
        
        if (!$assignedDivision) {
            return redirect()->route('teacher.dashboard')
                           ->with('error', 'You are not assigned as a class teacher.');
        }
        
        $date = $request->get('date', today()->format('Y-m-d'));

        $attendanceData = Attendance::where('division_id', $assignedDivision->id)
                                   ->whereDate('date', $date)
                                   ->with('student')
                                   ->get();
        
        $summary = [
            'total' => $attendanceData->count(),
            'present' => $attendanceData->where('status', 'Present')->count(),
            'absent' => $attendanceData->where('status', 'Absent')->count(),
        ];
        
        return view('teacher.attendance', compact('attendanceData', 'assignedDivision', 'date', 'summary'));
    }
}
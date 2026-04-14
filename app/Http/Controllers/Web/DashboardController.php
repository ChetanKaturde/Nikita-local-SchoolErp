<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\User;
use App\Models\Academic\Division;
use App\Models\Fee\FeePayment;
use App\Models\Fee\StudentFee;
use App\Models\Library\Book;
use App\Models\Library\BookIssue;
use App\Models\Academic\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function principal()
    {
        return view('dashboard.principal');
    }

    public function student()
    {
        // Get authenticated student
        $student = Auth::guard('student')->user();
        
        // Check if student is authenticated
        if (!$student) {
            return redirect()->route('login')->with('error', 'Please login as student');
        }
        
        // Get attendance statistics
        $totalAttendance = Attendance::where('student_id', $student->id)->count();
        $presentAttendance = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 1) : 0;
        
        // Get fee statistics
        $studentFees = StudentFee::where('student_id', $student->id)->get();
        $totalFees = $studentFees->sum('total_amount');
        $totalPaid = $studentFees->sum('paid_amount');
        $totalPending = $totalFees - $totalPaid;
        
        return view('dashboard.student', compact(
            'student',
            'attendancePercentage',
            'totalFees',
            'totalPaid',
            'totalPending'
        ));
    }

    public function teacher()
    {
        $teacher = Auth::user();
        
        // Check if user is authenticated
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Please login');
        }
        
        // Get teacher's assigned division
        $assignedDivision = $teacher->assignedDivision;
        
        // Get student count
        $totalStudents = $assignedDivision ? 
            Student::where('division_id', $assignedDivision->id)
                   ->where('student_status', 'active')
                   ->count() : 0;
        
        return view('dashboard.teacher', compact('teacher', 'assignedDivision', 'totalStudents'));
    }

    public function office()
    {
        return view('dashboard.office');
    }

    public function accounts_staff()
    {
        // Get fee statistics from database
        $totalFeesCollected = FeePayment::sum('amount');
        $monthlyCollection = FeePayment::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->sum('amount');
        $pendingFees = StudentFee::whereRaw('paid_amount < total_amount')->count();
        $totalStudents = Student::where('student_status', 'active')->count();
        
        return view('dashboard.accounts', compact(
            'totalFeesCollected',
            'monthlyCollection',
            'pendingFees',
            'totalStudents'
        ));
    }

    public function librarian()
    {
        // Get book statistics from database
        $totalBooks = Book::count();
        $issuedBooks = BookIssue::where('status', 'issued')->count();
        $availableBooks = $totalBooks - $issuedBooks;
        $overdueBooks = BookIssue::where('status', 'issued')
                                ->where('due_date', '<', now()->toDateString())
                                ->count();
        
        // Get total students
        $totalStudents = Student::where('student_status', 'active')->count();
        
        // Get recent book issues
        $recentIssues = BookIssue::with(['book', 'student'])
                               ->latest()
                               ->limit(10)
                               ->get();
        
        // Get available books
        $availableBookList = Book::where('available_copies', '>', 0)
                               ->latest()
                               ->limit(10)
                               ->get();
        
        return view('dashboard.librarian', compact(
            'totalBooks',
            'issuedBooks',
            'availableBooks',
            'overdueBooks',
            'recentIssues',
            'availableBookList',
            'totalStudents'
        ));
    }

    public function accountant()
    {
        // Fetch recent fee payments for the accountant dashboard
        $recentPayments = \App\Models\Fee\FeePayment::with(['student', 'student.division', 'student.program'])
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $todayCollection = \App\Models\Fee\FeePayment::where('status', 'completed')
            ->whereDate('payment_date', today())
            ->sum('amount');

        $todayCount = \App\Models\Fee\FeePayment::where('status', 'completed')
            ->whereDate('payment_date', today())
            ->count();

        $monthlyReceipts = \App\Models\Fee\FeePayment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->count();

        // Get pending scholarship applications count
        $pendingScholarships = \App\Models\Fee\ScholarshipApplication::where('status', 'pending')->count();

        // Outstanding fees statistics
        $totalOutstanding = \App\Models\Fee\StudentFee::where('outstanding_amount', '>', 0)->sum('outstanding_amount');
        $outstandingStudentCount = \App\Models\Fee\StudentFee::where('outstanding_amount', '>', 0)
            ->distinct('student_id')
            ->count('student_id');

        // Recent remaining fees with student details
        $remainingFees = \App\Models\Fee\StudentFee::with(['student', 'student.program', 'student.division', 'feeStructure.feeHead'])
            ->where('outstanding_amount', '>', 0)
            ->orderBy('outstanding_amount', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.accountant', compact(
            'recentPayments',
            'todayCollection',
            'todayCount',
            'monthlyReceipts',
            'pendingScholarships',
            'totalOutstanding',
            'outstandingStudentCount',
            'remainingFees'
        ));
    }

    /**
     * Show accountant profile page.
     */
    public function accountantProfile()
    {
        $user = Auth::user();
        return view('dashboard.accountant-profile', compact('user'));
    }

    /**
     * Handle accountant password change.
     */
    public function accountantChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = \Hash::make($request->password);
        $user->temp_password = $request->password; // Update plain text password for admin view
        $user->password_generated_at = now();
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    public function hod_commerce()
    {
        return view('dashboard.hod_commerce');
    }

}
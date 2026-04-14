<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use App\Models\Academic\Timetable;
use App\Models\Academic\Attendance;
use App\Models\Result\StudentMark;
use App\Models\Result\Examination;
use App\Models\Fee\StudentFee;
use App\Models\StudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Constructor - Apply student auth middleware
     */
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    /**
     * Display student dashboard
     */
    public function index()
    {
        $student = Auth::guard('student')->user();

        // Get today's timetable
        $today = strtolower(Carbon::now()->format('l'));
        
        // Get the current academic year from the student's division
        $academicYearId = null;
        if ($student->division && $student->division->academic_year_id) {
            $academicYearId = $student->division->academic_year_id;
        }
        
        // Build the query for today's classes
        $todayQuery = Timetable::where('division_id', $student->division_id)
            ->where('day_of_week', $today)
            ->with(['subject', 'teacher']);
        
        // Filter by academic year if available
        if ($academicYearId) {
            $todayQuery->where('academic_year_id', $academicYearId);
        }
        
        // Filter only active timetables
        $todayQuery->where('is_active', true)
                   ->whereNotIn('status', ['cancelled', 'closed']);
        
        $todayClasses = $todayQuery->orderBy('start_time')->get();

        // Get attendance summary
        $attendanceSummary = $this->getAttendanceSummary($student);

        // Get recent notifications
        $notifications = $student->notifications()
            ->latest()
            ->take(5)
            ->get();

        // Get upcoming classes (next 7 days)
        $upcomingClasses = $this->getUpcomingClasses($student);

        // Get recent results/marks
        $recentResults = StudentMark::where('student_id', $student->id)
            ->with(['examination', 'subject'])
            ->latest()
            ->limit(5)
            ->get();

        // Get upcoming exams - exams for student's program
        $upcomingExams = Examination::where('end_date', '>=', now())
            ->whereHas('subject', function($query) use ($student) {
                $query->where('program_id', $student->program_id);
            })
            ->with(['subject'])
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Get fee summary
        $totalFees = StudentFee::where('student_id', $student->id)->sum('total_amount');
        $totalPaid = StudentFee::where('student_id', $student->id)->sum('paid_amount');
        $totalOutstanding = $totalFees - $totalPaid;

        return view('student.dashboard', compact(
            'student',
            'todayClasses',
            'attendanceSummary',
            'notifications',
            'upcomingClasses',
            'recentResults',
            'upcomingExams',
            'totalFees',
            'totalPaid',
            'totalOutstanding'
        ));
    }

    /**
     * Display student profile
     */
    public function profile()
    {
        $student = Auth::guard('student')->user();
        
        // Calculate attendance percentage
        $totalLectures = $student->attendances()->count();
        $presentDays = $student->attendances()->where('status', 'present')->count();
        $attendancePercentage = $totalLectures > 0 ? round(($presentDays / $totalLectures) * 100, 2) : 0;

        return view('student.profile.index', compact('student', 'attendancePercentage'));
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $student = Auth::guard('student')->user();

        return view('student.profile.edit', compact('student'));
    }

    /**
     * Update student profile
     */
    public function updateProfile(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('student-photos', 'public');
            $student->photo = $path;
        }

        $student->update([
            'name' => $validated['name'],
            'contact_no' => $validated['contact_no'] ?? $student->contact_no,
        ]);

        return redirect()->route('student.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('student.profile.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        // Get the associated user for password verification
        $user = $student->user;
        
        if (!$user) {
            return back()->withErrors([
                'current_password' => 'User account not found.',
            ]);
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify current password using the user model
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ])->withInput();
        }

        // Update password on the user model
        $user->update([
            'password' => Hash::make($validated['password']),
            'temp_password' => $validated['password'], // Update for admin view
            'password_generated_at' => now(),
        ]);

        return redirect()->route('student.profile')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Display student timetable
     */
    public function timetable()
    {
        $student = Auth::guard('student')->user();

        // Get the current academic year from the student's division
        // Division has academic_year_id which links to academic_years table
        $academicYearId = null;
        if ($student->division && $student->division->academic_year_id) {
            $academicYearId = $student->division->academic_year_id;
        }

        // Build the query - filter by division, academic year, status, and active
        $query = Timetable::where('division_id', $student->division_id)
            ->with(['subject', 'teacher']);

        // Filter by academic year if available
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        // Filter only active timetables (not cancelled, not closed)
        $query->where('is_active', true)
              ->whereNotIn('status', ['cancelled', 'closed']);

        $timetable = $query->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $timeSlots = $this->getTimeSlots();

        return view('student.timetable.index', compact('timetable', 'days', 'timeSlots'));
    }

    /**
     * Display student attendance
     */
    public function attendance()
    {
        $student = Auth::guard('student')->user();

        $attendanceBySubject = $student->getAttendancePercentageBySubject();

        // Overall attendance
        $totalLectures = $student->attendances()->count();
        $presentDays = $student->attendances()->where('status', 'present')->count();
        $absentDays = $student->attendances()->where('status', 'absent')->count();
        $lateDays = $student->attendances()->where('status', 'late')->count();
        $overallPercentage = $totalLectures > 0 ? round(($presentDays / $totalLectures) * 100, 2) : 0;

        // Recent attendance records
        $recentAttendance = $student->attendances()
            ->with(['timetable.subject'])
            ->latest('date')
            ->take(10)
            ->get();

        return view('student.attendance.index', compact(
            'attendanceBySubject',
            'totalLectures',
            'presentDays',
            'absentDays',
            'lateDays',
            'overallPercentage',
            'recentAttendance'
        ));
    }

    /**
     * Display notifications
     */
    public function notifications()
    {
        $student = Auth::guard('student')->user();

        $notifications = $student->notifications()
            ->latest()
            ->paginate(20);

        return view('student.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $student = Auth::guard('student')->user();

        $notification = $student->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $student = Auth::guard('student')->user();

        $student->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get attendance summary
     */
    private function getAttendanceSummary($student)
    {
        $totalLectures = $student->attendances()
            ->where('date', '>=', Carbon::now()->startOfMonth())
            ->count();

        $presentDays = $student->attendances()
            ->where('date', '>=', Carbon::now()->startOfMonth())
            ->where('status', 'present')
            ->count();

        $percentage = $totalLectures > 0 ? round(($presentDays / $totalLectures) * 100, 2) : 0;

        return [
            'total' => $totalLectures,
            'present' => $presentDays,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get upcoming classes
     */
    private function getUpcomingClasses($student)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $today = strtolower(Carbon::now()->format('l'));
        $todayIndex = array_search($today, $days);
        
        // Get the current academic year from the student's division
        $academicYearId = null;
        if ($student->division && $student->division->academic_year_id) {
            $academicYearId = $student->division->academic_year_id;
        }

        $upcomingClasses = collect();

        // Get classes for next 3 days
        for ($i = 0; $i < 3; $i++) {
            $dayIndex = ($todayIndex + $i) % count($days);
            $day = $days[$dayIndex];

            $classesQuery = Timetable::where('division_id', $student->division_id)
                ->where('day_of_week', $day)
                ->with(['subject', 'teacher']);
            
            // Filter by academic year if available
            if ($academicYearId) {
                $classesQuery->where('academic_year_id', $academicYearId);
            }
            
            // Filter only active timetables
            $classesQuery->where('is_active', true)
                         ->whereNotIn('status', ['cancelled', 'closed']);
            
            $classes = $classesQuery->orderBy('start_time')->get();

            if ($classes->isNotEmpty()) {
                $upcomingClasses->push([
                    'day' => ucfirst($day),
                    'date' => Carbon::now()->addDays($i)->format('M d'),
                    'classes' => $classes,
                ]);
            }
        }

        return $upcomingClasses;
    }

    /**
     * Display student fees
     */
    public function fees()
    {
        $student = Auth::guard('student')->user();
        
        // Get fee structures for the student's program (if program exists)
        $feeStructures = collect();
        if ($student->program_id) {
            $feeStructures = \App\Models\Fee\FeeStructure::where('program_id', $student->program_id)
                ->where('is_active', true)
                ->with(['feeHead'])
                ->get();
        }
        
        // Get fee records for the student - these are individual fee assignments
        $feeRecords = \App\Models\Fee\StudentFee::where('student_id', $student->id)
            ->with(['feeStructure.feeHead'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate totals using final_amount (after discounts)
        $totalFees = $feeRecords->sum('final_amount');
        $totalPaid = $feeRecords->sum('paid_amount');
        $totalOutstanding = $feeRecords->sum('outstanding_amount');
        
        return view('student.fees.index', compact(
            'student',
            'feeRecords',
            'feeStructures',
            'totalFees',
            'totalPaid',
            'totalOutstanding'
        ));
    }

    /**
     * Display student results
     */
    public function results()
    {
        $student = Auth::guard('student')->user();
        
        // Get exam results for the student
        $results = \App\Models\Result\StudentMark::where('student_id', $student->id)
            ->with(['subject', 'examination'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.results.index', compact('student', 'results'));
    }

    /**
     * Display student library
     */
    public function library()
    {
        $student = Auth::guard('student')->user();

        // Get issued books for the student
        $issuedBooks = \App\Models\Library\BookIssue::where('student_id', $student->id)
            ->with(['book'])
            ->orderBy('issue_date', 'desc')
            ->paginate(10);

        return view('student.library.index', compact('student', 'issuedBooks'));
    }

    /**
     * Display fee payment page
     */
    public function feesPayment($studentFee)
    {
        $student = Auth::guard('student')->user();
        
        $fee = \App\Models\Fee\StudentFee::where('id', $studentFee)
            ->where('student_id', $student->id)
            ->with(['feeStructure'])
            ->firstOrFail();
        
        return view('student.fees.payment', compact('student', 'fee'));
    }

    /**
     * Process offline payment (when Razorpay credentials are not configured)
     */
    public function processPayment(Request $request)
    {
        // Debug: Check if student is authenticated
        if (!Auth::guard('student')->check()) {
            // Redirect to student login page
            return redirect()->route('student.login')->with('error', 'Please login to continue');
        }
        
        $student = Auth::guard('student')->user();
        
        $request->validate([
            'student_fee_id' => 'required|exists:student_fees,id',
            'installments' => 'required|array|min:1',
            'amount' => 'required|numeric|min:1'
        ]);
        
        $studentFee = \App\Models\Fee\StudentFee::with(['feeStructure'])
            ->where('id', $request->student_fee_id)
            ->where('student_id', $student->id)
            ->firstOrFail();
        
        // Calculate single installment amount
        $totalInstallments = $studentFee->feeStructure->installments ?? 1;
        $singleInstallmentAmount = $studentFee->final_amount / $totalInstallments;
        
        // Validate selected installments
        $selectedInstallments = $request->installments;
        $expectedAmount = 0;
        foreach ($selectedInstallments as $installmentNum) {
            if ($installmentNum < 1 || $installmentNum > $totalInstallments) {
                return redirect()->back()->with('error', 'Invalid installment number selected.');
            }
            $expectedAmount += $singleInstallmentAmount;
        }
        
        // Check if amount matches expected (within small tolerance for rounding)
        if (abs($request->amount - $expectedAmount) > 1) {
            return redirect()->back()->with('error', 'Amount does not match selected installment(s).');
        }
        
        // Get existing payments
        $existingPayments = \App\Models\Fee\FeePayment::where('student_fee_id', $studentFee->id)
            ->where('status', 'success')
            ->get();
        $paidInstallments = $existingPayments->pluck('installment_number')->toArray();
        
        // Check if any selected installment is already fully paid
        foreach ($selectedInstallments as $installmentNum) {
            if (in_array($installmentNum, $paidInstallments)) {
                return redirect()->back()->with('error', 'Installment ' . $installmentNum . ' is already fully paid.');
            }
        }
        
        // Process each selected installment
        $totalPaid = 0;
        $receiptNumbers = [];
        
        foreach ($selectedInstallments as $installmentNum) {
            // Check for partial payment
            $partialPayment = $existingPayments->where('installment_number', $installmentNum)->first();
            
            if ($partialPayment) {
                // Add remaining amount for this installment
                $remainingAmount = $singleInstallmentAmount - $partialPayment->amount;
                $payAmount = min($remainingAmount, $singleInstallmentAmount);
            } else {
                $payAmount = $singleInstallmentAmount;
            }
            
            // Create payment record
            $receiptNumber = 'RCP' . date('Y') . strtoupper(str_pad(\App\Models\Fee\FeePayment::max('id') + 1, 6, '0', STR_PAD_LEFT));
            
            $payment = \App\Models\Fee\FeePayment::create([
                'student_fee_id' => $studentFee->id,
                'installment_number' => $installmentNum,
                'receipt_number' => $receiptNumber,
                'amount' => $payAmount,
                'payment_mode' => 'cash',
                'transaction_id' => 'CASH-' . date('Ymd') . '-' . uniqid(),
                'payment_date' => now(),
                'due_date' => now(),
                'status' => 'success',
                'remarks' => $request->filled('remarks') ? $request->remarks : 'Offline payment - Direct submission'
            ]);
            
            $receiptNumbers[] = $payment->id;
            $totalPaid += $payAmount;
        }
        
        // Update student fee
        $studentFee->paid_amount += $totalPaid;
        $studentFee->outstanding_amount = max($studentFee->final_amount - $studentFee->paid_amount, 0);
        
        // Check if all installments are paid
        $totalPaidInstallments = \App\Models\Fee\FeePayment::where('student_fee_id', $studentFee->id)
            ->where('status', 'success')
            ->count();
        
        if ($totalPaidInstallments >= $totalInstallments) {
            $studentFee->status = 'paid';
        } else {
            $studentFee->status = 'partial';
        }
        
        $studentFee->save();
        
        // Redirect back to fees page with success message
        return redirect()->route('student.fees')
            ->with('success', 'Payment submitted successfully! Razorpay credentials not found, submitting form directly.');
    }

    /**
     * Get time slots
     */
     private function getTimeSlots()
     {
         return \App\Models\Academic\TimeSlot::orderBy('start_time')->get();
     }
}

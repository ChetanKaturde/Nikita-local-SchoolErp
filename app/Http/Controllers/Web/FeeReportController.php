<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Fee\FeePayment;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeeStructure;
use App\Models\Fee\FeeHead;
use App\Models\Academic\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeReportController extends Controller
{
    /**
     * Display fee reports page for accountant.
     */
    public function index(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Program filter
        $programs = Program::active()->get();
        $programId = $request->input('program_id');

        // Fee collection summary
        $totalCollected = FeePayment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->when($programId, function ($q) use ($programId) {
                $q->whereHas('studentFee.student', function ($sq) use ($programId) {
                    $sq->where('program_id', $programId);
                });
            })
            ->sum('amount');

        $totalOutstanding = StudentFee::where('outstanding_amount', '>', 0)->sum('outstanding_amount');

        $totalPayments = FeePayment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->count();

        // Collection by payment mode
        $collectionByMode = FeePayment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->select('payment_mode', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_mode')
            ->get();

        // Collection by fee head
        $collectionByFeeHead = FeePayment::where('fee_payments.status', 'completed')
            ->whereBetween('fee_payments.payment_date', [$startDate, $endDate])
            ->join('student_fees', 'fee_payments.student_fee_id', '=', 'student_fees.id')
            ->join('fee_structures', 'student_fees.fee_structure_id', '=', 'fee_structures.id')
            ->join('fee_heads', 'fee_structures.fee_head_id', '=', 'fee_heads.id')
            ->select('fee_heads.name', DB::raw('SUM(fee_payments.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('fee_heads.id', 'fee_heads.name')
            ->orderByDesc('total')
            ->get();

        // Collection by program
        $collectionByProgram = FeePayment::where('fee_payments.status', 'completed')
            ->whereBetween('fee_payments.payment_date', [$startDate, $endDate])
            ->join('student_fees', 'fee_payments.student_fee_id', '=', 'student_fees.id')
            ->join('students', 'student_fees.student_id', '=', 'students.id')
            ->join('standards', 'students.program_id', '=', 'standards.id')
            ->select('standards.name', DB::raw('SUM(fee_payments.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('standards.id', 'standards.name')
            ->orderByDesc('total')
            ->get();

        // Top outstanding students
        $topOutstanding = StudentFee::with(['student', 'student.program', 'feeStructure.feeHead'])
            ->where('outstanding_amount', '>', 0)
            ->orderByDesc('outstanding_amount')
            ->limit(20)
            ->get();

        // Recent transactions
        $recentTransactions = FeePayment::with(['student', 'student.program'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderByDesc('payment_date')
            ->limit(20)
            ->get();

        return view('fees.reports.index', compact(
            'startDate',
            'endDate',
            'programs',
            'programId',
            'totalCollected',
            'totalOutstanding',
            'totalPayments',
            'collectionByMode',
            'collectionByFeeHead',
            'collectionByProgram',
            'topOutstanding',
            'recentTransactions'
        ));
    }
}

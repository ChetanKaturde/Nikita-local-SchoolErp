@extends('layouts.app')

@section('title', 'Accountant Dashboard')

@section('page-title', 'Accountant Dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Accountant Dashboard</h3>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fa-3x mb-3"></i>
                    <h5>Fee Collection (Today)</h5>
                    <h3>₹{{ number_format($todayCollection ?? 0, 2) }}</h3>
                    <p class="mb-0"><small>{{ $todayCount ?? 0 }} payments received</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Outstanding Fees</h5>
                    <h3>₹{{ number_format($totalOutstanding ?? 0, 2) }}</h3>
                    <p class="mb-0"><small>{{ $outstandingStudentCount ?? 0 }} students pending</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-receipt fa-3x mb-3"></i>
                    <h5>Receipts Generated</h5>
                    <h3>{{ $monthlyReceipts ?? 0 }}</h3>
                    <p class="mb-0"><small>This month</small></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-award fa-3x mb-3"></i>
                    <h5>Scholarships</h5>
                    <h3>{{ $pendingScholarships ?? 0 }}</h3>
                    <p class="mb-0"><small>pending approval</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Scholarship Applications -->
    @if(($pendingScholarships ?? 0) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-warning"><i class="bi bi-file-earmark-check me-2"></i>Pending Scholarship Applications</h5>
                    <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-sm btn-warning">View All</a>
                </div>
                <div class="card-body">
                    <p class="text-muted">There are <strong>{{ $pendingScholarships }}</strong> scholarship applications waiting for approval.</p>
                    <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-outline-warning">
                        <i class="bi bi-check-circle me-1"></i>Review Applications
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('fees.payments.create') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-plus-circle me-2"></i>Collect Fee Payment
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.structures.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-list-columns me-2"></i>Manage Fee Structures
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.outstanding.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-exclamation-triangle me-2"></i>View Outstanding Fees
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-file-earmark-check me-2"></i>Scholarship Applications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Fee Collections -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Fee Collections</h5>
                    <a href="{{ route('fees.payments.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Status</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td>{{ $payment->student->first_name ?? 'N/A' }} {{ $payment->student->last_name ?? '' }}</td>
                                    <td>{{ $payment->student->admission_number ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @if($payment->payment_mode == 'cash')
                                            <span class="badge bg-success">Cash</span>
                                        @elseif($payment->payment_mode == 'online')
                                            <span class="badge bg-info">Online</span>
                                        @elseif($payment->payment_mode == 'bank_transfer')
                                            <span class="badge bg-primary">Bank Transfer</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($payment->payment_mode) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status == 'completed')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($payment->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status == 'completed')
                                            <a href="{{ route('fees.payments.download', $payment->id) }}" class="btn btn-sm btn-outline-primary" title="Download Receipt">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No recent payments found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remaining Fees of Students -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Remaining Fees of Students</h5>
                    <a href="{{ route('fees.outstanding.index') }}" class="btn btn-sm btn-warning">View All Outstanding</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    <th>Program</th>
                                    <th>Division</th>
                                    <th>Fee Type</th>
                                    <th>Total Fee</th>
                                    <th>Paid</th>
                                    <th>Remaining</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($remainingFees as $fee)
                                <tr>
                                    <td>
                                        <strong>{{ $fee->student->first_name ?? 'N/A' }} {{ $fee->student->last_name ?? '' }}</strong>
                                    </td>
                                    <td>{{ $fee->student->admission_number ?? 'N/A' }}</td>
                                    <td>{{ $fee->student->program->name ?? 'N/A' }}</td>
                                    <td>{{ $fee->student->division->name ?? 'N/A' }}</td>
                                    <td>{{ $fee->feeStructure->feeHead->name ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($fee->final_amount, 2) }}</td>
                                    <td><span class="text-success">₹{{ number_format($fee->paid_amount, 2) }}</span></td>
                                    <td><strong class="text-danger">₹{{ number_format($fee->outstanding_amount, 2) }}</strong></td>
                                    <td>
                                        @if($fee->status === 'pending')
                                            <span class="badge bg-danger">Pending</span>
                                        @elseif($fee->status === 'partial')
                                            <span class="badge bg-warning">Partial</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($fee->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                                        No remaining fees found. All students are up to date!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Scholarship Applications (Dynamic) -->
    @php
        $pendingApps = \App\Models\Fee\ScholarshipApplication::with(['student', 'scholarship'])
            ->where('status', 'pending')
            ->limit(5)
            ->get();
    @endphp
    
    @if($pendingApps->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-warning"><i class="bi bi-file-earmark-check me-2"></i>Pending Scholarship Applications</h5>
                    <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-sm btn-warning">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Scholarship Type</th>
                                    <th>Amount</th>
                                    <th>Applied Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingApps as $app)
                                <tr>
                                    <td>
                                        <strong>{{ $app->student->first_name ?? 'N/A' }} {{ $app->student->last_name ?? '' }}</strong><br>
                                        <small>{{ $app->student->admission_number ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $app->scholarship->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($app->scholarship)
                                            {{ $app->scholarship->type === 'percentage' ? $app->scholarship->value . '%' : '₹' . number_format($app->scholarship->value, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $app->created_at->format('d M Y') }}</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <form action="{{ route('fees.scholarship-applications.approve', $app) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this application?')">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-sm btn-danger" title="Reject">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Fee Reports')
@section('page-title', 'Fee Reports')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Reports</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Program</label>
                            <select name="program_id" class="form-select">
                                <option value="">All Programs</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ $programId == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i>Generate
                            </button>
                            <a href="{{ route('fees.reports') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin fa-3x mb-3"></i>
                    <h5>Total Collected</h5>
                    <h3>₹{{ number_format($totalCollected, 2) }}</h3>
                    <p class="mb-0"><small>{{ $totalPayments }} transactions</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Total Outstanding</h5>
                    <h3>₹{{ number_format($totalOutstanding, 2) }}</h3>
                    <p class="mb-0"><small>Pending from all students</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up fa-3x mb-3"></i>
                    <h5>Collection Rate</h5>
                    @php
                        $totalExpected = $totalCollected + $totalOutstanding;
                        $rate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 1) : 0;
                    @endphp
                    <h3>{{ $rate }}%</h3>
                    <p class="mb-0"><small>This period</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection by Payment Mode -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Collection by Payment Mode</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Payment Mode</th>
                                    <th>Transactions</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collectionByMode as $item)
                                <tr>
                                    <td>
                                        @if($item->payment_mode == 'cash')
                                            <span class="badge bg-success">Cash</span>
                                        @elseif($item->payment_mode == 'online')
                                            <span class="badge bg-info">Online</span>
                                        @elseif($item->payment_mode == 'bank_transfer')
                                            <span class="badge bg-primary">Bank Transfer</span>
                                        @elseif($item->payment_mode == 'cheque')
                                            <span class="badge bg-secondary">Cheque</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ ucfirst($item->payment_mode) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->count }}</td>
                                    <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collection by Fee Head -->
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Collection by Fee Type</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Count</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collectionByFeeHead as $item)
                                <tr>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td>{{ $item->count }}</td>
                                    <td><strong class="text-success">₹{{ number_format($item->total, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection by Program -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Collection by Program</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Program</th>
                                    <th>Transactions</th>
                                    <th>Total Collected</th>
                                    <th>% Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = $collectionByProgram->sum('total'); @endphp
                                @forelse($collectionByProgram as $item)
                                <tr>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td>{{ $item->count }}</td>
                                    <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $share = $grandTotal > 0 ? round(($item->total / $grandTotal) * 100, 1) : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                 style="width: {{ $share }}%"
                                                 aria-valuenow="{{ $share }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $share }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Outstanding Students -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-warning">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Top 20 Students with Outstanding Fees</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Fee Type</th>
                                    <th>Total Fee</th>
                                    <th>Paid</th>
                                    <th>Outstanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topOutstanding as $fee)
                                <tr>
                                    <td>
                                        <strong>{{ $fee->student->first_name ?? 'N/A' }} {{ $fee->student->last_name ?? '' }}</strong><br>
                                        <small class="text-muted">{{ $fee->student->admission_number ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $fee->student->program->name ?? 'N/A' }}</td>
                                    <td>{{ $fee->feeStructure->feeHead->name ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($fee->final_amount, 2) }}</td>
                                    <td class="text-success">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                    <td><strong class="text-danger">₹{{ number_format($fee->outstanding_amount, 2) }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No outstanding fees</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Transactions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Receipt #</th>
                                    <th>Mode</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}</td>
                                    <td>
                                        <strong>{{ $payment->student->first_name ?? 'N/A' }} {{ $payment->student->last_name ?? '' }}</strong>
                                    </td>
                                    <td>{{ $payment->student->program->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->receipt_number ?? 'N/A' }}</td>
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
                                    <td><strong>₹{{ number_format($payment->amount, 2) }}</strong></td>
                                    <td>
                                        @if($payment->status == 'completed')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($payment->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No transactions found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

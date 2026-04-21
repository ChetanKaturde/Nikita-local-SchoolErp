@extends('student.layouts.app')

@section('title', 'My Fees')
@section('page-title', 'My Fees')

@section('content')
<div class="container-fluid px-4 py-4">
    @php
        // Check if there are any outstanding fees for quick action
        $hasOutstanding = $feeRecords->contains(function($fee) { return $fee->outstanding_amount > 0; });
    @endphp
    
    @if($hasOutstanding)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center justify-content-between">
                <div>
                    <strong><i class="bi bi-info-circle me-2"></i>You have outstanding fees!</strong>
                    <span class="ms-2">Total Outstanding: <strong>₹{{ number_format($totalOutstanding, 2) }}</strong></span>
                </div>
                <a href="#pay-fees-section" class="btn btn-primary">
                    <i class="bi bi-credit-card me-1"></i> Pay Now
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($feeRecords->count() > 0 || $feeStructures->count() > 0)
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Fees</h6>
                            <h4 class="mb-0">₹{{ number_format($totalFees, 2) }}</h4>
                        </div>
                        <i class="bi bi-credit-card display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Paid Amount</h6>
                            <h4 class="mb-0">₹{{ number_format($totalPaid, 2) }}</h4>
                        </div>
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Outstanding</h6>
                            <h4 class="mb-0">₹{{ number_format($totalOutstanding, 2) }}</h4>
                        </div>
                        <i class="bi bi-exclamation-triangle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($feeStructures->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Fee Structure for {{ $student->program->name ?? 'Your Program' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Academic Year</th>
                                    <th>Amount</th>
                                    <th>Installments</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeStructures as $structure)
                                <tr>
                                    <td><strong>{{ $structure->feeHead->name }}</strong> ({{ $structure->feeHead->code }})</td>
                                    <td>{{ $structure->academic_year }}</td>
                                    <td>₹{{ number_format($structure->amount, 2) }}</td>
                                    <td>{{ $structure->installments }}</td>
                                    <td>
                                        @if($structure->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
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
    
    <div class="row" id="pay-fees-section">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>My Fee Details</h5>
                </div>
                <div class="card-body">
                    @if($feeRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Total Amount</th>
                                    <th>Discount</th>
                                    <th>Final Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeRecords as $fee)
                                <tr>
                                    <td><strong>{{ $fee->feeStructure->feeHead->name ?? 'N/A' }}</strong></td>
                                    <td>₹{{ number_format($fee->total_amount, 2) }}</td>
                                    <td>₹{{ number_format($fee->discount_amount, 2) }}</td>
                                    <td><strong>₹{{ number_format($fee->final_amount, 2) }}</strong></td>
                                    <td class="text-success">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                    <td>
                                        <strong class="{{ $fee->outstanding_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            ₹{{ number_format($fee->outstanding_amount, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($fee->status === 'paid')
                                            <span class="badge bg-success">Full Paid</span>
                                        @elseif($fee->status === 'partial')
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($fee->outstanding_amount > 0)
                                            <a href="{{ route('student.fees.payment', $fee->id) }}" class="btn btn-sm btn-success">
                                                <i class="bi bi-credit-card me-1"></i>Pay Now
                                            </a>
                                        @else
                                            <span class="text-success"><i class="bi bi-check-circle me-1"></i> Paid</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @elseif($feeStructures->count() > 0)
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle me-2"></i>Fee Structures Available But Not Assigned</h5>
                        <p class="mb-2">Fee structures have been defined for your program, but fees have not been assigned to you yet.</p>
                        <hr>
                        <p class="mb-0 text-muted"><strong>Available Fee Structures:</strong></p>
                        <ul class="mt-2">
                            @foreach($feeStructures as $structure)
                            <li>{{ $structure->feeHead->name }} - ₹{{ number_format($structure->amount, 2) }} ({{ $structure->installments }} installments)</li>
                            @endforeach
                        </ul>
                        <p class="mt-3 mb-0"><strong>Please contact your institution administration to get fees assigned.</strong></p>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-credit-card display-4 d-block mb-3 text-muted"></i>
                        <h5>No Fees Found</h5>
                        <p class="mb-0">
                            No fee structures have been created for your program yet. Please contact the admin office.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if($feeRecords->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Payment History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Receipt No.</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeRecords as $fee)
                                    @foreach($fee->payments as $payment)
                                    <tr>
                                        <td><strong>{{ $payment->receipt_number }}</strong></td>
                                        <td>{{ $fee->feeStructure->feeHead->name }}</td>
                                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                                        <td><span class="badge bg-info">{{ ucfirst($payment->payment_mode) }}</span></td>
                                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    </tr>
                                    @endforeach
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
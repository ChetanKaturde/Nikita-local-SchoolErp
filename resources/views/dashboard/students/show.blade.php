@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-person me-2"></i> Student Details</h3>
                <div>
                    <a href="{{ route('dashboard.students.edit', $student) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil-square"></i> Edit Student
                    </a>
                    <a href="{{ route('dashboard.students.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Students
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Student Information -->
                <div class="col-lg-8">
                    <!-- Personal Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Full Name:</td>
                                            <td>{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Date of Birth:</td>
                                            <td>{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Gender:</td>
                                            <td>{{ ucfirst($student->gender) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Blood Group:</td>
                                            <td>{{ $student->blood_group ?? '—' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Religion:</td>
                                            <td>{{ $student->religion ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Category:</td>
                                            <td>{{ strtoupper($student->category) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Mobile:</td>
                                            <td>{{ $student->mobile_number ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Email:</td>
                                            <td>{{ $student->email ?? '—' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($student->current_address || $student->permanent_address)
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Current Address:</h6>
                                    <p class="text-muted">{{ $student->current_address ?? '—' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Permanent Address:</h6>
                                    <p class="text-muted">{{ $student->permanent_address ?? '—' }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Academic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Admission Number:</td>
                                            <td><span class="badge bg-primary">{{ $student->admission_number }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Roll Number:</td>
                                            <td><span class="badge bg-info">{{ $student->roll_number }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Program:</td>
                                            <td>{{ $student->program?->name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Division:</td>
                                            <td>{{ $student->division?->division_name ?? '—' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Academic Session:</td>
                                            <td>{{ $student->academicSession?->session_name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Academic Year:</td>
                                            <td>{{ $student->academic_year }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Admission Date:</td>
                                            <td>{{ $student->admission_date?->format('d M Y') ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status:</td>
                                            <td>
                                                <span class="badge bg-{{ $student->student_status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($student->student_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($student->prn || $student->university_seat_number)
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">PRN:</h6>
                                    <p class="text-muted">{{ $student->prn ?? 'Not assigned' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">University Seat Number:</h6>
                                    <p class="text-muted">{{ $student->university_seat_number ?? 'Not assigned' }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Guardians Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Guardians</h5>
                            <a href="{{ route('dashboard.students.guardians.create', $student) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-plus-circle"></i> Add Guardian
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($student->guardians as $guardian)
                                <div class="guardian-card border rounded p-3 mb-3 {{ $guardian->is_primary_contact ? 'border-primary bg-light' : '' }}">
                                    <div class="row">
                                        <div class="col-md-2 text-center">
                                            @if($guardian->photo_path)
                                                <img src="{{ route('documents.guardians.photo', [$student, $guardian]) }}" 
                                                     class="img-thumbnail rounded-circle" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 80px;">
                                                    <i class="bi bi-person text-white fs-2"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-7">
                                            <h6 class="mb-1">
                                                {{ $guardian->first_name }} {{ $guardian->last_name }}
                                                @if($guardian->is_primary_contact)
                                                    <span class="badge bg-primary ms-2">Primary Contact</span>
                                                @endif
                                            </h6>
                                            <p class="text-muted mb-1"><strong>Relation:</strong> {{ ucfirst($guardian->relation) }}</p>
                                            <p class="text-muted mb-1"><strong>Mobile:</strong> {{ $guardian->mobile_number ?? '—' }}</p>
                                            <p class="text-muted mb-1"><strong>Email:</strong> {{ $guardian->email ?? '—' }}</p>
                                            @if($guardian->occupation)
                                                <p class="text-muted mb-0"><strong>Occupation:</strong> {{ $guardian->occupation }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <div class="btn-group-vertical" role="group">
                                                <a href="{{ route('dashboard.students.guardians.edit', [$student, $guardian]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('dashboard.students.guardians.destroy', [$student, $guardian]) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Delete this guardian?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-people fs-1 text-muted"></i>
                                    <p class="mt-2">No guardians added yet.</p>
                                    <a href="{{ route('dashboard.students.guardians.create', $student) }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Add First Guardian
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Fee Details Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Fee Details</h5>
                        </div>
                        <div class="card-body">
                            @if($feeRecords->count() > 0)
                                <!-- Fee Summary Cards -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Total Fees</h6>
                                                <h4 class="mb-0">₹{{ number_format($totalFees, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Paid Amount</h6>
                                                <h4 class="mb-0">₹{{ number_format($totalPaid, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Outstanding</h6>
                                                <h4 class="mb-0">₹{{ number_format($totalOutstanding, 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Fee Records Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Fee Type</th>
                                                <th>Total Amount</th>
                                                <th>Discount</th>
                                                <th>Final Amount</th>
                                                <th>Paid Amount</th>
                                                <th>Outstanding</th>
                                                <th>Installments</th>
                                                <th>Status</th>
                                                <th>Payment History</th>
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
                                                    <td class="{{ $fee->outstanding_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                        <strong>₹{{ number_format($fee->outstanding_amount, 2) }}</strong>
                                                    </td>
                                                    <td>{{ $fee->feeStructure->installments ?? 1 }}</td>
                                                    <td>
                                                        @if($fee->status === 'paid')
                                                            <span class="badge bg-success">Full Paid</span>
                                                        @elseif($fee->status === 'partial')
                                                            <span class="badge bg-warning">Partial</span>
                                                        @else
                                                            <span class="badge bg-danger">Unpaid</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($fee->payments && $fee->payments->count() > 0)
                                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#paymentHistory{{ $fee->id }}">
                                                                <i class="bi bi-receipt"></i> View ({{ $fee->payments->count() }})
                                                            </button>
                                                            
                                                            <!-- Payment History Modal -->
                                                            <div class="modal fade" id="paymentHistory{{ $fee->id }}" tabindex="-1">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-info text-white">
                                                                            <h5 class="modal-title">Payment History - {{ $fee->feeStructure->feeHead->name ?? 'Fee' }}</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <table class="table table-sm">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Receipt No.</th>
                                                                                        <th>Installment</th>
                                                                                        <th>Amount</th>
                                                                                        <th>Date</th>
                                                                                        <th>Mode</th>
                                                                                        <th>Status</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($fee->payments as $payment)
                                                                                    <tr>
                                                                                        <td><strong>{{ $payment->receipt_number }}</strong></td>
                                                                                        <td>{{ $payment->installment_number }}</td>
                                                                                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                                                                                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                                                                        <td>{{ ucfirst($payment->payment_mode) }}</td>
                                                                                        <td>
                                                                                            <span class="badge bg-{{ $payment->status === 'success' ? 'success' : 'warning' }}">
                                                                                                {{ ucfirst($payment->status) }}
                                                                                            </span>
                                                                                        </td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No payments yet</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-currency-dollar fs-1 text-muted"></i>
                                    <p class="mt-2">No fee records found for this student.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Photo & Documents -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Photo & Documents</h5>
                        </div>
                        <div class="card-body text-center">
                            <!-- Student Photo -->
                            <div class="mb-4">
                                <h6>Student Photo</h6>
                                @if($student->photo_path)
                                    <img src="{{ route('documents.students.document', [$student, 'photo']) }}" 
                                         class="img-thumbnail" 
                                         style="width: 200px; height: 250px; object-fit: cover;">
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                         style="width: 200px; height: 250px; margin: 0 auto;">
                                        <i class="bi bi-person fs-1 text-muted"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Student Signature -->
                            <div class="mb-3">
                                <h6>Student Signature</h6>
                                @if($student->signature_path)
                                    <img src="{{ route('documents.students.document', [$student, 'signature']) }}" 
                                         class="img-thumbnail" 
                                         style="width: 200px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                         style="width: 200px; height: 100px; margin: 0 auto;">
                                        <span class="text-muted">No signature</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Documents Section -->
                            <div class="mb-3">
                                <h6>Documents</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Cast Certificate</small>
                                        @if($student->cast_certificate_path)
                                            <a href="{{ route('documents.students.document', [$student, 'cast_certificate']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-pdf"></i> View
                                            </a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Marksheet</small>
                                        @if($student->marksheet_path)
                                            <a href="{{ route('documents.students.document', [$student, 'marksheet']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-pdf"></i> View
                                            </a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </div>
                                </div>
                                @if($student->aadhar_path || $student->income_certificate_path || $student->domicile_certificate_path)
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Aadhar Card</small>
                                        @if($student->aadhar_path)
                                            <a href="{{ route('documents.students.document', [$student, 'aadhar']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-pdf"></i> View
                                            </a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Income Certificate</small>
                                        @if($student->income_certificate_path)
                                            <a href="{{ route('documents.students.document', [$student, 'income_certificate']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-pdf"></i> View
                                            </a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('dashboard.students.edit', $student) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit Student
                                </a>
                                <a href="{{ route('dashboard.students.guardians.create', $student) }}" class="btn btn-info">
                                    <i class="bi bi-person-plus"></i> Add Guardian
                                </a>
                                <button class="btn btn-success" onclick="window.print()">
                                    <i class="bi bi-printer"></i> Print Details
                                </button>
                                <form action="{{ route('dashboard.students.destroy', $student) }}" 
                                      method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-trash"></i> Delete Student
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .quick-actions { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endsection
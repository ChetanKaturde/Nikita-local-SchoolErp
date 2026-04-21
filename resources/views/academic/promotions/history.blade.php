@extends('layouts.app')

@section('title', 'Promotion History')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="bi bi-clock-history"></i> Promotion History
                        </h3>
                        <a href="{{ route('academic.promotions.index') }}" class="btn btn-primary ms-2">
                            <i class="bi bi-arrow-up-circle"></i> Back to Promotion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('academic.promotions.history') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Student</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Name or Admission No." value="{{ $search }}">
                        </div>
                        <div class="col-md-3">
                            <label for="from_session_id" class="form-label">Session</label>
                            <select name="from_session_id" id="from_session_id" class="form-select">
                                <option value="">All Sessions</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ $fromSessionId == $session->id ? 'selected' : '' }}>
                                        {{ $session->session_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="promotion_type" class="form-label">Promotion Type</label>
                            <select name="promotion_type" id="promotion_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="promoted" {{ $promotionType == 'promoted' ? 'selected' : '' }}>Normal Promoted</option>
                                <option value="conditionally_promoted" {{ $promotionType == 'conditionally_promoted' ? 'selected' : '' }}>ATKT/Conditional</option>
                                <option value="repeated" {{ $promotionType == 'repeated' ? 'selected' : '' }}>Repeated</option>
                                <option value="demoted" {{ $promotionType == 'demoted' ? 'selected' : '' }}>Demoted</option>
                                <option value="transferred" {{ $promotionType == 'transferred' ? 'selected' : '' }}>Transferred</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($promotions->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No promotion history found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="historyTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Student Name</th>
                                        <th>From Class</th>
                                        <th>To Class</th>
                                        <th>Promotion Type</th>
                                        <th>Promoted By</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($promotions as $promotion)
                                        <tr class="{{ $promotion->status === 'rolled_back' ? 'table-secondary' : '' }}">
                                            <td>
                                                {{ $promotion->created_at->format('d-m-Y H:i') }}
                                            </td>
                                            <td>
                                                <strong>{{ $promotion->student->full_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $promotion->student->admission_number ?? '-' }}</small>
                                            </td>
                                            <td>
                                                {{ $promotion->fromProgram->name ?? '-' }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $promotion->fromDivision->division_name ?? '-' }}
                                                    ({{ $promotion->fromAcademicSession->name ?? '-' }})
                                                </small>
                                            </td>
                                            <td>
                                                {{ $promotion->toProgram->name ?? '-' }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $promotion->toDivision->division_name ?? '-' }}
                                                    ({{ $promotion->toAcademicSession->name ?? '-' }})
                                                </small>
                                            </td>
                                            <td>
                                                @php
                                                    $typeClass = match($promotion->promotion_type) {
                                                        'promoted' => 'success',
                                                        'conditionally_promoted' => 'warning',
                                                        'repeated' => 'danger',
                                                        'demoted' => 'danger',
                                                        'transferred' => 'info',
                                                        'tc_issued' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $typeClass }}">
                                                    {{ $promotion->promotion_type_label }}
                                                </span>
                                                @if($promotion->is_override)
                                                    <br>
                                                    <small class="text-warning">
                                                        <i class="bi bi-exclamation-triangle"></i> Override
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $promotion->promotedBy->name ?? 'Unknown' }}
                                                <br>
                                                <small class="text-muted">{{ $promotion->promoted_by_role ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($promotion->status) {
                                                        'completed' => 'success',
                                                        'pending' => 'warning',
                                                        'cancelled' => 'danger',
                                                        'rolled_back' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ ucfirst($promotion->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($promotion->status === 'completed' && !$promotion->isRolledBack())
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmRollback({{ $promotion->id }}, '{{ $promotion->student->full_name ?? 'this student' }}')"
                                                            title="Rollback Promotion">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $promotions->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rollback Confirmation Modal -->
<div class="modal fade" id="rollbackModal" tabindex="-1" aria-labelledby="rollbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rollbackModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirm Rollback
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to rollback this promotion?</p>
                <p><strong>Student:</strong> <span id="rollbackStudentName"></span></p>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i> 
                    This will delete the new academic record and restore the student's previous academic status.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <form id="rollbackForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-arrow-counterclockwise"></i> Yes, Rollback
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmRollback(promotionId, studentName) {
    document.getElementById('rollbackStudentName').textContent = studentName;
    document.getElementById('rollbackForm').action = '{{ url("academic/promotions/rollback") }}/' + promotionId;
    
    const modal = new bootstrap.Modal(document.getElementById('rollbackModal'));
    modal.show();
}
</script>
@endpush

@endsection

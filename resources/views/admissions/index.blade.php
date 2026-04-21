@extends('layouts.app')

@section('title', 'Admissions')
@section('page-title', 'Admission Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('admissions.index') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search applications..." 
                   value="{{ request('search') }}" style="width: 250px;">
            <select name="status" class="form-select me-2" style="width: 150px;">
                <option value="">All Status</option>
                <option value="applied" {{ request('status') === 'applied' ? 'selected' : '' }}>Applied</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="enrolled" {{ request('status') === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
            </select>
            <button type="submit" class="btn btn-outline-secondary">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <a href="{{ route('admissions.apply.form') }}" class="btn btn-primary">
        <i class="bi bi-plus"></i> New Application
    </a>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>{{ \App\Models\Academic\Admission::where('status', 'applied')->count() }}</h5>
                <p class="mb-0">Applied</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>{{ \App\Models\Academic\Admission::where('status', 'verified')->count() }}</h5>
                <p class="mb-0">Verified</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5>{{ \App\Models\Academic\Admission::where('status', 'rejected')->count() }}</h5>
                <p class="mb-0">Rejected</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5>{{ \App\Models\Academic\Admission::count() }}</h5>
                <p class="mb-0">Total</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Application No</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Contact</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admissions as $admission)
                        <tr>
                            <td>{{ $loop->iteration + ($admissions->currentPage() - 1) * $admissions->perPage() }}</td>
                            <td><strong>{{ $admission->application_no }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $admission->first_name }} {{ $admission->middle_name ?? '' }} {{ $admission->last_name }}</strong><br>
                                    <small class="text-muted">{{ $admission->email }}</small>
                                </div>
                            </td>
                            <td>
                                {{ $admission->program->name ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $admission->division->division_name ?? '' }}</small>
                            </td>
                            <td>
                                <small>{{ $admission->mobile_number }}</small>
                            </td>
                            <td>{{ $admission->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $admission->status === 'applied' ? 'warning' : 
                                    ($admission->status === 'verified' ? 'success' : 
                                    ($admission->status === 'enrolled' ? 'primary' : 'danger')) 
                                }}">
                                    {{ ucfirst($admission->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admissions.show', $admission) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($admission->status === 'applied')
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="verifyAdmission({{ $admission->id }})" title="Verify">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectAdmission({{ $admission->id }})" title="Reject">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                    @if($admission->status === 'verified')
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="enrollAdmission({{ $admission->id }})" title="Enroll Student">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                @if(request('search') || request('status'))
                                    No applications found matching your criteria
                                @else
                                    No applications found
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($admissions->hasPages())
            <!-- Custom Pagination Component -->
            <x-pagination :paginator="$admissions" />
        @endif
    </div>
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Verify Admission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to verify this admission application?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Once verified, the student can be enrolled in the system.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="verifyForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="bi bi-check me-1"></i>Verify</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Admission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x me-1"></i>Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verifyAdmission(id) {
    document.getElementById('verifyForm').action = `/admissions/${id}/verify`;
    new bootstrap.Modal(document.getElementById('verifyModal')).show();
}

function rejectAdmission(id) {
    document.getElementById('rejectForm').action = `/admissions/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function enrollAdmission(id) {
    if(confirm('Are you sure you want to enroll this student? This will create a student account.')) {
        fetch(`/admissions/${id}/enroll`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Student enrolled successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}
</script>
@endpush
@endsection

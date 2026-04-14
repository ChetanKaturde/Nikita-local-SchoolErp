@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Scholarship Applications</h2>
        <a href="{{ route('fees.scholarship-applications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Application
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Scholarship</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td>
                                <strong>{{ $app->student->first_name ?? 'N/A' }} {{ $app->student->last_name ?? '' }}</strong><br>
                                <small>{{ $app->student->admission_number ?? 'N/A' }}</small>
                            </td>
                            <td>
                                {{ $app->scholarship->name ?? 'N/A' }}<br>
                                <small class="text-muted">
                                    @if($app->scholarship)
                                        {{ ucfirst($app->scholarship->type ?? 'fixed') }}:
                                        {{ $app->scholarship->value ?? 0 }}{{ ($app->scholarship->type ?? '') === 'percentage' ? '%' : '₹' }}
                                    @else
                                        N/A
                                    @endif
                                </small>
                            </td>
                            <td>{{ $app->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $app->status === 'approved' ? 'success' : ($app->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td>
                                @if($app->status === 'pending')
                                    <form action="{{ route('fees.scholarship-applications.approve', $app) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this scholarship application?')">Approve</button>
                                    </form>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $app->id }}">Reject</button>
                                    
                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Scholarship Application</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('fees.scholarship-applications.reject', $app) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject this scholarship application for <strong>{{ $app->student->first_name ?? 'N/A' }} {{ $app->student->last_name ?? '' }}</strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Rejection Reason</label>
                                                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Enter reason for rejection..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject Application</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">{{ $app->status === 'approved' ? 'Approved' : 'Rejected' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No applications found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection

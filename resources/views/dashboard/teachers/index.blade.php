@extends('layouts.app')

@section('title', 'Teachers Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-person-badge me-2 text-primary"></i> Teachers Management</h3>
                    <p class="text-muted mb-0">Manage teaching staff and their assignments</p>
                </div>
                <a href="{{ route('dashboard.teachers.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Add Teacher
                </a>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('dashboard.teachers.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Teachers Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Teachers List</h6>
            <small class="text-muted">{{ $teachers->total() }} teachers</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="?sort=name&dir={{ $sortDir === 'asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark">
                                    Teacher Details
                                    @if($sortBy === 'name')
                                        <i class="bi bi-sort-{{ $sortDir }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="?sort=email&dir={{ $sortDir === 'asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark">
                                    Email
                                    @if($sortBy === 'email')
                                        <i class="bi bi-sort-{{ $sortDir }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Password</th>
                            <th>Division Assignment</th>
                            <th>
                                <a href="?sort=created_at&dir={{ $sortDir === 'asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark">
                                    Joined
                                    @if($sortBy === 'created_at')
                                        <i class="bi bi-sort-{{ $sortDir }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $teacher->name }}</strong>
                                            <br><small class="text-muted">Teacher ID: {{ $teacher->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $teacher->email }}</td>
                                <td>
                                    @if($teacher->temp_password)
                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                            <input type="password" class="form-control font-monospace" value="{{ $teacher->temp_password }}"
                                                   id="teacher-password-{{ $teacher->id }}" readonly style="background-color: #f8f9fa; letter-spacing: 2px;">
                                            <button class="btn btn-outline-success" type="button"
                                                    onclick="toggleTeacherPassword('teacher-password-{{ $teacher->id }}')" title="Show/Hide">
                                                <i class="bi bi-eye" id="teacher-eye-{{ $teacher->id }}"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" type="button"
                                                    onclick="copyTeacherPassword('teacher-password-{{ $teacher->id }}')" title="Copy">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> 
                                            Updated: {{ $teacher->password_generated_at ? \Carbon\Carbon::parse($teacher->password_generated_at)->diffForHumans() : 'N/A' }}
                                        </small>
                                        @if($teacher->password_generated_at && $teacher->password_generated_at->diffInMinutes() < 60)
                                            <small class="text-success d-block">
                                                <i class="bi bi-check-circle"></i> Recently set by admin
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge bg-warning">No Password Set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($teacher->assignedDivision)
                                        <span class="badge bg-success">{{ $teacher->assignedDivision->division_name }}</span>
                                        <br><small class="text-muted">{{ $teacher->assignedDivision->academicYear->session_name ?? '' }}</small>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>{{ $teacher->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('dashboard.teachers.show', $teacher) }}" 
                                           class="btn btn-sm btn-primary" title="View Details">
                                            👁️
                                        </a>
                                        <a href="{{ route('dashboard.teachers.edit', $teacher) }}" 
                                           class="btn btn-sm btn-warning" title="Edit Teacher">
                                            ✏️
                                        </a>
                                        <form action="{{ route('dashboard.teachers.destroy', $teacher) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Delete {{ $teacher->name }}? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Delete Teacher" 
                                                    {{ $teacher->assignedDivision ? 'disabled' : '' }}>
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-person-badge fs-1 mb-3 d-block"></i>
                                        <h5>No teachers found</h5>
                                        <p>Add your first teacher to get started.</p>
                                        <a href="{{ route('dashboard.teachers.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Add First Teacher
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($teachers->hasPages())
                <div class="card-footer bg-light">
                    {{ $teachers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleTeacherPassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById('teacher-eye-' + inputId.replace('teacher-password-', ''));
    
    if (input.type === 'password') {
        input.type = 'text';
        if (eyeIcon) {
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        }
    } else {
        input.type = 'password';
        if (eyeIcon) {
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    }
}

function copyTeacherPassword(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(function() {
        // Show success toast
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
        toast.textContent = 'Password copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }, function(err) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-danger position-fixed bottom-0 end-0 m-3';
        toast.textContent = 'Failed to copy password';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    });
}
</script>
@endpush
@extends('layouts.app')

@section('title', 'Students Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="fas fa-user-graduate me-2 text-primary"></i> Students Management</h3>
                    <p class="text-muted mb-0">Manage student records, admissions, and academic information</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print List
                    </button>
                    <a href="{{ route('dashboard.students.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Student
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Students</h6>
                            <h3 class="mb-0">{{ $students->total() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Active Students</h6>
                            <h3 class="mb-0">{{ $students->where('student_status', 'active')->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Programs</h6>
                            <h3 class="mb-0">{{ $programs->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-mortarboard fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">This Page</h6>
                            <h3 class="mb-0">{{ $students->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-ul fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Students</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Program</label>
                    <select name="program_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year" class="form-select" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <option value="FY" {{ request('academic_year') == 'FY' ? 'selected' : '' }}>First Year (FY)</option>
                        <option value="SY" {{ request('academic_year') == 'SY' ? 'selected' : '' }}>Second Year (SY)</option>
                        <option value="TY" {{ request('academic_year') == 'TY' ? 'selected' : '' }}>Third Year (TY)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Name, Email, Roll No..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Students List</h6>
            <small class="text-muted">Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students</small>
        </div>
        <div class="card-body p-0">
            <form method="POST" action="{{ route('dashboard.students.bulkAction') }}" id="bulkActionForm">
                @csrf
                <div class="mb-3 px-3 pt-3">
                    <div class="d-flex gap-2 align-items-center">
                        <select name="action" id="bulkActionSelect" class="form-select" style="width: auto;" required>
                            <option value="">Bulk Action</option>
                            <option value="delete">Delete Selected</option>
                            <option value="activate">Activate Selected</option>
                            <option value="deactivate">Deactivate Selected</option>
                        </select>
                        <button type="button" class="btn btn-primary" onclick="confirmBulkAction()">
                            <i class="bi bi-check2-circle me-1"></i>Apply
                        </button>
                        <span class="text-muted ms-2" id="selectedCount">0 selected</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th style="width: 60px;">Photo</th>
                                <th>
                                    <a href="?sort=first_name&dir={{ $sortDir === 'asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark">
                                        Student Details
                                        @if($sortBy === 'first_name' || $sortBy === 'last_name')
                                            <i class="bi bi-sort-{{ $sortDir }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="?sort=admission_number&dir={{ $sortDir === 'asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark">
                                        Academic Info
                                        @if($sortBy === 'admission_number')
                                            <i class="bi bi-sort-{{ $sortDir }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Contact</th>
                                <th>Password</th>
                                <th>
                                    <a href="?sort=student_status&dir={{ $sortDir === 'asc' ? 'desc' : 'asc' }}" class="text-decoration-none text-dark">
                                        Status
                                        @if($sortBy === 'student_status')
                                            <i class="bi bi-sort-{{ $sortDir }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Fees</th>
                                <th class="text-end" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-checkbox">
                                    </td>
                                    <td class="text-center">
                                    @if($student->photo_path)
                                        <img src="{{ route('documents.students.document', [$student, 'photo']) }}" 
                                             class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-hash"></i> {{ $student->admission_number }}
                                            @if($student->roll_number)
                                                | <i class="bi bi-bookmark"></i> {{ $student->roll_number }}
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-primary mb-1">{{ $student->program?->name ?? '—' }}</span>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-diagram-3"></i> {{ $student->division?->division_name ?? '—' }}
                                            | <i class="bi bi-calendar"></i> {{ $student->academic_year }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($student->email)
                                            <small><i class="bi bi-envelope"></i> {{ $student->email }}</small><br>
                                        @endif
                                        @if($student->mobile_number)
                                            <small><i class="bi bi-phone"></i> {{ $student->mobile_number }}</small>
                                        @else
                                            <small class="text-muted">No contact info</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($student->user && $student->user->temp_password)
                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                            <input type="password" class="form-control font-monospace" value="{{ $student->user->temp_password }}" 
                                                   id="student-password-{{ $student->id }}" readonly style="background-color: #f8f9fa; letter-spacing: 2px;">
                                            <button class="btn btn-outline-success" type="button" 
                                                    onclick="toggleStudentPassword('student-password-{{ $student->id }}')" title="Show/Hide">
                                                <i class="fas fa-eye" id="student-eye-{{ $student->id }}"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" type="button" 
                                                    onclick="copyStudentPassword('student-password-{{ $student->id }}')" title="Copy">
                                                <i class="fas fa-clipboard"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Generated: {{ $student->user->password_generated_at ? \Carbon\Carbon::parse($student->user->password_generated_at)->diffForHumans() : 'N/A' }}</small>
                                    @else
                                        <span class="badge bg-warning">No Password Set</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $student->student_status === 'active' ? 'success' : ($student->student_status === 'graduated' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($student->student_status) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $totalPaid = $student->fees->sum('paid_amount');
                                        $totalOutstanding = $student->fees->sum('outstanding_amount');
                                        $hasFees = $student->fees->count() > 0;
                                    @endphp
                                    @if($hasFees)
                                        <div class="small">
                                            <span class="text-success">Paid: ₹{{ number_format($totalPaid, 0) }}</span><br>
                                            <span class="text-danger">Due: ₹{{ number_format($totalOutstanding, 0) }}</span>
                                        </div>
                                        @if($totalOutstanding > 0)
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-success">Settled</span>
                                        @endif
                                    @else
                                        <span class="text-muted">No Fees</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('dashboard.students.show', $student) }}" 
                                           class="btn btn-sm btn-primary" title="View Details">
                                            👁️
                                        </a>
                                        <a href="{{ route('dashboard.students.edit', $student) }}" 
                                           class="btn btn-sm btn-warning" title="Edit Student">
                                            ✏️
                                        </a>
                                        <form action="{{ route('dashboard.students.destroy', $student) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Delete {{ $student->first_name }} {{ $student->last_name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Student">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 mb-3 d-block"></i>
                                        <h5>No students found</h5>
                                        <p>Try adjusting your filters or add a new student.</p>
                                        <a href="{{ route('dashboard.students.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Add First Student
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} results
                            </small>
                        </div>
                        <div>
                            {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.student-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedCount();
});

document.querySelectorAll('.student-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.student-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count + ' selected';
}

function confirmBulkAction() {
    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
    if (selectedCount === 0) {
        alert('Please select at least one student');
        return;
    }

    const action = document.getElementById('bulkActionSelect').value;
    if (!action) {
        alert('Please select an action');
        return;
    }

    let confirmMessage = '';
    if (action === 'delete') {
        confirmMessage = 'Are you sure you want to delete ' + selectedCount + ' student(s)? This action cannot be undone.';
    } else if (action === 'activate') {
        confirmMessage = 'Are you sure you want to activate ' + selectedCount + ' student(s)?';
    } else if (action === 'deactivate') {
        confirmMessage = 'Are you sure you want to deactivate ' + selectedCount + ' student(s)?';
    }

    if (confirm(confirmMessage)) {
        document.getElementById('bulkActionForm').submit();
    }
}

function toggleStudentPassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById('student-eye-' + inputId.replace('student-password-', ''));
    
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

function copyStudentPassword(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(function() {
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
@endsection
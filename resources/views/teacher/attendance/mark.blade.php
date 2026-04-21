@extends('layouts.teacher')

@section('title', 'Mark Attendance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-calendar-check me-2"></i>Mark Attendance</h2>
            <p class="text-muted mb-0">{{ $timetable->division->division_name }} - {{ $timetable->subject->name }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($attendanceDates->count() > 0)
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="editAttendanceDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-pencil-square me-1"></i>Edit Attendance
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="editAttendanceDropdown">
                    @foreach($attendanceDates as $attDate)
                        <li>
                            <a class="dropdown-item {{ $attDate == $date ? 'active' : '' }}" 
                               href="{{ route('teacher.attendance.create', $timetable->id) }}?date={{ $attDate->format('Y-m-d') }}">
                                {{ $attDate->format('d M Y') }}
                                @php
                                    $count = \App\Models\Academic\Attendance::where('timetable_id', $timetable->id)
                                        ->whereDate('date', $attDate)->count();
                                @endphp
                                <span class="badge bg-primary float-end">{{ $count }} students</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Lecture Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="bi bi-calendar3 text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-1">Date</h6>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="bi bi-clock text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-1">Time</h6>
                        <p class="text-muted">{{ $timetable->formatted_time_range }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-1">Division</h6>
                        <p class="text-muted">{{ $timetable->division->division_name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="bi bi-door-open text-warning" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-1">Room</h6>
                        <p class="text-muted">{{ $timetable->room_number ?? 'TBA' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <form id="attendanceForm" action="{{ route('teacher.attendance.store', $timetable->id) }}" method="POST">
        @csrf
        @method('POST')
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="timetable_id" value="{{ $timetable->id }}">

        @if($existingAttendance->count() > 0)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Editing Attendance</strong> for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}. 
            Modify the status and remarks as needed, then click Update.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Student List ({{ $students->total() }} students)</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">
                        <i class="bi bi-check-circle me-1"></i>Mark All Present
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">
                        <i class="bi bi-x-circle me-1"></i>Mark All Absent
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Roll No</th>
                                <th>Student Name</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Late</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                @php
                                    $existing = $existingAttendance->get($student->id);
                                    $status = $existing ? $existing->status : 'present';
                                @endphp
                                <tr class="{{ $existing ? 'table-info' : '' }}">
                                    <td class="ps-4">
                                        <span class="badge bg-primary">{{ $student->roll_number ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width: 35px; height: 35px; min-width: 35px;">
                                                {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                @if($existing)
                                                    <small class="text-success"><i class="bi bi-check-circle"></i> Previously marked</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="attendances[{{ $index }}][status]" value="present"
                                               class="btn-check" id="present_{{ $student->id }}"
                                               {{ $status === 'present' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-success" for="present_{{ $student->id }}">
                                            <i class="bi bi-check-circle"></i>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="attendances[{{ $index }}][status]" value="absent"
                                               class="btn-check" id="absent_{{ $student->id }}"
                                               {{ $status === 'absent' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-danger" for="absent_{{ $student->id }}">
                                            <i class="bi bi-x-circle"></i>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="attendances[{{ $index }}][status]" value="late"
                                               class="btn-check" id="late_{{ $student->id }}"
                                               {{ $status === 'late' ? 'checked' : '' }}>
                                        <label class="btn btn-sm btn-outline-warning" for="late_{{ $student->id }}">
                                            <i class="bi bi-exclamation-circle"></i>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                                        <input type="text" name="attendances[{{ $index }}][remarks]"
                                               class="form-control form-control-sm"
                                               value="{{ $existing?->remarks ?? '' }}"
                                               placeholder="Optional remarks">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Pagination --}}
            <div class="card-footer bg-white border-0 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                    <p class="text-muted mb-0">
                        Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students
                    </p>
                </div>
            </div>
            
            <div class="card-footer bg-white border-0 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Default status is <strong>Present</strong>. Click on status buttons to change.
                    </p>
                    <div>
                        <button type="button" class="btn btn-primary btn-lg" onclick="submitAttendance()">
                            <i class="bi bi-check-circle me-2"></i>{{ $existingAttendance->count() > 0 ? 'Update Attendance' : 'Submit Attendance' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Force enable submit button on page load
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.removeAttribute('disabled');
    }
    // Also add form submit handler
    const form = document.getElementById('attendanceForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtns = form.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
            });
        });
    }
    
    // Save form data to sessionStorage before pagination
    document.querySelectorAll('a[href*="page="]').forEach(link => {
        link.addEventListener('click', function(e) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            sessionStorage.setItem('attendanceData', JSON.stringify(data));
        });
    });
    
    // Restore form data from sessionStorage
    const savedData = sessionStorage.getItem('attendanceData');
    if (savedData) {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach(key => {
            if (key.startsWith('attendances[')) {
                const input = document.querySelector(`[name="${key}"][value="${data[key]}"]`);
                if (input) input.checked = true;
                const textInput = document.querySelector(`[name="${key}"]`);
                if (textInput && textInput.type === 'text') textInput.value = data[key];
            }
        });
    }
});

function submitAttendance() {
    // Clear saved data on successful submit
    sessionStorage.removeItem('attendanceData');
    document.getElementById('attendanceForm').submit();
}

function markAll(status) {
    const radios = document.querySelectorAll(`input[name$="[status]"][value="${status}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}
</script>
@endpush

@push('styles')
<style>
.btn-check:checked + .btn-outline-success {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

/* Pagination styling */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    color: #0d6efd;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
}
</style>
@endpush
@endsection

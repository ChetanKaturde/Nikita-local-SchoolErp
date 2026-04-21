@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-pencil-square me-2 text-primary"></i> Edit Attendance</h3>
                    <p class="text-muted mb-0">
                        Division: <strong>{{ $division->division_name }}</strong> |
                        Date: <strong>{{ \Carbon\Carbon::parse($validated['date'])->format('d M Y') }}</strong>
                    </p>
                </div>
                <a href="{{ route('academic.attendance.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Holiday Warning Alert --}}
    @if(isset($holidayCheck) && $holidayCheck['is_holiday'])
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">⚠️ Holiday Detected</h5>
                <p class="mb-0">
                    <strong>{{ $holidayCheck['holiday_title'] ?? 'Holiday' }}</strong> 
                    ({{ $holidayCheck['holiday_type'] ?? 'Holiday' }})
                    <br>
                    <small>{{ $holidayCheck['message'] }}</small>
                </p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Session Error/Success Messages --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($students->count() > 0)
        <form action="{{ route('academic.attendance.update') }}" method="POST" id="attendanceForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="division_id" value="{{ $validated['division_id'] }}">
            <input type="hidden" name="academic_session_id" value="{{ $validated['academic_session_id'] }}">
            <input type="hidden" name="date" value="{{ $validated['date'] }}">

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>Student List ({{ $students->count() }} students)</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-success me-2" onclick="markAll('present')">
                            <i class="bi bi-check-all"></i> Mark All Present
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="markAll('absent')">
                            <i class="bi bi-x-circle"></i> Mark All Absent
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Roll No.</th>
                                    <th>Student Name</th>
                                    <th style="width: 200px;" class="text-center">Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $student->roll_number }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($student->photo_path)
                                                    <img src="{{ asset('storage/' . $student->photo_path) }}" 
                                                         class="rounded-circle me-2" width="32" height="32" alt="Photo">
                                                @else
                                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person text-white"></i>
                                                    </div>
                                                @endif
                                                <strong>{{ $student->full_name }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <input type="hidden" name="students[{{ $index }}][student_id]" value="{{ $student->id }}">

                                                <input type="radio" class="btn-check"
                                                       name="students[{{ $index }}][status]"
                                                       id="present_{{ $student->id }}"
                                                       value="Present"
                                                       {{ ($existingAttendance[$student->id] ?? '') == 'Present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm" for="present_{{ $student->id }}">
                                                    <i class="bi bi-check-circle"></i> Present
                                                </label>

                                                <input type="radio" class="btn-check"
                                                       name="students[{{ $index }}][status]"
                                                       id="absent_{{ $student->id }}"
                                                       value="Absent"
                                                       {{ ($existingAttendance[$student->id] ?? '') == 'Absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger btn-sm" for="absent_{{ $student->id }}">
                                                    <i class="bi bi-x-circle"></i> Absent
                                                </label>

                                                <input type="radio" class="btn-check"
                                                       name="students[{{ $index }}][status]"
                                                       id="late_{{ $student->id }}"
                                                       value="Late"
                                                       {{ ($existingAttendance[$student->id] ?? '') == 'Late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning btn-sm" for="late_{{ $student->id }}">
                                                    <i class="bi bi-clock"></i> Late
                                                </label>

                                                <input type="radio" class="btn-check"
                                                       name="students[{{ $index }}][status]"
                                                       id="excused_{{ $student->id }}"
                                                       value="Excused"
                                                       {{ ($existingAttendance[$student->id] ?? '') == 'Excused' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-info btn-sm" for="excused_{{ $student->id }}">
                                                    <i class="bi bi-question-circle"></i> Excused
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="bi bi-save"></i> Update Attendance
                    </button>
                    <a href="{{ route('academic.attendance.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="button" class="btn btn-danger btn-lg ms-3" onclick="confirmDelete()">
                        <i class="bi bi-trash"></i> Delete Attendance
                    </button>
                </div>
            </div>
        </form>

        {{-- Delete Confirmation Modal --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete attendance for this date?</p>
                        <p class="text-muted">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('academic.attendance.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="division_id" value="{{ $validated['division_id'] }}">
                            <input type="hidden" name="date" value="{{ $validated['date'] }}">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                <h5>No Students Found</h5>
                <p class="text-muted">No active students found in this division.</p>
                <a href="{{ route('academic.attendance.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    @endif
</div>

<script>
/**
 * Mark all students as present or absent
 */
function markAll(status) {
    const capitalizedStatus = status.charAt(0).toUpperCase() + status.slice(1);
    const radios = document.querySelectorAll(`input[type="radio"][value="${capitalizedStatus}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}

/**
 * Validate that all students have attendance marked before submission
 */
function validateAttendance() {
    const students = document.querySelectorAll('[name^="students["]');
    const studentIds = new Set();
    const markedStatuses = {};
    
    // Collect all student IDs from the form
    students.forEach(input => {
        const match = input.name.match(/students\[(\d+)\]\.student_id/);
        if (match) {
            studentIds.add(match[1]);
        }
    });
    
    // Check if each student has a status selected
    let allMarked = true;
    let firstUnmarked = null;
    
    studentIds.forEach(index => {
        const statusInput = document.querySelector(`input[name="students[${index}][status]"]:checked`);
        if (!statusInput) {
            allMarked = false;
            if (!firstUnmarked) {
                firstUnmarked = index;
            }
        }
    });
    
    if (!allMarked) {
        alert('Please mark attendance for all students.');
        // Scroll to the first unmarked student
        if (firstUnmarked) {
            const row = document.querySelector(`input[name="students[${firstUnmarked}][student_id]"]`).closest('tr');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.classList.add('bg-warning');
            setTimeout(() => row.classList.remove('bg-warning'), 3000);
        }
        return false;
    }
    
    return true;
}

/**
 * Show delete confirmation modal
 */
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Add form submission validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('attendanceForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateAttendance()) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endsection

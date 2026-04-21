@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="container-fluid">
    <!-- Holiday Alert -->
    @if($todayHoliday)
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); color: white;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div class="flex-grow-1">
                <strong>Today is a Holiday!</strong> - {{ $todayHoliday->title }}
                @if($todayHoliday->start_date != $todayHoliday->end_date)
                    <span class="ms-2">
                        (Holiday from {{ \Carbon\Carbon::parse($todayHoliday->start_date)->format('d M') }} to {{ \Carbon\Carbon::parse($todayHoliday->end_date)->format('d M Y') }})
                    </span>
                @else
                    <span class="ms-2">({{ \Carbon\Carbon::parse($todayHoliday->start_date)->format('d M Y') }})</span>
                @endif
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="fas fa-check-square me-2 text-primary"></i> Attendance Management</h3>
                    <p class="text-muted mb-0">Mark and manage student attendance</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.attendance.report') }}" class="btn btn-info">
                        <i class="bi bi-graph-up"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header {{ ($todayHoliday || $isSunday || !$hasTimetableToday) ? 'bg-secondary' : 'bg-primary' }} text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Mark Attendance</h5>
                </div>
                <div class="card-body">
                    @if($todayHoliday)
                        <div class="alert alert-danger mb-3" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); color: white; border: none;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Today is a Holiday ({{ $todayHoliday->title }})!</strong> Attendance cannot be marked on holidays.
                        </div>
                    @elseif($isSunday)
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-calendar-event me-2"></i>
                            <strong>Today is Sunday!</strong> Attendance cannot be marked on Sundays (weekly off).
                        </div>
                    @elseif(!$hasTimetableToday)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-calendar-x me-2"></i>
                            <strong>No Timetable for Today!</strong> There is no class scheduled for today.
                        </div>
                    @endif
                    <form action="{{ route('academic.attendance.mark') }}" method="POST" id="markForm" class="{{ $todayHoliday ? 'opacity-50' : '' }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="academic_session_id" class="form-label">Academic Session <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_session_id') is-invalid @enderror" 
                                        id="academic_session_id" name="academic_session_id" required>
                                    <option value="">Select Academic Session</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_session_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                                <select class="form-select @error('division_id') is-invalid @enderror" 
                                        id="division_id" name="division_id" required>
                                    <option value="">Select Division</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }} - {{ $division->academicYear->session_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                                       onchange="checkHoliday()">
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="holidayWarning" class="alert alert-warning mt-2 d-none">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <span id="holidayWarningText"></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editMode" name="edit_mode" value="1">
                                    <label class="form-check-label" for="editMode">
                                        <i class="bi bi-pencil"></i> Edit existing attendance
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            @php
                                $canMarkAttendance = !$todayHoliday && !$isSunday && $hasTimetableToday;
                            @endphp
                            <button type="submit" class="btn {{ $canMarkAttendance ? 'btn-success' : 'btn-secondary' }} btn-lg" id="markAttendanceBtn" {{ $canMarkAttendance ? '' : 'disabled' }}>
                                @if($todayHoliday)
                                    <i class="bi bi-calendar-x"></i> Holiday - Cannot Mark Attendance
                                @elseif($isSunday)
                                    <i class="bi bi-calendar-event"></i> Sunday - Cannot Mark Attendance
                                @elseif(!$hasTimetableToday)
                                    <i class="bi bi-calendar-x"></i> No Class Today
                                @else
                                    <i class="bi bi-arrow-right-circle"></i> Proceed to Mark Attendance
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-calendar-day me-2"></i>Today's Attendance Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1">{{ \App\Models\Academic\Attendance::whereDate('date', today())->count() }}</h4>
                                <small class="text-muted">Total Marked</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1">{{ \App\Models\Academic\Attendance::whereDate('date', today())->where('status', 'present')->count() }}</h4>
                                <small class="text-muted">Present</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger mb-1">{{ \App\Models\Academic\Attendance::whereDate('date', today())->where('status', 'absent')->count() }}</h4>
                                <small class="text-muted">Absent</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                @php
                                    $total = \App\Models\Academic\Attendance::whereDate('date', today())->count();
                                    $present = \App\Models\Academic\Attendance::whereDate('date', today())->where('status', 'present')->count();
                                    $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                                @endphp
                                <h4 class="text-info mb-1">{{ $percentage }}%</h4>
                                <small class="text-muted">Attendance Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Check if selected date is a holiday
function checkHoliday() {
    const dateInput = document.getElementById('date');
    const holidayWarning = document.getElementById('holidayWarning');
    const holidayWarningText = document.getElementById('holidayWarningText');
    const submitBtn = document.getElementById('markAttendanceBtn');
    
    if (!dateInput || !dateInput.value) return;
    
    fetch("{{ route('academic.attendance.check-holiday') }}?date=" + dateInput.value)
        .then(response => response.json())
        .then(data => {
            if (data.is_holiday) {
                holidayWarningText.textContent = 'Cannot mark attendance on holiday: ' + (data.holiday_title || 'Holiday');
                holidayWarning.classList.remove('d-none');
                if (submitBtn) submitBtn.disabled = true;
            } else {
                holidayWarning.classList.add('d-none');
                if (submitBtn) submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Holiday check error:', error);
        });
}

// Run on page load and handle edit mode
document.addEventListener('DOMContentLoaded', function() {
    checkHoliday();
    
    const editModeCheckbox = document.getElementById('editMode');
    const markForm = document.getElementById('markForm');
    
    if (editModeCheckbox && markForm) {
        editModeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                markForm.action = '{{ route("academic.attendance.edit.post") }}';
                document.getElementById('markAttendanceBtn').innerHTML = '<i class="bi bi-pencil"></i> Proceed to Edit Attendance';
            } else {
                markForm.action = '{{ route("academic.attendance.mark") }}';
                document.getElementById('markAttendanceBtn').innerHTML = '<i class="bi bi-arrow-right-circle"></i> Proceed to Mark Attendance';
            }
        });
    }
});
</script>
@endsection
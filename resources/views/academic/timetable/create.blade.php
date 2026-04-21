@extends('layouts.app')

@section('title', 'Add Class to Timetable')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fa fa-plus-circle me-2"></i>Add Class to Timetable</h2>
                    <p class="text-muted mb-0">Schedule a new class</p>
                </div>
                <a href="{{ route('academic.timetable.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-calendar-day me-2"></i>Class Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academic.timetable.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="academic_year_id" value="{{ $academicYears->first()->id ?? 1 }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                                <select name="division_id" id="division_id" class="form-select @error('division_id') is-invalid @enderror" required>
                                    <option value="">-- Select Division --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                    <option value="">-- Select Subject --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->code }} - {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="teacher_id" class="form-label">Teacher <span class="text-danger">*</span></label>
                                <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                    <option value="">-- Select Teacher --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date" class="form-label">Specific Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" 
                                       class="form-control @error('date') is-invalid @enderror" 
                                       value="{{ old('date') }}" 
                                       min="{{ date('Y-m-d') }}"
                                       required
                                       onchange="checkHolidayOnDate(); autoDetectDay();">
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fa fa-info-circle"></i> Select a date - day will be auto-detected
                                </div>
                                <div id="holidayWarning" class="alert alert-warning mt-2 d-none">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <span id="holidayWarningText"></span>
                                </div>
                            </div>

                            <!-- Hidden Day Field (auto-calculated from date) -->
                            <input type="hidden" name="day_of_week" id="day_of_week" value="">

                            <div class="col-md-6">
                                <label for="room_id" class="form-label">Room</label>
                                <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror">
                                    <option value="">-- Select Room (Optional) --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->room_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="room_number" class="form-label">Room Number (Manual)</label>
                                <input type="text" name="room_number" id="room_number" class="form-control @error('room_number') is-invalid @enderror" 
                                       value="{{ old('room_number') }}" placeholder="e.g., Room 101">
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Time -->
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                                       value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                                       value="{{ old('end_time') }}" required>
                                <div class="form-text">Must be after start time</div>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-circle-check"></i> Schedule Class
                            </button>
                            <a href="{{ route('academic.timetable.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-circle-xmark"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Information</h5>
                </div>
                <div class="card-body">
                    <h6>Guidelines:</h6>
                    <ul class="small mb-0">
                        <li>Select a division to schedule classes for</li>
                        <li>Choose the subject to be taught</li>
                        <li>Assign a teacher to the class</li>
                        <li>Select day and time slot</li>
                        <li>System will check for teacher conflicts</li>
                        <li>Optionally assign a room</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fa fa-exclamation-triangle me-2"></i>Conflict Check</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-0">
                        The system automatically checks for:
                    </p>
                    <ul class="small mt-2 mb-0">
                        <li>Teacher availability</li>
                        <li>Division schedule conflicts</li>
                        <li>Time slot overlaps</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const teacherSelect = document.getElementById('teacher_id');
    const daySelect = document.getElementById('day_of_week');
    const timeSlotSelect = document.getElementById('time_slot_id');
    const availabilityDiv = document.getElementById('teacher-availability');

    function checkAvailability() {
        const teacherId = teacherSelect.value;
        const dayOfWeek = daySelect.value;
        const timeSlotId = timeSlotSelect.value;

        if (!teacherId || !dayOfWeek || !timeSlotId) {
            availabilityDiv.innerHTML = '';
            availabilityDiv.className = 'form-text';
            return;
        }

        fetch("{{ route('academic.timetable.check-availability') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                teacher_id: teacherId,
                day_of_week: dayOfWeek,
                time_slot_id: timeSlotId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                availabilityDiv.innerHTML = '<i class="fa fa-circle-check text-success"></i> ' + data.message;
                availabilityDiv.className = 'form-text text-success';
            } else {
                availabilityDiv.innerHTML = '<i class="fa fa-circle-xmark text-danger"></i> ' + data.message;
                availabilityDiv.className = 'form-text text-danger';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    teacherSelect.addEventListener('change', checkAvailability);
    daySelect.addEventListener('change', checkAvailability);
    timeSlotSelect.addEventListener('change', checkAvailability);
    
    // Auto-populate day_of_week when date is selected
    const dateInput = document.getElementById('date');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                const dateObj = new Date(selectedDate + 'T00:00:00'); // Fix timezone issue
                const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
                
                // Update hidden day_of_week field
                daySelect.value = dayName;
                
                // Check for holiday
                checkHolidayOnDate();
                
                // Visual feedback
                dateInput.classList.add('border-success');
                setTimeout(() => {
                    dateInput.classList.remove('border-success');
                }, 2000);
            }
        });
    }
    
    // Holiday check function
    window.checkHolidayOnDate = function() {
        const dateInput = document.getElementById('date');
        const holidayWarning = document.getElementById('holidayWarning');
        const holidayWarningText = document.getElementById('holidayWarningText');
        
        if (!dateInput || !dateInput.value) {
            if (holidayWarning) holidayWarning.classList.add('d-none');
            return;
        }
        
        fetch("{{ route('academic.attendance.check-holiday') }}?date=" + dateInput.value)
            .then(response => response.json())
            .then(data => {
                if (data.is_holiday) {
                    if (holidayWarning) {
                        holidayWarningText.textContent = 'This date is marked as Holiday. Attendance and Timetable cannot be added.';
                        holidayWarning.classList.remove('d-none');
                    }
                } else {
                    if (holidayWarning) holidayWarning.classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Holiday check error:', error);
            });
    };
    
    // Auto-detect day from date
    window.autoDetectDay = function() {
        const dateInput = document.getElementById('date');
        const dayField = document.getElementById('day_of_week');
        
        if (dateInput && dayField && dateInput.value) {
            const dateObj = new Date(dateInput.value + 'T00:00:00');
            const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
            dayField.value = dayName;
        }
    };
    
    // Form validation - ensure either date or day_of_week is selected
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const dateValue = dateInput?.value;
            const dayValue = daySelect?.value;
            
            if (!dateValue && !dayValue) {
                e.preventDefault();
                alert('Please select either a specific date or a day of the week');
                if (daySelect) daySelect.focus();
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection

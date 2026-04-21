<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addClassModalLabel">
                    <i class="fa fa-plus-circle me-2"></i>Add Class to Timetable
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addClassForm" method="POST" action="{{ route('academic.timetable.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="academic_year_id" id="addAcademicYearId" value="{{ $academicYears->first()->id ?? 1 }}">

                    <!-- Class Details Section -->
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fa fa-info-circle me-2"></i>Class Details
                    </h6>

                    <div class="row g-3">
                        <!-- Division -->
                        <div class="col-md-6">
                            <label for="addDivisionId" class="form-label fw-semibold">
                                Division <span class="text-danger">*</span>
                            </label>
                            <select name="division_id" id="addDivisionId" class="form-select @error('division_id') is-invalid @enderror" required>
                                <option value="">-- Select Division --</option>
                                @foreach($divisions ?? [] as $division)
                                    <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="col-md-6">
                            <label for="addSubjectId" class="form-label fw-semibold">
                                Subject <span class="text-danger">*</span>
                            </label>
                            <select name="subject_id" id="addSubjectId" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects ?? [] as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Teacher -->
                        <div class="col-md-6">
                            <label for="addTeacherId" class="form-label fw-semibold">
                                Teacher <span class="text-danger">*</span>
                            </label>
                            <select name="teacher_id" id="addTeacherId" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                <option value="">-- Select Teacher --</option>
                                @foreach($teachers ?? [] as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Specific Date -->
                        <div class="col-md-6">
                            <label for="addDate" class="form-label fw-semibold">
                                Specific Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="date" 
                                   id="addDate" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date') }}" 
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            <div class="form-text">Select a date - day will be auto-detected</div>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="holidayWarning" class="alert alert-warning mt-2 d-none">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <span id="holidayWarningText"></span>
                            </div>
                        </div>

                        <!-- Hidden Day Field (auto-calculated from date) -->
                        <input type="hidden" name="day_of_week" id="addDayOfWeek" value="">

                        <!-- Time Slot -->
                        <!-- <div class="col-md-6">
                            <label for="addTimeSlotId" class="form-label fw-semibold">
                                Time Slot
                            </label>
                            <select name="time_slot_id" id="addTimeSlotId" class="form-select @error('time_slot_id') is-invalid @enderror">
                                <option value="">-- Select Time Slot --</option>
                                @if(isset($timeSlots) && $timeSlots->count() > 0)
                                    @foreach($timeSlots as $slot)
                                        <option value="{{ $slot->id }}" {{ old('time_slot_id') == $slot->id ? 'selected' : '' }}>
                                            {{ $slot->name ?? 'Period ' . $loop->iteration }} ({{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-text">Select the class period</div>
                            @error('time_slot_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> -->

                        <!-- Start Time -->
                        <div class="col-md-6">
                            <label for="addStartTime" class="form-label fw-semibold">
                                Start Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   name="start_time" 
                                   id="addStartTime" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   value="{{ old('start_time') }}">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="col-md-6">
                            <label for="addEndTime" class="form-label fw-semibold">
                                End Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   name="end_time" 
                                   id="addEndTime" 
                                   class="form-control @error('end_time') is-invalid @enderror" 
                                   value="{{ old('end_time') }}">
                            <div class="form-text">Must be after start time</div>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Room Number -->
                        <div class="col-md-6">
                            <label for="addRoomNumber" class="form-label fw-semibold">
                                Room Number
                            </label>
                            <input type="text" 
                                   name="room_number" 
                                   id="addRoomNumber" 
                                   class="form-control @error('room_number') is-invalid @enderror" 
                                   value="{{ old('room_number') }}" 
                                   placeholder="e.g., Room 101">
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="addStatus" class="form-label fw-semibold">
                                Status
                            </label>
                            <select name="status" id="addStatus" class="form-select">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Conflict Warning -->
                    <div id="conflictWarning" class="alert alert-danger mt-3 d-none">
                        <i class="fa fa-exclamation-triangle-fill me-2"></i>
                        <strong>Schedule Conflict Detected!</strong>
                        <ul id="conflictList" class="mb-0 mt-2"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-circle-xmark me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="addSubmitBtn">
                        <i class="fa fa-circle-check me-1"></i> Add Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add Class Modal JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const addDateInput = document.getElementById('addDate');
    const holidayWarning = document.getElementById('holidayWarning');
    const holidayWarningText = document.getElementById('holidayWarningText');
    const conflictWarning = document.getElementById('conflictWarning');
    const addSubmitBtn = document.getElementById('addSubmitBtn');

    // Check for holiday when date is selected
    if (addDateInput) {
        addDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                checkHoliday(selectedDate);
            } else {
                holidayWarning.classList.add('d-none');
            }
        });
    }

    // Check holiday function
    function checkHoliday(date) {
        fetch('{{ route("academic.attendance.check-holiday") }}?date=' + date)
            .then(response => response.json())
            .then(data => {
                if (data.is_holiday) {
                    holidayWarning.classList.remove('d-none');
                    holidayWarningText.textContent = data.message || 'This date is a holiday. Attendance cannot be marked.';
                } else {
                    holidayWarning.classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Error checking holiday:', error);
            });
    }
});
</script>

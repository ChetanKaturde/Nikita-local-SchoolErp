@extends('layouts.app')

@section('title', 'Timetable - Grid View')

@section('content')
<div class="container-fluid">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="fas fa-th me-2 text-primary"></i> Timetable - Grid View</h3>
                    <p class="text-muted mb-0">View and manage all timetable entries</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-outline-primary">
                        <i class="fa fa-list"></i> Table View
                    </a>
                    @role('admin|principal')
                    <a href="{{ route('academic.timetable.create') }}" class="btn btn-success">
                        <i class="fa fa-plus-circle"></i> Add Class
                    </a>
                    @endrole
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('academic.timetable.grid') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-building"></i> Division
                    </label>
                    <select name="division_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }} — {{ $division->program->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-person"></i> Teacher
                    </label>
                    <select name="teacher_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar-check"></i> Day
                    </label>
                    <select name="day_of_week" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Days</option>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                            <option value="{{ $day }}" {{ request('day_of_week') == $day ? 'selected' : '' }}>
                                {{ ucfirst($day) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-toggle-on"></i> Status
                    </label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="" {{ request('status') === '' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar"></i> Date
                    </label>
                    <input type="date" name="date" id="date_filter" class="form-control form-select-sm"
                           value="{{ request('date') }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-search"></i> Search
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Subject, Teacher, Room..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request()->anyFilled(['division_id', 'teacher_id', 'day_of_week', 'status', 'search', 'date']))
                        <a href="{{ route('academic.timetable.grid') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Timetable Grid Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-table text-primary me-2"></i>
                        All Timetable Entries
                        @if($selectedDivision)
                            <span class="text-muted fw-normal">| {{ $selectedDivision->division_name }}</span>
                        @endif
                    </h5>
                    <small class="text-muted">
                        @if($timetables->total() > 0)
                            Showing {{ $timetables->count() }} of {{ $timetables->total() }} entries
                        @else
                            No entries found
                        @endif
                    </small>
                </div>
                <div class="d-flex gap-2">
                    @role('admin|principal')
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Class
                    </button>
                    @endrole
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Class / Division</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timetables as $index => $timetable)
                            @php
                                $rowClass = '';
                                if ($timetable->status === 'cancelled') {
                                    $rowClass = 'table-danger';
                                } elseif ($timetable->status === 'completed') {
                                    $rowClass = 'table-light';
                                } elseif ($timetable->status === 'closed') {
                                    $rowClass = 'table-warning';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $timetables->firstItem() + $index }}</td>
                                <td>
                                    @if($timetable->date)
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-calendar-day text-primary me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($timetable->date)->format('d M Y') }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ App\Models\Academic\Timetable::DAY_COLORS[$timetable->day_of_week] ?? '#6c757d' }}">
                                        {{ ucfirst($timetable->day_of_week) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $timetable->subject->name ?? 'N/A' }}</strong>
                                    @if($timetable->subject->code)
                                        <br><small class="text-muted">{{ $timetable->subject->code }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($timetable->teacher)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-gradient text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($timetable->teacher->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $timetable->teacher->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">No Teacher</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $timetable->division->division_name ?? 'N/A' }}</strong>
                                    @if($timetable->division->program)
                                        <br><small class="text-muted">{{ $timetable->division->program->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-clock me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($timetable->start_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-clock me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($timetable->end_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td>
                                    @switch($timetable->status)
                                        @case('active')
                                            <span class="badge bg-success">Active</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-secondary">Completed</span>
                                            @break
                                        @case('upcoming')
                                            <span class="badge bg-info text-dark">Upcoming</span>
                                            @break
                                        @case('closed')
                                            <span class="badge bg-danger">Closed</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($timetable->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @role('admin|principal')
                                    <div class="btn-group btn-group-sm d-flex gap-1">
                                        <a href="{{ route('academic.timetable.edit', $timetable->id) }}" 
                                           class="btn btn-warning btn-sm"
                                           title="Edit"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm"
                                                title="Delete"
                                                data-bs-toggle="tooltip"
                                                onclick="confirmDelete({{ $timetable->id }}, '{{ $timetable->subject->name ?? 'this entry' }} - {{ ucfirst($timetable->day_of_week) }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    @endrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa fa-calendar-xmark text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3 text-muted">No Timetable Entries Found</h5>
                                        <p class="text-muted mb-3">No timetable entries match your current filters.</p>
                                        @role('admin|principal')
                                        <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus-circle me-2"></i>Add First Class
                                        </a>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($timetables->hasPages())
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $timetables->firstItem() ?? 0 }} to {{ $timetables->lastItem() ?? 0 }} of {{ $timetables->total() }} entries
                        </div>
                        
                        <!-- Custom Pagination Component -->
                        <x-pagination :paginator="$timetables" />
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@include('academic.timetable.timetable-modals')

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this timetable entry?</p>
                <div class="alert alert-warning mb-0">
                    <i class="fa fa-info-circle me-2"></i>
                    <strong id="deleteItemName"></strong>
                </div>
                <p class="text-muted small mt-2 mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-xmark me-1"></i>Cancel
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteForm').action = "/academic/timetable/" + id;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Auto-detect day when date is selected in Add Class Modal
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Get the date input and hidden day field in the modal
    const addDateInput = document.getElementById('addDate');
    const addDayField = document.getElementById('addDayOfWeek');
    
    if (addDateInput && addDayField) {
        addDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                // Create a Date object from the selected date (add time to avoid timezone issues)
                const date = new Date(selectedDate + 'T00:00:00');
                // JavaScript getDay() returns: 0=Sunday, 1=Monday, 2=Tuesday, etc.
                const dayIndex = date.getDay();
                
                // Map JavaScript day index to our day values (lowercase)
                const dayMap = {
                    0: 'sunday',
                    1: 'monday',
                    2: 'tuesday',
                    3: 'wednesday',
                    4: 'thursday',
                    5: 'friday',
                    6: 'saturday'
                };
                
                const detectedDay = dayMap[dayIndex];
                
                // Set the hidden day field
                if (detectedDay) {
                    addDayField.value = detectedDay;
                }
            } else {
                addDayField.value = '';
            }
        });
    }
});
</script>
@endpush

@endsection

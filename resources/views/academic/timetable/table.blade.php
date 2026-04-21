@extends('layouts.app')

@section('title', 'Timetable - Table View')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fa fa-calendar-week me-2"></i>Timetable Management</h2>
                    <p class="text-muted mb-0">Manage class schedules and timetables</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-outline-primary {{ request()->routeIs('academic.timetable.table') ? 'active' : '' }}">
                        <i class="fa fa-list"></i> Table View
                    </a>
                    <a href="{{ route('academic.timetable.grid') }}" class="btn btn-outline-primary {{ request()->routeIs('academic.timetable.grid') ? 'active' : '' }}">
                        <i class="fa fa-table-cells"></i> Grid View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('academic.timetable.table') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Program</label>
                    <select name="program_id" id="program_filter" class="form-select" onchange="filterByProgram()">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Division</label>
                    <select name="division_id" id="division_filter" class="form-select">
                        <option value="">All Divisions</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" 
                                    {{ request('division_id') == $division->id ? 'selected' : '' }}
                                    data-program="{{ $division->program_id }}">
                                {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Day</label>
                    <select name="day_of_week" class="form-select">
                        <option value="">All Days</option>
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                            <option value="{{ $day }}" {{ request('day_of_week') == $day ? 'selected' : '' }}>
                                {{ ucfirst($day) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Teacher</label>
                    <select name="teacher_id" class="form-select">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="" {{ request('status') === '' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" id="date_filter" class="form-control"
                           value="{{ request('date') }}" min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                               placeholder="Subject, Teacher, Room..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-outline-secondary w-100">
                        <i class="fa fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @role('admin|principal')
                    <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> Add Schedule
                    </a>
                    @endrole
                </div>
                <div>
                    <a href="{{ route('academic.timetable.export.pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                       class="btn btn-outline-danger" target="_blank">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Timetable Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Scroll indicator -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">
                    <i class="fa fa-arrows-left-right"></i> Scroll horizontally to view all columns
                </small>
                <span class="badge bg-primary">
                    <i class="fa fa-table"></i> {{ $timetables->total() }} Entries
                </span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Division</th>
                            <th>Room</th>
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
                                        <small class="text-muted">Specific date</small>
                                    @else
                                        <span class="text-muted">—</span>
                                        <br><small class="text-muted">Recurring</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ App\Models\Academic\Timetable::DAY_COLORS[$timetable->day_of_week] ?? '#6c757d' }}">
                                        {{ ucfirst($timetable->day_of_week) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($timetable->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($timetable->end_time)->format('H:i') }}</td>
                                <td>
                                    <strong>{{ $timetable->subject->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $timetable->subject->code ?? '' }}</small>
                                </td>
                                <td>{{ $timetable->teacher->name ?? 'No Teacher' }}</td>
                                <td>{{ $timetable->division->division_name ?? 'N/A' }}</td>
                                <td>{{ $timetable->room->room_number ?? $timetable->room_number ?? 'TBA' }}</td>
                                <td>
                                    @switch($timetable->status)
                                        @case('active')
                                            <span class="badge bg-success"><i class="fa fa-circle-check me-1"></i>Active</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger"><i class="fa fa-circle-xmark me-1"></i>Cancelled</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-info text-dark"><i class="fa fa-check-double me-1"></i>Completed</span>
                                            @break
                                        @case('upcoming')
                                            <span class="badge bg-info text-dark"><i class="fa fa-clock me-1"></i>Upcoming</span>
                                            @break
                                        @case('closed')
                                            <span class="badge bg-danger"><i class="fa fa-lock me-1"></i>Closed</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($timetable->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-sm btn-primary btn-view"
                                                data-id="{{ $timetable->id }}"
                                                title="View"
                                                data-bs-toggle="tooltip">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        @role('admin|principal')
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                                data-id="{{ $timetable->id }}"
                                                title="Edit"
                                                data-bs-toggle="tooltip">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                data-id="{{ $timetable->id }}"
                                                data-name="{{ $timetable->subject->name ?? 'Entry' }} on {{ $timetable->date ? \Carbon\Carbon::parse($timetable->date)->format('d M Y') : $timetable->day_of_week }}"
                                                title="Delete"
                                                data-bs-toggle="tooltip">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fa fa-inbox fs-1 d-block mb-2"></i>
                                        No timetable entries found
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-white border-0 px-4 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted pagination-info">
                        <i class="fa fa-list-ul me-2"></i>
                        Showing <strong>{{ $timetables->firstItem() ?? 0 }}</strong> to <strong>{{ $timetables->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $timetables->total() }}</strong> entries
                        @if($timetables->lastPage() > 1)
                            <span class="d-none d-sm-inline ms-2">
                                (Page <strong>{{ $timetables->currentPage() }}</strong> of <strong>{{ $timetables->lastPage() }}</strong>)
                            </span>
                        @endif
                    </div>
                    
                    <!-- Custom Pagination Component -->
                    <x-pagination :paginator="$timetables" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Timetable Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Timetable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this timetable entry?</p>
                <p><strong id="deleteItemName"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('academic.timetable.timetable-modals')
@endsection

@push('styles')
<style>
    /* Timetable Table Specific Styles */
    .table-responsive {
        border-radius: 8px;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        max-width: 100%;
    }

    /* Ensure scrollbar is always visible on desktop */
    .table-responsive::-webkit-scrollbar {
        height: 10px;
        width: 10px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        white-space: nowrap;
        vertical-align: middle;
        padding: 12px 8px;
    }

    .table tbody td {
        vertical-align: middle;
        white-space: nowrap;
        padding: 12px 8px;
    }

    .table tbody td strong {
        color: #212529;
    }

    /* Fix Actions column visibility - Sticky column */
    .table tbody td:last-child,
    .table thead th:last-child {
        position: sticky;
        right: 0;
        background-color: #ffffff;
        z-index: 10;
        border-left: 2px solid #dee2e6;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.05);
    }

    .table thead th:last-child {
        background-color: #f8f9fa !important;
        z-index: 30;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
    }

    .table tbody tr:hover td:last-child {
        background-color: rgba(0, 0, 0, 0.02) !important;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
    }

    /* Make Actions column slightly wider for better button visibility */
    .table thead th:last-child,
    .table tbody td:last-child {
        min-width: 140px;
        max-width: 140px;
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* Action buttons - Solid colors for better visibility */
    .btn-group-sm > .btn {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        border-radius: 4px;
        transition: all 0.2s ease;
        white-space: nowrap;
        color: white !important;
    }

    .btn-group-sm > .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        filter: brightness(1.1);
    }

    .btn-group-sm > .btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-view {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
    }

    .btn-edit {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #000 !important;
    }

    .btn-delete {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    /* Icon styling in buttons */
    .btn-view i,
    .btn-edit i,
    .btn-delete i {
        font-size: 0.875rem;
        vertical-align: middle;
    }

    /* Button group spacing */
    .btn-group .btn + .btn {
        margin-left: 2px;
    }

    /* Status badges */
    .badge.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
    }

    .badge.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    }

    .badge.bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
    }
    
    /* Table row hover effect */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }
    
    /* Cancelled row */
    .table-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    
    /* Pagination wrapper */
    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
    }
    
    @media (max-width: 767px) {
        .pagination-wrapper {
            justify-content: center;
            width: 100%;
        }
        
        .pagination-wrapper .pagination {
            justify-content: center;
        }
    }
    
    /* Card footer for pagination */
    .card-footer.bg-white {
        border-top: 1px solid #e9ecef !important;
    }
    
    /* Pagination info text */
    .pagination-info {
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    .pagination-info strong {
        color: #0d6efd;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
// Filter divisions by program
function filterByProgram() {
    const programId = document.getElementById('program_filter').value;
    const divisionSelect = document.getElementById('division_filter');
    const divisions = divisionSelect.querySelectorAll('option');
    
    divisions.forEach(option => {
        if (!programId || option.dataset.program === programId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Reset division selection if not visible
    if (divisionSelect.value && divisionSelect.querySelector('option[value="' + divisionSelect.value + '"]')?.style.display === 'none') {
        divisionSelect.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize program filter
    filterByProgram();
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // View button handler
    document.querySelectorAll('.btn-view').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('viewModal'));

            // Show loading state
            document.getElementById('viewModalBody').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading timetable details...</p>
                </div>
            `;

            fetch("{{ route('academic.timetable.ajax.get') }}?id=" + id)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data) {
                        const t = data.data;
                        const statusBadge = t.status === 'active' ? 'bg-success' : 'bg-danger';
                        const statusText = t.status.charAt(0).toUpperCase() + t.status.slice(1);
                        
                        document.getElementById('viewModalBody').innerHTML = `
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%;">Day</th>
                                            <td>
                                                <span class="badge" id="dayBadge">${t.day_name}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Time</th>
                                            <td><i class="fa fa-clock me-2"></i>${t.formatted_time}</td>
                                        </tr>
                                        ${t.period_name ? `<tr><th>Period</th><td>${t.period_name}</td></tr>` : ''}
                                        <tr>
                                            <th>Subject</th>
                                            <td><strong>${t.subject_name}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Teacher</th>
                                            <td><i class="fa fa-user me-2"></i>${t.teacher_name}</td>
                                        </tr>
                                        <tr>
                                            <th>Division</th>
                                            <td><i class="fa fa-users me-2"></i>${t.division_name}</td>
                                        </tr>
                                        <tr>
                                            <th>Room</th>
                                            <td><i class="fa fa-location-dot me-2"></i>${t.room_name}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td><span class="badge ${statusBadge}">${statusText}</span></td>
                                        </tr>
                                        ${t.notes ? `<tr><th>Notes</th><td>${t.notes}</td></tr>` : ''}
                                    </tbody>
                                </table>
                            </div>
                        `;
                        
                        // Set day badge color after modal is shown
                        setTimeout(() => {
                            const dayBadge = document.getElementById('dayBadge');
                            if (dayBadge) {
                                const dayColors = {
                                    'Monday': '#3B82F6',
                                    'Tuesday': '#8B5CF6',
                                    'Wednesday': '#10B981',
                                    'Thursday': '#F59E0B',
                                    'Friday': '#EF4444',
                                    'Saturday': '#6366F1'
                                };
                                dayBadge.style.backgroundColor = dayColors[t.day_name] || '#6c757d';
                                dayBadge.style.color = 'white';
                            }
                        }, 100);
                        
                        modal.show();
                    } else {
                        throw new Error('No data found');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('viewModalBody').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Error loading data</strong>
                            <p class="mb-0 mt-2">Unable to load timetable details. Please try again.</p>
                        </div>
                    `;
                    modal.show();
                });
        });
    });

    // Edit button handler
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.dataset.id;
            window.location.href = "{{ route('academic.timetable.index') }}/" + id + "/edit";
        });
    });

    // Delete button handler with improved confirmation
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('deleteItemName').textContent = name;
            document.getElementById('deleteForm').action = "{{ route('academic.timetable.index') }}/" + id;

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
    
    // Add date filter functionality
    const dateFilterInput = document.getElementById('date_filter');
    if (dateFilterInput) {
        dateFilterInput.addEventListener('change', function() {
            if (this.value) {
                // Submit form when date is selected
                this.closest('form').submit();
            }
        });
    }
});
</script>
@endpush

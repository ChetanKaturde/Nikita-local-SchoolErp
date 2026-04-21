@extends('layouts.app')

@section('title', 'Student Promotion')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="bi bi-arrow-up-circle"></i> Student Promotion
                        </h3>
                        <a href="{{ route('academic.promotions.history') }}" class="btn btn-info ms-2">
                            <i class="bi bi-clock-history"></i> View History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('academic.promotions.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="from_session_id" class="form-label">From Session</label>
                            <select name="from_session_id" id="from_session_id" class="form-select">
                                <option value="">Select Session</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ $fromSessionId == $session->id ? 'selected' : '' }}>
                                        {{ $session->session_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="program_id" class="form-label">Program</label>
                            <select name="program_id" id="program_id" class="form-select">
                                <option value="">All Programs</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ $programId == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="division_id" class="form-label">Division</label>
                            <select name="division_id" id="division_id" class="form-select">
                                <option value="">All Divisions</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Target Session Info -->
    @if($nextSession && $nextAcademicYear)
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Promotion Target:</strong>
                To {{ $nextSession->name }} ({{ $nextAcademicYear }})
                @if(!$nextSession)
                    <span class="text-danger">No next session configured!</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Student List -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="card-title mb-0">Eligible Students for Promotion</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-warning btn-sm text-nowrap" id="previewBtn" disabled>
                                <i class="bi bi-eye"></i> Preview
                            </button>
                            <button type="button" class="btn btn-success btn-sm text-nowrap" id="promoteBtn" disabled>
                                <i class="bi bi-check-circle"></i> Promote
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($studentsWithEligibility->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> No students found with the selected filters.
                        </div>
                    @else
                        <form id="promotionForm" method="POST" action="{{ route('academic.promotions.promote') }}">
                            @csrf
                            
                            <!-- Hidden fields for target -->
                            <input type="hidden" name="to_session_id" value="{{ $nextSession?->id }}">
                            <input type="hidden" name="to_program_id" value="{{ $programId }}">
                            <input type="hidden" name="to_division_id" value="" id="to_division_id">
                            <input type="hidden" name="to_academic_year" value="{{ $nextAcademicYear }}">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="promotionTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Student Name</th>
                                            <th>Roll Number</th>
                                            <th>Current Class</th>
                                            <th>Current Division</th>
                                            <th>Attendance %</th>
                                            <th>Result Status</th>
                                            <th>Backlogs</th>
                                            <th>Eligibility</th>
                                            <th>Promotion Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentsWithEligibility as $item)
                                            <tr class="{{ $item['eligible'] ? 'table-success' : ($item['conditional'] ? 'table-warning' : 'table-danger') }}">
                                                <td>
                                                    @if($item['eligible'] || $item['conditional'])
                                                        <input type="checkbox" 
                                                               name="student_ids[]" 
                                                               value="{{ $item['student']->id }}" 
                                                               class="form-check-input student-checkbox">
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $item['student']->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $item['student']->admission_number }}</small>
                                                </td>
                                                <td>{{ $item['record']->roll_number ?? '-' }}</td>
                                                <td>{{ $item['program']->name ?? '-' }}</td>
                                                <td>{{ $item['division']->division_name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item['attendance_percentage'] >= 75 ? 'success' : ($item['attendance_percentage'] >= 60 ? 'warning' : 'danger') }}">
                                                        {{ number_format($item['attendance_percentage'], 1) }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = match($item['result_status']) {
                                                            'pass' => 'success',
                                                            'atkt' => 'warning',
                                                            'fail' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">
                                                        {{ strtoupper($item['result_status']) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($item['backlog_count'] > 0)
                                                        <span class="badge bg-danger">{{ $item['backlog_count'] }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item['eligible'])
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> Eligible
                                                        </span>
                                                    @elseif($item['conditional'])
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-exclamation-triangle"></i> ATKT
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle"></i> Not Eligible
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $typeClass = match($item['promotion_type']) {
                                                            'Normal' => 'success',
                                                            'ATKT' => 'warning',
                                                            'Fail' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $typeClass }}">
                                                        {{ $item['promotion_type'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($item['eligible'] || $item['conditional'])
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary"
                                                                onclick="previewSingle({{ $item['student']->id }})">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-secondary"
                                                                onclick="showIneligibleReason('{{ $item['student']->full_name }}', {!! json_encode($item['reasons']) !!})">
                                                            <i class="bi bi-info-circle"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        
                        @if($studentsWithEligibility->count() > 20)
                        <div class="mt-3">
                            <nav aria-label="Promotion students pagination">
                                <ul class="pagination pagination-sm mb-0">
                                    @php
                                        $currentPage = $allRecords->currentPage();
                                        $totalPages = $allRecords->lastPage();
                                        $total = $allRecords->total();
                                    @endphp
                                    <li class="page-item disabled">
                                        <span class="page-link">Showing {{ $studentsWithEligibility->count() }} of {{ $total }} students</span>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        @endif
                        
                        <!-- Pagination Links -->
                        <div class="mt-2">
                            {{ $allRecords->appends([
                                'from_session_id' => $fromSessionId,
                                'program_id' => $programId,
                                'division_id' => $divisionId
                            ])->links('vendor.pagination.bootstrap-5') }}
                        </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="bi bi-eye"></i> Promotion Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmPromoteBtn">
                    <i class="bi bi-check-circle"></i> Confirm Promotion
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Single Student Preview Modal -->
<div class="modal fade" id="singlePreviewModal" tabindex="-1" aria-labelledby="singlePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="singlePreviewModalLabel">
                    <i class="bi bi-person-check"></i> Individual Promotion Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="singlePreviewModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmSinglePromoteBtn">
                    <i class="bi bi-check-circle"></i> Confirm Promotion
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Ineligible Reason Modal -->
<div class="modal fade" id="ineligibleModal" tabindex="-1" aria-labelledby="ineligibleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="ineligibleModalLabel">
                    <i class="bi bi-exclamation-circle"></i> Not Eligible
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="ineligibleStudentName"></h6>
                <ul id="ineligibleReasons" class="list-group mt-3">
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Select Target Division Modal -->
<div class="modal fade" id="targetDivisionModal" tabindex="-1" aria-labelledby="targetDivisionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="targetDivisionModalLabel">
                    <i class="bi bi-collection"></i> Select Target Division
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="targetDivisionSelect" class="form-label">Promote to Division</label>
                    <select class="form-select" id="targetDivisionSelect" required>
                        <option value="">Select Division</option>
                        @foreach($targetDivisions as $division)
                            <option value="{{ $division->id }}" data-program-id="{{ $division->program_id }}">{{ $division->division_name }} ({{ $division->program->short_name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="proceedToPreview">
                    <i class="bi bi-arrow-right"></i> Proceed to Preview
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedStudents = [];
let currentPreviewData = null;
let singlePreviewStudentId = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Select All checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        updateSelectedStudents();
    });

    // Individual checkbox changes
    document.querySelectorAll('.student-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedStudents);
    });

    // Preview button
    document.getElementById('previewBtn').addEventListener('click', function() {
        if (selectedStudents.length === 0) return;
        showTargetDivisionModal();
    });

    // Promote button
    document.getElementById('promoteBtn').addEventListener('click', function() {
        if (selectedStudents.length === 0) return;
        showTargetDivisionModal();
    });

    // Proceed to preview
    document.getElementById('proceedToPreview').addEventListener('click', function() {
        const divisionId = document.getElementById('targetDivisionSelect').value;
        if (!divisionId) {
            alert('Please select a target division');
            return;
        }
        
        // Get program_id from selected division
        const selectedOption = document.getElementById('targetDivisionSelect').selectedOptions[0];
        const programId = selectedOption ? selectedOption.dataset.programId : null;
        
        document.getElementById('to_division_id').value = divisionId;
        document.querySelector('input[name="to_program_id"]').value = programId;
        
        // Hide modal and show preview
        bootstrap.Modal.getInstance(document.getElementById('targetDivisionModal')).hide();
        
        if (selectedStudents.length === 1) {
            previewSingle(selectedStudents[0]);
        } else {
            showBulkPreview();
        }
    });

    // Confirm promotion
    document.getElementById('confirmPromoteBtn').addEventListener('click', function() {
        if (!confirm('Are you sure you want to promote ' + selectedStudents.length + ' student(s)?')) {
            return;
        }
        document.getElementById('promotionForm').submit();
    });

    // Confirm single promotion
    document.getElementById('confirmSinglePromoteBtn').addEventListener('click', function() {
        if (!singlePreviewStudentId) return;
        
        if (!confirm('Are you sure you want to promote this student?')) {
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("academic/promotions/promote") }}/' + singlePreviewStudentId;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add hidden fields
        const toSessionId = document.querySelector('input[name="to_session_id"]').value;
        const toProgramId = document.querySelector('input[name="to_program_id"]').value;
        const toDivisionId = document.querySelector('input[name="to_division_id"]').value;
        const toAcademicYear = document.querySelector('input[name="to_academic_year"]').value;
        
        form.appendChild(createHiddenInput('to_session_id', toSessionId));
        form.appendChild(createHiddenInput('to_program_id', toProgramId));
        form.appendChild(createHiddenInput('to_division_id', toDivisionId));
        form.appendChild(createHiddenInput('to_academic_year', toAcademicYear));
        
        document.body.appendChild(form);
        form.submit();
    });
});

function createHiddenInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}

function updateSelectedStudents() {
    selectedStudents = [];
    document.querySelectorAll('.student-checkbox:checked').forEach(cb => {
        selectedStudents.push(parseInt(cb.value));
    });
    
    const previewBtn = document.getElementById('previewBtn');
    const promoteBtn = document.getElementById('promoteBtn');
    
    if (selectedStudents.length > 0) {
        previewBtn.disabled = false;
        promoteBtn.disabled = false;
        previewBtn.textContent = 'Preview (' + selectedStudents.length + ')';
        promoteBtn.textContent = 'Promote (' + selectedStudents.length + ')';
    } else {
        previewBtn.disabled = true;
        promoteBtn.disabled = true;
        previewBtn.innerHTML = '<i class="bi bi-eye"></i> Preview Promotion';
        promoteBtn.innerHTML = '<i class="bi bi-check-circle"></i> Promote Selected';
    }
}

function showTargetDivisionModal() {
    const modal = new bootstrap.Modal(document.getElementById('targetDivisionModal'));
    modal.show();
}

function showBulkPreview() {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const modalBody = document.getElementById('previewModalBody');
    
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    modal.show();
    
    // Fetch preview data - send as regular form data (not JSON string)
    const formData = new FormData();
    selectedStudents.forEach(id => {
        formData.append('student_ids[]', id);
    });
    formData.append('to_session_id', document.querySelector('input[name="to_session_id"]').value);
    formData.append('to_program_id', document.querySelector('input[name="to_program_id"]').value);
    formData.append('to_division_id', document.getElementById('to_division_id').value);
    formData.append('to_academic_year', document.querySelector('input[name="to_academic_year"]').value);
    
    const previewUrl = '{{ url("/academic/promotions/preview") }}';
    
    fetch(previewUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Server error: ' + response.status + ' - ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            currentPreviewData = data;
            renderBulkPreview(data);
        } else {
            modalBody.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error loading preview') + '</div>';
        }
    })
    .catch(error => {
        modalBody.innerHTML = '<div class="alert alert-danger">Error loading preview: ' + error.message + '</div>';
    });
}

function renderBulkPreview(data) {
    const modalBody = document.getElementById('previewModalBody');
    const target = data.target;
    
    let html = `
        <div class="alert alert-info">
            <strong>Promoting to:</strong> ${target.session} - ${target.program} - ${target.division} (${target.academic_year})
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>From Class</th>
                        <th>To Class</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.previews.forEach(preview => {
        const canPromote = preview.can_promote;
        const statusClass = canPromote ? 'bg-success' : 'bg-danger';
        const statusText = canPromote ? 'Can Promote' : 'Cannot Promote';
        
        let warnings = '';
        if (preview.eligibility.warnings && preview.eligibility.warnings.length > 0) {
            warnings = `<br><small class="text-warning">⚠️ ${preview.eligibility.warnings.join(', ')}</small>`;
        }
        
        html += `
            <tr class="${canPromote ? '' : 'table-danger'}">
                <td>${preview.student.name}</td>
                <td>${preview.current.program} - ${preview.current.division}</td>
                <td>${preview.proposed.program} - ${preview.proposed.division}</td>
                <td><span class="badge bg-${preview.eligibility.conditional ? 'warning' : 'success'}">${preview.promotion_type}</span></td>
                <td><span class="badge ${statusClass}">${statusText}</span>${warnings}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    
    modalBody.innerHTML = html;
}

function previewSingle(studentId) {
    singlePreviewStudentId = studentId;
    
    const divisionId = document.getElementById('to_division_id').value;
    if (!divisionId) {
        showTargetDivisionModal();
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('singlePreviewModal'));
    const modalBody = document.getElementById('singlePreviewModalBody');
    
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    modal.show();
    
    // Fetch preview data - send as regular form data (not JSON string)
    const formData = new FormData();
    formData.append('student_ids[]', studentId);
    formData.append('to_session_id', document.querySelector('input[name="to_session_id"]').value);
    formData.append('to_program_id', document.querySelector('input[name="to_program_id"]').value);
    formData.append('to_division_id', divisionId);
    formData.append('to_academic_year', document.querySelector('input[name="to_academic_year"]').value);
    
    const previewUrl = '{{ url("/academic/promotions/preview") }}';
    
    fetch(previewUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Server error: ' + response.status + ' - ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.previews.length > 0) {
            renderSinglePreview(data.previews[0], data.target);
        } else {
            modalBody.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error loading preview') + '</div>';
        }
    })
    .catch(error => {
        modalBody.innerHTML = '<div class="alert alert-danger">Error loading preview: ' + error.message + '</div>';
    });
}

function renderSinglePreview(preview, target) {
    const modalBody = document.getElementById('singlePreviewModalBody');
    
    let warnings = '';
    if (preview.eligibility.warnings && preview.eligibility.warnings.length > 0) {
        warnings = `
            <div class="alert alert-warning mt-3">
                <strong><i class="bi bi-exclamation-triangle"></i> Warnings:</strong>
                <ul class="mb-0">
                    ${preview.eligibility.warnings.map(w => `<li>${w}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    let reasons = '';
    if (preview.eligibility.reasons && preview.eligibility.reasons.length > 0) {
        reasons = `
            <div class="alert alert-danger mt-3">
                <strong><i class="bi bi-x-circle"></i> Ineligibility Reasons:</strong>
                <ul class="mb-0">
                    ${preview.eligibility.reasons.map(r => `<li>${r}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2">Current Details</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Student Name:</strong></td>
                        <td>${preview.student.name}</td>
                    </tr>
                    <tr>
                        <td><strong>Admission No:</strong></td>
                        <td>${preview.student.admission_number}</td>
                    </tr>
                    <tr>
                        <td><strong>Session:</strong></td>
                        <td>${preview.current.session}</td>
                    </tr>
                    <tr>
                        <td><strong>Class:</strong></td>
                        <td>${preview.current.program}</td>
                    </tr>
                    <tr>
                        <td><strong>Division:</strong></td>
                        <td>${preview.current.division}</td>
                    </tr>
                    <tr>
                        <td><strong>Result:</strong></td>
                        <td><span class="badge bg-${preview.current.result_status === 'pass' ? 'success' : (preview.current.result_status === 'atkt' ? 'warning' : 'danger')}">${preview.current.result_status.toUpperCase()}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Attendance:</strong></td>
                        <td>${preview.current.attendance}%</td>
                    </tr>
                    <tr>
                        <td><strong>Backlogs:</strong></td>
                        <td>${preview.current.backlogs}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="border-bottom pb-2">Proposed Details</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Session:</strong></td>
                        <td>${preview.proposed.session}</td>
                    </tr>
                    <tr>
                        <td><strong>Class:</strong></td>
                        <td>${preview.proposed.program}</td>
                    </tr>
                    <tr>
                        <td><strong>Division:</strong></td>
                        <td>${preview.proposed.division}</td>
                    </tr>
                    <tr>
                        <td><strong>Academic Year:</strong></td>
                        <td>${preview.proposed.year}</td>
                    </tr>
                </table>
                
                <div class="mt-3">
                    <strong>Promotion Type:</strong>
                    <span class="badge bg-${preview.eligibility.conditional ? 'warning' : 'success'} fs-6">
                        ${preview.promotion_type}
                    </span>
                </div>
                
                <div class="mt-3">
                    <strong>Eligibility Status:</strong>
                    <span class="badge bg-${preview.can_promote ? 'success' : 'danger'} fs-6">
                        ${preview.can_promote ? 'Eligible' : 'Not Eligible'}
                    </span>
                </div>
                
                ${warnings}
                ${reasons}
            </div>
        </div>
    `;
    
    modalBody.innerHTML = html;
    
    // Disable confirm button if not eligible
    const confirmBtn = document.getElementById('confirmSinglePromoteBtn');
    if (!preview.can_promote) {
        confirmBtn.disabled = true;
        confirmBtn.title = 'Student is not eligible for promotion';
    } else {
        confirmBtn.disabled = false;
        confirmBtn.title = '';
    }
}

function showIneligibleReason(studentName, reasons) {
    document.getElementById('ineligibleStudentName').textContent = studentName;
    
    const reasonsList = document.getElementById('ineligibleReasons');
    reasonsList.innerHTML = reasons.map(r => 
        `<li class="list-group-item list-group-item-danger">${r}</li>`
    ).join('');
    
    const modal = new bootstrap.Modal(document.getElementById('ineligibleModal'));
    modal.show();
}

// Program filter - update divisions via AJAX
document.getElementById('program_id').addEventListener('change', function() {
    const programId = this.value;
    const sessionId = document.getElementById('from_session_id').value;
    
    if (programId) {
        fetch('{{ route("academic.promotions.getDivisions") }}?program_id=' + programId + '&session_id=' + sessionId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const divisionSelect = document.getElementById('division_id');
                    divisionSelect.innerHTML = '<option value="">All Divisions</option>';
                    data.divisions.forEach(div => {
                        divisionSelect.innerHTML += '<option value="' + div.id + '">' + div.division_name + '</option>';
                    });
                }
            });
    }
});

// Session filter - update next session info
document.getElementById('from_session_id').addEventListener('change', function() {
    fetch('{{ route("academic.promotions.getNextSession") }}?session_id=' + this.value)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.next_session) {
                document.querySelector('input[name="to_session_id"]').value = data.next_session.id;
                // Could update UI to show next session info
            }
        });
});
</script>
@endpush

@endsection

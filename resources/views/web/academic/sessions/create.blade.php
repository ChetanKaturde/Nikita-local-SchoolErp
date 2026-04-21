@extends('layouts.app')

@section('title', 'Create Academic Session')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-calendar-event me-2 text-primary"></i> Create Academic Session</h3>
                <a href="{{ route('academic.sessions.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Sessions
                </a>
            </div>

            <form action="{{ route('academic.sessions.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Session Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="session_name" class="form-label">Session Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('session_name') is-invalid @enderror" 
                                               id="session_name" name="session_name" value="{{ old('session_name') }}" 
                                               placeholder="e.g., 2025-2026" required>
                                        @error('session_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Format: YYYY-YYYY (e.g., 2025-2026)</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                               id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Academic session start date</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                               id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Academic session end date</small>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Note:</strong> Only one academic session can be active at a time. 
                                    Activating this session will automatically deactivate others.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active Session</strong>
                                        <small class="text-muted d-block">Make this the current active academic session</small>
                                    </label>
                                </div>
                                @error('is_active')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Help Card -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Guidelines</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <small>Session name must be unique</small>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <small>End date must be after start date</small>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <small>Active session must include today's date</small>
                                    </li>
                                    <li class="mb-0">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <small>Only one session can be active at a time</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <button type="submit" class="btn btn-success btn-lg me-3">
                                    <i class="bi bi-check-circle"></i> Create Session
                                </button>
                                <a href="{{ route('academic.sessions.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate session name based on start date
    const startDateInput = document.getElementById('start_date');
    const sessionNameInput = document.getElementById('session_name');
    
    startDateInput.addEventListener('change', function() {
        if (this.value && !sessionNameInput.value) {
            const year = new Date(this.value).getFullYear();
            sessionNameInput.value = year + '-' + (year + 1);
        }
    });
    
    // Validate end date is after start date
    const endDateInput = document.getElementById('end_date');
    
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
    });
});
</script>
@endsection
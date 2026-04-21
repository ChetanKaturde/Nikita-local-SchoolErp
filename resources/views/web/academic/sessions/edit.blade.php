@extends('layouts.app')

@section('title', 'Edit Academic Session')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1">Edit Academic Session</h1>
            <p class="text-muted mb-0">Update academic session information</p>
        </div>
        <a href="{{ route('academic.sessions.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Sessions
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('academic.sessions.update', $session) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Session Name -->
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">
                        Session Name <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        class="form-control @error('session_name') is-invalid @enderror"
                        id="session_name"
                        name="session_name"
                        value="{{ old('session_name', $session->session_name) }}"
                        placeholder="e.g., 2024-2025"
                        required
                    >
                    @error('session_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Enter the academic session year (e.g., 2024-2025)</div>
                </div>

                <!-- Start Date & End Date -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label fw-semibold">
                            Start Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control @error('start_date') is-invalid @enderror"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date', $session->start_date ? \Carbon\Carbon::parse($session->start_date)->format('Y-m-d') : '') }}"
                            required
                        >
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label fw-semibold">
                            End Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control @error('end_date') is-invalid @enderror"
                            id="end_date"
                            name="end_date"
                            value="{{ old('end_date', $session->end_date ? \Carbon\Carbon::parse($session->end_date)->format('Y-m-d') : '') }}"
                            required
                        >
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-check form-switch">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            role="switch" 
                            id="is_active" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', $session->is_active) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="is_active">
                            Active Session
                        </label>
                    </div>
                    <div class="form-text">Mark this session as active</div>
                </div>

                <!-- Form Actions -->
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Session
                        </button>
                        <a href="{{ route('academic.sessions.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Primary button */
.btn-primary {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.btn-primary:hover {
    background-color: #222 !important;
    border-color: #222 !important;
    color: #fff !important;
}

/* Secondary button */
.btn-secondary {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
}

.btn-secondary:hover {
    background-color: #5a6268 !important;
    border-color: #545b62 !important;
}

/* Outline secondary button */
.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

/* Form controls */
.form-control:focus,
.form-select:focus {
    border-color: #000;
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
}

.form-check-input:checked {
    background-color: #000;
    border-color: #000;
}

.form-check-input:focus {
    border-color: #000;
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
}

/* Text styling */
.text-gray-800 {
    color: #2d3748;
}

/* Card styling */
.card {
    border-radius: 0.5rem;
}

/* Form labels */
.form-label {
    color: #495057;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('head')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush
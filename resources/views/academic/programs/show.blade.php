@extends('layouts.app')

@section('title', 'Program Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-graduation-cap me-2"></i>
            {{ $program->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('academic.programs.edit', $program) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Program
            </a>
            <a href="{{ route('academic.programs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Programs
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Program Details Card -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Full Name:</strong> {{ $program->name }}</p>
                    <p><strong>Short Name:</strong> {{ $program->short_name }}</p>
                    <p><strong>Program Code:</strong> <code>{{ $program->code }}</code></p>
                    <p><strong>Department:</strong> 
                        @if($program->department)
                            <span class="badge bg-info">{{ $program->department->name }}</span>
                        @else
                            <span class="text-muted">Not assigned</span>
                        @endif
                    </p>
                    <p><strong>Status:</strong> 
                        @if($program->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Academic Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Program Type:</strong> 
                        @if(!empty($program->program_type))
                            <span class="badge bg-primary">
                                <i class="fas fa-graduation-cap me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $program->program_type)) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </p>
                    <p><strong>Duration:</strong> {{ $program->duration_years }} years</p>
                    <p><strong>Total Semesters:</strong> {{ $program->total_semesters ?? ($program->duration_years * 2) }}</p>
                    <p><strong>University Affiliation:</strong> {{ $program->university_affiliation ?? '—' }}</p>
                    <p><strong>University Program Code:</strong> {{ $program->university_program_code ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Scale & Students -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Grading System</h5>
                </div>
                <div class="card-body">
                    <p><strong>Default Grade Scale:</strong> {{ $program->default_grade_scale_name }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Enrollment Statistics</h5>
                </div>
                <div class="card-body">
                    <p><strong>Enrolled Students:</strong> 
                        <span class="badge bg-primary">{{ $program->students_count ?? 0 }}</span>
                    </p>
                    <p><strong>Available Seats:</strong> 
                        @php
                            $totalSeats = $program->total_semesters ?: ($program->duration_years * 2);
                            $occupied = $program->students_count ?? 0;
                            $available = max(0, $totalSeats - $occupied);
                        @endphp
                        <span class="badge bg-{{ $available > 0 ? 'success' : 'danger' }}">
                            {{ $available }} / {{ $totalSeats }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex gap-2">
                <form action="{{ route('academic.programs.toggle-status', $program) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn 
                        {{ $program->is_active ? 'btn-danger' : 'btn-success' }}">
                        <i class="bi {{ $program->is_active ? 'bi-toggle-off' : 'bi-toggle-on' }} me-1"></i>
                        {{ $program->is_active ? 'Deactivate' : 'Activate' }} Program
                    </button>
                </form>

                @if($program->students_count == 0)
                    <form action="{{ route('academic.programs.destroy', $program) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this program? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Delete Program
                        </button>
                    </form>
                @else
                    <button class="btn btn-outline-secondary" disabled title="Cannot delete program with enrolled students">
                        <i class="bi bi-trash me-1"></i> Delete Program
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
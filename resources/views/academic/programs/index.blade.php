@extends('layouts.app')

@section('title', 'Program Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-graduation-cap me-2"></i>Program Management</h1>
        <a href="{{ route('academic.programs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Add New Program
        </a>
    </div>



    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Department</label>
                    <select name="department_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Programs Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($programs->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-mortarboard fs-1 text-muted mb-3"></i>
                    <h5>No programs found</h5>
                    <p class="text-muted">Get started by creating a new program</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Program</th>
                                <th>Code</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Seats</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programs as $program)
                                <tr>
                                    <td>
                                        <strong>{{ $program->name }}</strong><br>
                                        <small class="text-muted">{{ $program->short_name }}</small>
                                    </td>
                                    <td><code>{{ $program->code }}</code></td>
                                    <td>{{ $program->department->name ?? '—' }}</td>
                                    <td>
                                        @if(!empty($program->program_type))
                                            <span class="badge bg-info">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $program->program_type)) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $program->duration_years }} years</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $program->students_count ?? 0 }} / {{ $program->total_semesters ?: ($program->duration_years * 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($program->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('academic.programs.show', $program) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                👁️
                                            </a>
                                            <a href="{{ route('academic.programs.edit', $program) }}" 
                                               class="btn btn-sm btn-warning" title="Edit Program">
                                                ✏️
                                            </a>
                                            <form action="{{ route('academic.programs.toggle-status', $program) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm 
                                                    {{ $program->is_active ? 'btn-danger' : 'btn-success' }}" 
                                                        title="{{ $program->is_active ? 'Deactivate' : 'Activate' }}">
                                                    {{ $program->is_active ? '⏸️' : '▶️' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-center">
                    {{ $programs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
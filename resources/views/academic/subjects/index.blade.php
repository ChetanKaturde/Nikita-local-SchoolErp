@extends('layouts.app')

@section('title', 'Subjects Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-book me-2 text-primary"></i> Subjects Management</h3>
                    <p class="text-muted mb-0">Manage academic subjects and their details</p>
                </div>
                <a href="{{ route('academic.subjects.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Add Subject
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="program_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request()->hasAny(['search', 'program_id']))
                            <a href="{{ route('academic.subjects.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Subjects List</h6>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted">
                    Showing {{ $subjects->firstItem() ?? 0 }} - {{ $subjects->lastItem() ?? 0 }} of {{ $subjects->total() }} subjects
                </small>
                <form method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="program_id" value="{{ request('program_id') }}">
                    <label class="me-2 mb-0 small text-muted">Show:</label>
                    <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject Details</th>
                            <th>Program</th>
                            <th>Semester</th>
                            <th>Type</th>
                            <th>Credit</th>
                            <th class="text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $subject->name }}</strong>
                                        <br><small class="text-muted">Code: {{ $subject->code }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $subject->program->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $subject->semester }}</td>
                                <td>
                                    <span class="badge bg-{{ $subject->type == 'Theory' ? 'primary' : 'warning' }}">
                                        {{ $subject->type }}
                                    </span>
                                </td>
                                <td>{{ $subject->credit }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('academic.subjects.show', $subject) }}" 
                                           class="btn btn-sm btn-primary" title="View Details">
                                            👁️
                                        </a>
                                        <a href="{{ route('academic.subjects.edit', $subject) }}" 
                                           class="btn btn-sm btn-warning" title="Edit Subject">
                                            ✏️
                                        </a>
                                        <form action="{{ route('academic.subjects.destroy', $subject) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Delete {{ $subject->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Subject">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-book fs-1 mb-3 d-block"></i>
                                        <h5>No subjects found</h5>
                                        <p>Add your first subject to get started.</p>
                                        <a href="{{ route('academic.subjects.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Add First Subject
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subjects->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info small text-muted">
                            Page {{ $subjects->currentPage() }} of {{ $subjects->lastPage() }}
                        </div>
                        <nav aria-label="Subjects pagination">
                            {{ $subjects->appends(request()->query())->links() }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
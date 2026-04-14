@extends('layouts.app')

@section('title', 'Fee Structures')
@section('page-title', 'Fee Structures')

@section('content')
<div class="container-fluid px-4 py-4">

    @if(isset($activeSession) && $activeSession)
    <div class="alert alert-info mb-3">
        <i class="bi bi-info-circle me-2"></i>
        Viewing fee structures for active session: <strong>{{ $activeSession->session_name }}</strong>
        ({{ $activeSession->start_date->format('M d, Y') }} - {{ $activeSession->end_date->format('M d, Y') }})
        <a href="{{ route('academic.sessions.index') }}" class="btn btn-sm btn-outline-primary ms-2">Change Session</a>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Fee Structures</h5>
                    <a href="{{ route('fees.structures.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add New
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small">Program</label>
                            <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Programs</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Fee Head</label>
                            <select name="fee_head_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Fee Heads</option>
                                @foreach($feeHeads as $head)
                                    <option value="{{ $head->id }}" {{ request('fee_head_id') == $head->id ? 'selected' : '' }}>
                                        {{ $head->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Academic Year</label>
                            <input type="text" name="academic_year" class="form-control form-control-sm"
                                   placeholder="e.g., 2025-2026" value="{{ request('academic_year') }}"
                                   onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            @if(request()->hasAny(['program_id', 'fee_head_id', 'academic_year']))
                                <a href="{{ route('fees.structures.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Clear Filters
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fee Head</th>
                                    <th>Program</th>
                                    <th>Academic Year</th>
                                    <th>Amount</th>
                                    <th>Installments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeStructures as $structure)
                                <tr>
                                    <td>{{ $structure->feeHead->name }}</td>
                                    <td>{{ $structure->program->name }}</td>
                                    <td>{{ $structure->academic_year }}</td>
                                    <td>₹{{ number_format($structure->amount, 2) }}</td>
                                    <td>{{ $structure->installments }}</td>
                                    <td>
                                        <span class="badge {{ $structure->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $structure->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('fees.structures.show', $structure) }}" class="btn btn-info btn-sm" title="View">
                                                👁️
                                            </a>
                                            <a href="{{ route('fees.structures.edit', $structure) }}" class="btn btn-warning btn-sm" title="Edit">
                                                ✏️
                                            </a>
                                            <form method="POST" action="{{ route('fees.structures.destroy', $structure) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" title="Delete">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                        No fee structures found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $feeStructures->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
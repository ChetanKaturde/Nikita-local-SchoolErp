@extends('layouts.app')

@section('title', 'Academic Sessions')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1"><i class="bi bi-calendar-event me-2 text-primary"></i> Academic Sessions</h3>
                    <p class="text-muted mb-0">Manage academic years and session periods</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print List
                    </button>
                    <a href="{{ route('academic.sessions.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Session
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Sessions</h6>
                            <h3 class="mb-0">{{ $sessions->total() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-event fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Active Session</h6>
                            <h3 class="mb-0">{{ $sessions->where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Current Year</h6>
                            <h3 class="mb-0">{{ date('Y') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar3 fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">This Page</h6>
                            <h3 class="mb-0">{{ $sessions->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-ul fs-1 opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Sessions</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Session name..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('academic.sessions.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-table me-2"></i>Academic Sessions</h6>
            <small class="text-muted">Showing {{ $sessions->firstItem() ?? 0 }} to {{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }} sessions</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Session Name</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th class="text-end" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr class="{{ $session->is_active ? 'table-success' : '' }}">
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $session->session_name }}</strong>
                                        @if($session->is_active)
                                            <span class="badge bg-success ms-2">Current</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $session->start_date->format('M d, Y') }}</strong>
                                        <span class="text-muted">to</span>
                                        <strong>{{ $session->end_date->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            @php
                                                $duration = $session->start_date->diffInDays($session->end_date);
                                                $months = round($duration / 30);
                                            @endphp
                                            <i class="bi bi-calendar-range"></i> {{ $months }} months ({{ $duration }} days)
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $today = now()->toDateString();
                                        $isCurrentPeriod = $today >= $session->start_date->toDateString() && $today <= $session->end_date->toDateString();
                                    @endphp
                                    
                                    <div>
                                        <span class="badge bg-{{ $session->is_active ? 'success' : 'secondary' }} mb-1">
                                            {{ $session->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <br>
                                        @if($isCurrentPeriod)
                                            <small class="text-success"><i class="bi bi-check-circle"></i> Current Period</small>
                                        @elseif($today < $session->start_date->toDateString())
                                            <small class="text-info"><i class="bi bi-clock"></i> Upcoming</small>
                                        @else
                                            <small class="text-muted"><i class="bi bi-archive"></i> Past</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $session->description ?? '—' }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if(!$session->is_active)
                                            <form action="{{ route('academic.sessions.set-active', $session) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Set {{ $session->session_name }} as the active session? This will deactivate all other sessions.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Set as Active Session">
                                                    ⚡ Set Active
                                                </button>
                                            </form>
                                            <form action="{{ route('academic.sessions.toggle-status', $session) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" title="Activate Session">
                                                    ▶️
                                                </button>
                                            </form>
                                        @elseif($session->is_active)
                                            <span class="badge bg-success me-1 align-self-center">✓ Active</span>
                                        @endif

                                        <a href="{{ route('academic.sessions.edit', $session) }}"
                                           class="btn btn-sm btn-primary" title="Edit Session">
                                            ✏️
                                        </a>

                                        <form action="{{ route('academic.sessions.destroy', $session) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete {{ $session->session_name }}? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    title="Delete Session">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-calendar-event fs-1 mb-3 d-block"></i>
                                        <h5>No academic sessions found</h5>
                                        <p>Create your first academic session to get started.</p>
                                        <a href="{{ route('academic.sessions.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Create First Session
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($sessions->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Showing {{ $sessions->firstItem() }} to {{ $sessions->lastItem() }} of {{ $sessions->total() }} results
                            </small>
                        </div>
                        <div>
                            {{ $sessions->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
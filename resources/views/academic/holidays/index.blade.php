@extends('layouts.app')

@section('title', 'Holiday Management')

@php
use Illuminate\Support\Facades\Auth;
    $canManageHolidays = Auth::check() && in_array(Auth::user()->roles->first()->name ?? '', ['admin', 'principal', 'hod_commerce', 'hod_science', 'hod_management', 'hod_arts']);
@endphp

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-calendar-check me-2"></i>Holiday Management</h2>
                    <p class="text-muted mb-0">Manage school holidays and events</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academic.timetable.table') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-table"></i> Timetable
                    </a>
                    @if($canManageHolidays)
                    <a href="{{ route('academic.holidays.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Holiday
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('academic.holidays.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search holidays..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="public_holiday" {{ request('type') == 'public_holiday' ? 'selected' : '' }}>Public Holiday</option>
                        <option value="school_holiday" {{ request('type') == 'school_holiday' ? 'selected' : '' }}>School Holiday</option>
                        <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Event</option>
                        <option value="program" {{ request('type') == 'program' ? 'selected' : '' }}>Program</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('academic.holidays.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Holidays Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Holidays List</h5>
            <span class="badge bg-light text-primary">{{ $holidays->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Date Range</th>
                            <th>Duration</th>
                            <th>Academic Year</th>
                            <th>Incharge</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $index => $holiday)
                            <tr class="{{ !$holiday->is_active ? 'table-secondary' : '' }}">
                                <td>{{ $holidays->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $holiday->title }}</strong>
                                    @if($holiday->is_recurring)
                                        <br><small class="text-muted"><i class="bi bi-arrow-repeat"></i> Recurring</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'public_holiday' => 'danger',
                                            'school_holiday' => 'warning',
                                            'event' => 'info',
                                            'program' => 'success'
                                        ];
                                        $typeLabels = [
                                            'public_holiday' => 'Public Holiday',
                                            'school_holiday' => 'School Holiday',
                                            'event' => 'Event',
                                            'program' => 'Program'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$holiday->type] ?? 'secondary' }}">
                                        {{ $typeLabels[$holiday->type] ?? ucfirst($holiday->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <i class="bi bi-calendar3 text-muted me-1"></i>
                                        {{ $holiday->start_date->format('d M Y') }}
                                    </div>
                                    @if($holiday->start_date != $holiday->end_date)
                                        <div class="text-muted small">
                                            <i class="bi bi-arrow-right"></i> {{ $holiday->end_date->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $holiday->duration }} day{{ $holiday->duration > 1 ? 's' : '' }}</span>
                                </td>
                                <td>{{ $holiday->academicYear->name ?? 'N/A' }}</td>
                                <td>
                                    @if($holiday->programIncharge)
                                        <small>{{ $holiday->programIncharge->name }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($holiday->is_active)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($canManageHolidays)
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('academic.holidays.edit', $holiday) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('academic.holidays.destroy', $holiday) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this holiday?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-muted">View Only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No holidays configured
                                    </div>
                                    @if($canManageHolidays)
                                    <a href="{{ route('academic.holidays.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle"></i> Add First Holiday
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($holidays->hasPages())
            <div class="card-footer bg-white border-0 px-4 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted pagination-info">
                        <i class="bi bi-list-ul me-2"></i>
                        Showing <strong>{{ $holidays->firstItem() ?? 0 }}</strong> to <strong>{{ $holidays->lastItem() ?? 0 }}</strong>
                        of <strong>{{ $holidays->total() }}</strong> entries
                    </div>
                    
                    <!-- Custom Pagination Component -->
                    <x-pagination :paginator="$holidays" />
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-x fs-1 text-danger mb-2"></i>
                    <h5 class="card-title">Public Holidays</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'public_holiday')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-event fs-1 text-warning mb-2"></i>
                    <h5 class="card-title">School Holidays</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'school_holiday')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-info mb-2"></i>
                    <h5 class="card-title">Events</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'event')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-range fs-1 text-success mb-2"></i>
                    <h5 class="card-title">Programs</h5>
                    <p class="text-muted mb-0">{{ $holidays->where('type', 'program')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endpush
@endsection

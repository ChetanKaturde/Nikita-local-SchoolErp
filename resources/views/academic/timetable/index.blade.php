@extends('layouts.app')

@section('title', 'Timetable Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fa fa-calendar-week me-2"></i>Timetable Management</h2>
                    <p class="text-muted mb-0">Manage class schedules and timetables</p>
                </div>
                <a href="{{ route('academic.timetable.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus-circle"></i> Add Class
                </a>
            </div>
        </div>
    </div>

    <!-- Division Selection -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('academic.timetable.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Select Division</label>
                    <select name="division_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Select Division --</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($selectedDivision)
    <!-- Timetable Display -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa fa-table-cells me-2"></i>
                Timetable - {{ $selectedDivision->division_name }}
            </h5>
            <div>
                <a href="{{ route('academic.timetable.print', $selectedDivision->id) }}" target="_blank" class="btn btn-light btn-sm">
                    <i class="fa fa-print"></i> Print
                </a>
                <a href="{{ route('academic.timetable.create', ['division_id' => $selectedDivision->id]) }}" class="btn btn-light btn-sm">
                    <i class="fa fa-plus-circle"></i> Add Class
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="100">Time/Day</th>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                <th class="text-center">{{ ucfirst($day) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $timeSlots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00'];
                        @endphp
                        @foreach($timeSlots as $time)
                            <tr>
                                <td class="fw-bold bg-light">{{ $time }}</td>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                    <td style="min-width: 150px;">
                                        @php
                                            $class = $timetables[$day] ?? collect();
                                            $matchingClass = $class->first(function($c) use ($time) {
                                                return substr($c->start_time, 0, 5) === $time;
                                            });
                                        @endphp
                                        @if($matchingClass)
                                            <div class="p-2 bg-primary bg-opacity-10 rounded">
                                                <strong class="text-primary">{{ $matchingClass->subject->code ?? 'N/A' }}</strong><br>
                                                <small>{{ $matchingClass->subject->name ?? 'N/A' }}</small><br>
                                                <small class="text-muted">{{ $matchingClass->teacher->name ?? 'N/A' }}</small><br>
                                                <small class="text-muted"><i class="fa fa-location-dot"></i> {{ $matchingClass->room_number ?? 'N/A' }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        <i class="fa fa-info-circle me-2"></i>
        Please select a division to view its timetable.
    </div>
    @endif
</div>
@endsection

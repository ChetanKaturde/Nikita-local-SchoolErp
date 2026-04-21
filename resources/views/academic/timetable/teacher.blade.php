@extends('layouts.app')

@section('title', 'My Timetable')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fa fa-calendar-week me-2"></i>My Timetable</h2>
            <p class="text-muted">Your teaching schedule</p>
        </div>
    </div>

    <!-- Today's Classes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Today's Classes ({{ ucfirst($today) }})</h5>
                </div>
                <div class="card-body">
                    @if($todayClasses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Division</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayClasses as $class)
                                        <tr>
                                            <td>{{ $class->formatted_time_range }}</td>
                                            <td>
                                                <strong>{{ $class->subject->name }}</strong>
                                                <br><small class="text-muted">{{ $class->subject->code }}</small>
                                            </td>
                                            <td>{{ $class->division->division_name }}</td>
                                            <td>{{ $class->room->room_number ?? $class->room_number ?? 'TBA' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fa fa-calendar-xmark fs-1 d-block mb-2"></i>
                            No classes scheduled for today
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Timetable -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Weekly Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Day</th>
                                    <th>Classes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $value => $label)
                                    <tr>
                                        <td class="fw-bold" style="width: 150px;">{{ $label }}</td>
                                        <td>
                                            @php
                                                $dayClasses = $weekClasses[$value] ?? collect();
                                            @endphp
                                            @if($dayClasses->count() > 0)
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($dayClasses as $class)
                                                        <div class="p-2 border rounded" style="min-width: 200px;">
                                                            <strong>{{ $class->formatted_time_range }}</strong><br>
                                                            <span class="text-primary">{{ $class->subject->code }}</span> - 
                                                            {{ $class->subject->name }}<br>
                                                            <small class="text-muted">
                                                                {{ $class->division->division_name }} | 
                                                                {{ $class->room->room_number ?? $class->room_number ?? 'TBA' }}
                                                            </small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No classes</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

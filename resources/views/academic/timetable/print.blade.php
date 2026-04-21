@extends('layouts.app')

@section('title', "Print Timetable - {$division->division_name}")

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fa fa-print me-2"></i>Print Timetable</h2>
                    <p class="text-muted mb-0">{{ $division->division_name }}</p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fa fa-print"></i> Print
                    </button>
                    <a href="{{ route('academic.timetable.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Time/Day</th>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                <th class="text-center">{{ ucfirst($day) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $timeSlots = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00'];
                        @endphp
                        @foreach($timeSlots as $time)
                            <tr>
                                <td class="fw-bold bg-light">{{ $time }}</td>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                    <td style="min-width: 120px;">
                                        @php
                                            $dayClasses = $timetables[$day] ?? collect();
                                            $class = $dayClasses->first(function($c) use ($time) {
                                                return substr($c->start_time, 0, 5) === $time;
                                            });
                                        @endphp
                                        @if($class)
                                            <div class="p-1">
                                                <strong>{{ $class->subject->code ?? '' }}</strong><br>
                                                <small>{{ $class->subject->name ?? '' }}</small><br>
                                                <small class="text-muted">{{ $class->teacher->name ?? '' }}</small><br>
                                                <small class="text-muted">{{ $class->room->room_number ?? $class->room_number ?? '' }}</small>
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
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { padding: 0; }
    .card { box-shadow: none; }
}
</style>
@endsection

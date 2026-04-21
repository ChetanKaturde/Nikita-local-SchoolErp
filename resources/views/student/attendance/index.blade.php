@extends('student.layouts.app')

@section('title', 'My Attendance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-calendar-check me-2 text-success"></i>My Attendance</h2>
                    <p class="text-muted mb-0">Track your attendance across all subjects</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select" style="width: auto;">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Semester</option>
                        <option>This Year</option>
                    </select>
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-center text-white">
                    <div class="mb-3">
                        <i class="bi bi-percent" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h2 class="mb-1">{{ $overallPercentage }}%</h2>
                    <p class="mb-2 opacity-75">Overall Attendance</p>
                    @if($overallPercentage >= 75)
                        <span class="badge bg-white text-success"><i class="bi bi-check-circle me-1"></i>Excellent</span>
                    @elseif($overallPercentage >= 65)
                        <span class="badge bg-white text-warning"><i class="bi bi-exclamation-circle me-1"></i>Warning</span>
                    @else
                        <span class="badge bg-white text-danger"><i class="bi bi-x-circle me-1"></i>Low</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-center text-white">
                    <div class="mb-3">
                        <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h2 class="mb-1">{{ $presentDays }}</h2>
                    <p class="mb-0 opacity-75">Days Present</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-center text-white">
                    <div class="mb-3">
                        <i class="bi bi-x-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h2 class="mb-1">{{ $absentDays }}</h2>
                    <p class="mb-0 opacity-75">Days Absent</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-center text-white">
                    <div class="mb-3">
                        <i class="bi bi-clock" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h2 class="mb-1">{{ $lateDays }}</h2>
                    <p class="mb-0 opacity-75">Late Arrivals</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject-wise Attendance -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>Subject-wise Attendance</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th class="text-center">Total Lectures</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceBySubject as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->subject->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item->subject->code ?? '' }}</small>
                                </td>
                                <td class="text-center">{{ $item->total }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $item->present }}</span></td>
                                <td class="text-center"><span class="badge bg-danger">{{ $item->absent }}</span></td>
                                <td class="text-center">
                                    <div class="progress" style="width: 120px; margin: 0 auto;">
                                        <div class="progress-bar bg-{{ $item->percentage >= 75 ? 'success' : ($item->percentage >= 65 ? 'warning' : 'danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $item->percentage }}%"
                                             aria-valuenow="{{ $item->percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $item->percentage }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($item->percentage >= 75)
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Safe</span>
                                    @elseif($item->percentage >= 65)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Warning</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Low ({{ 75 - $item->percentage }}% needed)</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">No attendance records found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Attendance</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $attendance)
                            <tr>
                                <td>{{ $attendance->date->format('d M Y') }}</td>
                                <td>{{ $attendance->timetable->subject->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>{{ $attendance->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No recent attendance records</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

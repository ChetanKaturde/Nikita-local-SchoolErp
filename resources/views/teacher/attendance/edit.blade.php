@extends('layouts.teacher')

@section('title', 'Edit Attendance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-pencil me-2"></i>Edit Attendance</h2>
            <p class="text-muted mb-0">Update attendance record</p>
        </div>
        <a href="{{ route('teacher.attendance.history') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Attendance Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.attendance.record.update', $attendance->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Student</label>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2 bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width: 40px; height: 40px; min-width: 40px;">
                                        {{ strtoupper(substr($attendance->student->first_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $attendance->student->full_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $attendance->student->roll_number ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <div class="form-control-plaintext fw-semibold">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $attendance->date->format('d M Y') }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <div class="form-control-plaintext">
                                    {{ $attendance->timetable->subject->name ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $attendance->timetable->subject->code ?? '' }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Division</label>
                                <div class="form-control-plaintext">
                                    {{ $attendance->timetable->division->division_name ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div>
                                        <input type="radio" name="status" value="present" id="status_present"
                                               class="btn-check" {{ $attendance->status === 'present' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success" for="status_present">
                                            <i class="bi bi-check-circle me-1"></i>Present
                                        </label>
                                    </div>
                                    <div>
                                        <input type="radio" name="status" value="absent" id="status_absent"
                                               class="btn-check" {{ $attendance->status === 'absent' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger" for="status_absent">
                                            <i class="bi bi-x-circle me-1"></i>Absent
                                        </label>
                                    </div>
                                    <div>
                                        <input type="radio" name="status" value="late" id="status_late"
                                               class="btn-check" {{ $attendance->status === 'late' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="status_late">
                                            <i class="bi bi-exclamation-circle me-1"></i>Late
                                        </label>
                                    </div>
                                </div>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks">{{ old('remarks', $attendance->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Attendance
                            </button>
                            <a href="{{ route('teacher.attendance.history') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Information</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <strong>Marked By:</strong><br>
                        {{ $attendance->markedBy->name ?? 'N/A' }}
                    </p>
                    <p class="small mb-2">
                        <strong>Marked On:</strong><br>
                        {{ $attendance->created_at->format('d M Y, h:i A') }}
                    </p>
                    <p class="small mb-0">
                        <strong>Last Updated:</strong><br>
                        {{ $attendance->updated_at->format('d M Y, h:i A') }}
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3">Current Status</h6>
                    <span class="badge bg-{{ $attendance->status_color }} fs-6">
                        <i class="bi bi-{{ $attendance->status === 'present' ? 'check-circle' : ($attendance->status === 'absent' ? 'x-circle' : 'exclamation-circle') }} me-1"></i>
                        {{ ucfirst($attendance->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

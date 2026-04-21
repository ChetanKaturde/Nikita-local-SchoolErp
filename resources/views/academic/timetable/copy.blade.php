@extends('layouts.app')

@section('title', 'Copy Timetable to Next Session')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fa fa-copy me-2"></i>Copy Timetable to Next Session</h2>
            <p class="text-muted">Duplicate the current timetable to a new academic year</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('academic.timetable.copy') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Source Academic Year *</label>
                            <select name="source_academic_year_id" class="form-select" required>
                                <option value="{{ $currentAcademicYear?->id }}">
                                    {{ $currentAcademicYear?->name ?? 'Select Year' }}
                                </option>
                            </select>
                            <div class="form-text">The current academic year to copy from</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Target Academic Year *</label>
                            <select name="target_academic_year_id" class="form-select" required>
                                <option value="">Select Target Year</option>
                                @foreach($nextAcademicYears as $year)
                                    <option value="{{ $year->id }}">
                                        {{ $year->name }} ({{ $year->start_date->format('Y') }} - {{ $year->end_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">The new academic year to copy to</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Divisions *</label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                @forelse($divisions as $division)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="division_ids[]" 
                                               value="{{ $division->id }}" 
                                               id="division_{{ $division->id }}">
                                        <label class="form-check-label" for="division_{{ $division->id }}">
                                            {{ $division->division_name }} - {{ $division->program->name ?? 'N/A' }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted">No divisions available</p>
                                @endforelse
                            </div>
                            <div class="form-text">Select which divisions to copy</div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> 
                            <ul class="mb-0">
                                <li>Existing timetable entries in the target year will not be overwritten</li>
                                <li>Teacher assignments will need to be verified after copying</li>
                                <li>Room availability may have changed</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-copy"></i> Copy Timetable
                            </button>
                            <a href="{{ route('academic.timetable.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>How it Works</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li class="mb-2">Select the source academic year (current year)</li>
                        <li class="mb-2">Choose the target academic year (next year)</li>
                        <li class="mb-2">Select which divisions to copy</li>
                        <li class="mb-2">Click "Copy Timetable"</li>
                    </ol>
                    <hr>
                    <p class="text-muted small">
                        The system will copy all active timetable entries to the new academic year.
                        Any conflicting entries will be skipped.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

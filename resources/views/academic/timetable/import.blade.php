@extends('layouts.app')

@section('title', 'Import Timetable')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fa fa-file-arrow-up me-2"></i>Import Timetable</h2>
            <p class="text-muted">Bulk import timetable from Excel file</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('academic.timetable.import') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Academic Year *</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">
                                        {{ $year->name }} ({{ $year->start_date->format('Y') }} - {{ $year->end_date->format('Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Excel File *</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                Accepts .xlsx, .xls, or .csv files
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload"></i> Import Timetable
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
                <div class="card-header">
                    <h5 class="mb-0">Expected Format</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Your Excel file should have the following columns:</p>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Column</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>A - Day</td>
                                <td>monday</td>
                            </tr>
                            <tr>
                                <td>B - Start Time</td>
                                <td>09:00</td>
                            </tr>
                            <tr>
                                <td>C - End Time</td>
                                <td>10:00</td>
                            </tr>
                            <tr>
                                <td>D - Subject Code</td>
                                <td>MATH101</td>
                            </tr>
                            <tr>
                                <td>E - Teacher Name</td>
                                <td>John Smith</td>
                            </tr>
                            <tr>
                                <td>F - Room Number</td>
                                <td>Room 101</td>
                            </tr>
                            <tr>
                                <td>G - Division Name</td>
                                <td>Class A</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert alert-info mt-3">
                        <i class="fa fa-info-circle me-2"></i>
                        The system will automatically detect and prevent scheduling conflicts.
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Download Template</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-download"></i> Download Sample Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

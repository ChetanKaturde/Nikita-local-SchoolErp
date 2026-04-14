@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Apply for Scholarship</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('fees.scholarship-applications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Student *</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->admission_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Scholarship *</label>
                    <select name="scholarship_id" class="form-select" required>
                        <option value="">Select Scholarship</option>
                        @foreach($scholarships as $scholarship)
                            <option value="{{ $scholarship->id }}">
                                {{ $scholarship->name }} - {{ $scholarship->value }}{{ $scholarship->type === 'percentage' ? '%' : '₹' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Supporting Documents</label>
                    <input type="file" name="documents" class="form-control" accept=".pdf,.jpg,.png">
                    <small class="text-muted">Upload caste certificate, income certificate, etc. (Max 5MB)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Application</button>
                <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

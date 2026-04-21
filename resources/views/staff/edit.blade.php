@extends('layouts.app')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff Member')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Staff Member</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('staff.update', $staff) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $staff->first_name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $staff->last_name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" value="{{ $staff->phone }}" required pattern="[6-9]\d{9}" maxlength="10"
                               placeholder="Enter 10-digit mobile (start with 6-9)"
                               title="Mobile number must be 10 digits starting with 6-9">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact</label>
                        <input type="text" name="emergency_contact" class="form-control" value="{{ $staff->emergency_contact }}" pattern="[6-9]\d{9}" maxlength="10"
                               placeholder="Enter 10-digit mobile (start with 6-9)"
                               title="Mobile number must be 10 digits starting with 6-9">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ $staff->date_of_birth }}" required
                               max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                               min="{{ date('Y-m-d', strtotime('-60 years')) }}"
                               title="Date of birth (must be between 18 and 60 years)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ $staff->gender == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $staff->gender == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ $staff->gender == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                        <small class="text-muted">Leave blank to keep current password</small>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Designation *</label>
                        <input type="text" name="designation" class="form-control" value="{{ $staff->designation }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department *</label>
                        <select name="department_id" class="form-select" required>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $staff->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employment Type *</label>
                        <select name="employment_type" class="form-select" required>
                            <option value="permanent" {{ $staff->employment_type == 'permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="contract" {{ $staff->employment_type == 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="part_time" {{ $staff->employment_type == 'part_time' ? 'selected' : '' }}>Part Time</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ $staff->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $staff->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ $staff->status == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address *</label>
                        <textarea name="address" class="form-control" rows="3" required>{{ $staff->address }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Update
                    </button>
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

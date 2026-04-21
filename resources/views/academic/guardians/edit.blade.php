@extends('layouts.app')

@section('title', 'Edit Guardian')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp

<style>
    /* Enhanced Validation Error Styling */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545 !important;
        border-width: 2px !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .invalid-feedback {
        color: #dc3545 !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        margin-top: 0.25rem !important;
        display: block !important;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #212529;
    }

    .required-field::after {
        content: ' *';
        color: #dc3545;
        font-weight: 700;
    }

    /* Error Alert Styling */
    .error-summary {
        background-color: #f8d7da;
        border: 2px solid #dc3545;
        color: #721c24;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .error-summary i {
        font-size: 1.2rem;
        margin-right: 0.5rem;
    }

    .error-summary ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .error-summary li {
        color: #721c24;
        font-weight: 500;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-pencil-square me-2"></i> Edit Guardian: {{ $guardian->first_name }} {{ $guardian->last_name }}</h3>
                <a href="{{ route('dashboard.students.show', $student) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Student
                </a>
            </div>

            @if ($errors->any())
            <div class="error-summary alert alert-danger" role="alert">
                <h5 class="mb-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Please correct {{ $errors->count() }} error{{ $errors->count() > 1 ? 's' : '' }} below:
                </h5>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('dashboard.students.guardians.update', [$student, $guardian]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Personal Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Guardian Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name', $guardian->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name', $guardian->last_name) }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="relation" class="form-label">Relation <span class="text-danger">*</span></label>
                                        <select class="form-select @error('relation') is-invalid @enderror" id="relation" name="relation" required>
                                            <option value="">Select Relation</option>
                                            <option value="father" {{ old('relation', $guardian->relation) == 'father' ? 'selected' : '' }}>Father</option>
                                            <option value="mother" {{ old('relation', $guardian->relation) == 'mother' ? 'selected' : '' }}>Mother</option>
                                            <option value="guardian" {{ old('relation', $guardian->relation) == 'guardian' ? 'selected' : '' }}>Guardian</option>
                                            <option value="uncle" {{ old('relation', $guardian->relation) == 'uncle' ? 'selected' : '' }}>Uncle</option>
                                            <option value="aunt" {{ old('relation', $guardian->relation) == 'aunt' ? 'selected' : '' }}>Aunt</option>
                                            <option value="grandfather" {{ old('relation', $guardian->relation) == 'grandfather' ? 'selected' : '' }}>Grandfather</option>
                                            <option value="grandmother" {{ old('relation', $guardian->relation) == 'grandmother' ? 'selected' : '' }}>Grandmother</option>
                                            <option value="other" {{ old('relation', $guardian->relation) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('relation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $guardian->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $guardian->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $guardian->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $guardian->date_of_birth?->format('Y-m-d')) }}">
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="occupation" class="form-label">Occupation</label>
                                        <input type="text" class="form-control @error('occupation') is-invalid @enderror" 
                                               id="occupation" name="occupation" value="{{ old('occupation', $guardian->occupation) }}">
                                        @error('occupation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="annual_income" class="form-label">Annual Income</label>
                                        <input type="number" class="form-control @error('annual_income') is-invalid @enderror" 
                                               id="annual_income" name="annual_income" value="{{ old('annual_income', $guardian->annual_income) }}" min="0">
                                        @error('annual_income')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="education_qualification" class="form-label">Education Qualification</label>
                                        <input type="text" class="form-control @error('education_qualification') is-invalid @enderror" 
                                               id="education_qualification" name="education_qualification" value="{{ old('education_qualification', $guardian->education_qualification) }}">
                                        @error('education_qualification')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="mobile_number" class="form-label required-field">Mobile Number</label>
                                        <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" 
                                               id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $guardian->mobile_number) }}" 
                                               required pattern="[6-9]\d{9}" maxlength="10" minlength="10"
                                               placeholder="Enter 10-digit mobile (start with 6-9)"
                                               title="Mobile number must be 10 digits starting with 6-9 (e.g., 9876543210)">
                                        <small class="text-muted">Must be 10 digits, starting with 6-9 (e.g., 9876543210)</small>
                                        @error('mobile_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email', $guardian->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3">{{ old('address', $guardian->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_primary_contact" name="is_primary_contact" 
                                                   {{ old('is_primary_contact', $guardian->is_primary_contact) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_primary_contact">
                                                <strong>Set as Primary Contact</strong>
                                                <small class="text-muted d-block">Primary contact will be used for all official communications</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Guardian Photo</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="photo-preview mb-3">
                                    @if($guardian->photo_path)
                                        <img id="photoPreview" src="{{ route('documents.guardians.photo', [$student, $guardian]) }}" 
                                             class="img-thumbnail" style="width: 200px; height: 250px; object-fit: cover;">
                                    @else
                                        <img id="photoPreview" src="https://via.placeholder.com/200x250?text=Guardian+Photo" 
                                             class="img-thumbnail" style="width: 200px; height: 250px; object-fit: cover;">
                                    @endif
                                </div>
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                       id="photo" name="photo" accept="image/*">
                                <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                                @if($guardian->photo_path)
                                    <small class="text-info d-block">Leave empty to keep current photo</small>
                                @endif
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Student Info -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Student Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">Name:</td>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Admission No:</td>
                                        <td>{{ $student->admission_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Program:</td>
                                        <td>{{ $student->program?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Division:</td>
                                        <td>{{ $student->division?->division_name ?? '—' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <button type="submit" class="btn btn-success btn-lg me-3">
                                    <i class="bi bi-check-circle"></i> Update Guardian
                                </button>
                                <a href="{{ route('dashboard.students.show', $student) }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Photo preview
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
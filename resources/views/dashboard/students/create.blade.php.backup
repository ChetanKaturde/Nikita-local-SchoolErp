@extends('layouts.app')

@section('title', 'Add New Student')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-person-plus me-2"></i> Add New Student</h3>
                <a href="{{ route('dashboard.students.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Students
                </a>
            </div>

            <form action="{{ route('dashboard.students.store') }}" method="POST" enctype="multipart/form-data" id="studentForm">
                @csrf
                
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                               id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                                        @error('middle_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="blood_group" class="form-label">Blood Group</label>
                                        <select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group">
                                            <option value="">Select Blood Group</option>
                                            <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                            <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                        </select>
                                        @error('blood_group')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="religion" class="form-label">Religion</label>
                                        <input type="text" class="form-control @error('religion') is-invalid @enderror" 
                                               id="religion" name="religion" value="{{ old('religion') }}">
                                        @error('religion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                            <option value="obc" {{ old('category') == 'obc' ? 'selected' : '' }}>OBC</option>
                                            <option value="sc" {{ old('category') == 'sc' ? 'selected' : '' }}>SC</option>
                                            <option value="st" {{ old('category') == 'st' ? 'selected' : '' }}>ST</option>
                                            <option value="vjnt" {{ old('category') == 'vjnt' ? 'selected' : '' }}>VJNT</option>
                                            <option value="nt" {{ old('category') == 'nt' ? 'selected' : '' }}>NT</option>
                                            <option value="ews" {{ old('category') == 'ews' ? 'selected' : '' }}>EWS</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="aadhar_number" class="form-label">Aadhar Number</label>
                                        <input type="text" class="form-control @error('aadhar_number') is-invalid @enderror" 
                                               id="aadhar_number" name="aadhar_number" value="{{ old('aadhar_number') }}" 
                                               maxlength="12" placeholder="Enter 12-digit Aadhar number">
                                        @error('aadhar_number')
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
                                        <label for="mobile_number" class="form-label">Mobile Number</label>
                                        <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" 
                                               id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}">
                                        @error('mobile_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="current_address" class="form-label">Current Address</label>
                                        <textarea class="form-control @error('current_address') is-invalid @enderror" 
                                                  id="current_address" name="current_address" rows="3">{{ old('current_address') }}</textarea>
                                        @error('current_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="permanent_address" class="form-label">Permanent Address</label>
                                        <textarea class="form-control @error('permanent_address') is-invalid @enderror" 
                                                  id="permanent_address" name="permanent_address" rows="3">{{ old('permanent_address') }}</textarea>
                                        @error('permanent_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="sameAddress">
                                            <label class="form-check-label" for="sameAddress">
                                                Same as current address
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Academic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="program_id" class="form-label">Program <span class="text-danger">*</span></label>
                                        <select class="form-select @error('program_id') is-invalid @enderror" id="program_id" name="program_id" required>
                                            <option value="">Select Program</option>
                                            @foreach($programs as $program)
                                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                    {{ $program->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('program_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="division_id" class="form-label">Division <span class="text-danger">*</span></label>
                                        <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id" required>
                                            <option value="">Select Division</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                                    {{ $division->division_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('division_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="academic_session_id" class="form-label">Academic Session <span class="text-danger">*</span></label>
                                        <select class="form-select @error('academic_session_id') is-invalid @enderror" id="academic_session_id" name="academic_session_id" required>
                                            <option value="">Select Session</option>
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                                    {{ $session->session_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academic_session_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                        <select class="form-select @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" required>
                                            <option value="">Select Year</option>
                                            <option value="FY" {{ old('academic_year') == 'FY' ? 'selected' : '' }}>First Year (FY)</option>
                                            <option value="SY" {{ old('academic_year') == 'SY' ? 'selected' : '' }}>Second Year (SY)</option>
                                            <option value="TY" {{ old('academic_year') == 'TY' ? 'selected' : '' }}>Third Year (TY)</option>
                                        </select>
                                        @error('academic_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="admission_date" class="form-label">Admission Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('admission_date') is-invalid @enderror" 
                                               id="admission_date" name="admission_date" value="{{ old('admission_date') }}" required>
                                        @error('admission_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="student_status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('student_status') is-invalid @enderror" id="student_status" name="student_status" required>
                                            <option value="active" {{ old('student_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="graduated" {{ old('student_status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                            <option value="dropped" {{ old('student_status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                            <option value="suspended" {{ old('student_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        </select>
                                        @error('student_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo & Documents -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Photo & Documents</h5>
                            </div>
                            <div class="card-body text-center">
                                <!-- Photo Upload -->
                                <div class="mb-4">
                                    <label for="photo" class="form-label">Student Photo</label>
                                    <div class="photo-preview mb-3">
                                        <img id="photoPreview" src="https://via.placeholder.com/150x200?text=Photo" 
                                             class="img-thumbnail" style="width: 150px; height: 200px; object-fit: cover;">
                                    </div>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                           id="photo" name="photo" accept="image/*">
                                    <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Signature Upload -->
                                <div class="mb-3">
                                    <label for="signature" class="form-label">Student Signature</label>
                                    <div class="signature-preview mb-3">
                                        <img id="signaturePreview" src="https://via.placeholder.com/200x80?text=Signature" 
                                             class="img-thumbnail" style="width: 200px; height: 80px; object-fit: cover;">
                                    </div>
                                    <input type="file" class="form-control @error('signature') is-invalid @enderror" 
                                           id="signature" name="signature" accept="image/*">
                                    <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                                    @error('signature')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Cast Certificate Upload -->
                                <div class="mb-3">
                                    <label for="cast_certificate" class="form-label">Cast Certificate</label>
                                    <input type="file" class="form-control @error('cast_certificate') is-invalid @enderror" 
                                           id="cast_certificate" name="cast_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Max size: 5MB. Formats: PDF, JPG, PNG</small>
                                    @error('cast_certificate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Marksheet Upload -->
                                <div class="mb-3">
                                    <label for="marksheet" class="form-label">Previous Marksheet</label>
                                    <input type="file" class="form-control @error('marksheet') is-invalid @enderror" 
                                           id="marksheet" name="marksheet" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Max size: 5MB. Formats: PDF, JPG, PNG</small>
                                    @error('marksheet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Auto-generated Info -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Auto-Generated</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">
                                    <strong>Admission Number:</strong><br>
                                    <span class="text-success">Auto-generated after save</span>
                                </p>
                                <p class="text-muted mb-0">
                                    <strong>Roll Number:</strong><br>
                                    <span class="text-success">Auto-generated after save</span>
                                </p>
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
                                    <i class="bi bi-check-circle"></i> Create Student
                                </button>
                                <a href="{{ route('dashboard.students.index') }}" class="btn btn-secondary btn-lg">
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

    // Signature preview
    document.getElementById('signature').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('signaturePreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Same address checkbox
    document.getElementById('sameAddress').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('permanent_address').value = document.getElementById('current_address').value;
        }
    });

    // Copy current address when it changes and checkbox is checked
    document.getElementById('current_address').addEventListener('input', function() {
        if (document.getElementById('sameAddress').checked) {
            document.getElementById('permanent_address').value = this.value;
        }
    });
});
</script>
@endsection
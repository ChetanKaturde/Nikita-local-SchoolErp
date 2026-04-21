@extends('layouts.app')

@section('title', 'Add New Student')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center py-4">
        <div class="col-md-10 col-lg-9">
            <div class="card shadow-lg" style="border-radius: 20px; border: none;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #007bff 0%, #1a1a1a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-user-plus text-white fs-4"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">Add New Student</h3>
                        <p class="text-muted mb-0">Fill out the form below to create a student account</p>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('dashboard.students.store') }}" enctype="multipart/form-data" id="studentForm">
                        @csrf

                        <!-- Personal Information -->
                        <div class="section-title"><i class="bi bi-person-badge me-2"></i>Personal Information</div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="first_name" class="form-label fw-semibold required-field">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                <span class="error-message" id="first_name_error"></span>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
                                <input type="text" class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                                <span class="error-message" id="middle_name_error"></span>
                                @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name" class="form-label fw-semibold required-field">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                <span class="error-message" id="last_name_error"></span>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label fw-semibold required-field">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}" required>
                                <span class="error-message" id="date_of_birth_error"></span>
                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label fw-semibold required-field">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <span class="error-message" id="gender_error"></span>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="blood_group" class="form-label fw-semibold">Blood Group</label>
                                <select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                </select>
                                @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="religion" class="form-label fw-semibold">Religion</label>
                                <select class="form-select @error('religion') is-invalid @enderror" id="religion" name="religion">
                                    <option value="">Select Religion</option>
                                    <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Muslim" {{ old('religion') == 'Muslim' ? 'selected' : '' }}>Muslim</option>
                                    <option value="Christian" {{ old('religion') == 'Christian' ? 'selected' : '' }}>Christian</option>
                                    <option value="Sikh" {{ old('religion') == 'Sikh' ? 'selected' : '' }}>Sikh</option>
                                    <option value="Buddhist" {{ old('religion') == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
                                    <option value="Jain" {{ old('religion') == 'Jain' ? 'selected' : '' }}>Jain</option>
                                    <option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('religion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label fw-semibold required-field">Category <span class="text-danger">*</span></label>
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
                                <span class="error-message" id="category_error"></span>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="aadhar_number" class="form-label fw-semibold">Aadhar Number</label>
                                <input type="text" class="form-control @error('aadhar_number') is-invalid @enderror" id="aadhar_number" name="aadhar_number" value="{{ old('aadhar_number') }}" maxlength="12">
                                <span class="error-message" id="aadhar_number_error"></span>
                                @error('aadhar_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="section-title"><i class="bi bi-telephone me-2"></i>Contact Information</div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold required-field">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                <span class="error-message" id="email_error"></span>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mobile_number" class="form-label fw-semibold required-field">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required maxlength="10">
                                <span class="error-message" id="mobile_number_error"></span>
                                @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="current_address" class="form-label fw-semibold required-field">Current Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('current_address') is-invalid @enderror" id="current_address" name="current_address" rows="2" required>{{ old('current_address') }}</textarea>
                            <span class="error-message" id="current_address_error"></span>
                            @error('current_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="permanent_address" class="form-label fw-semibold">Permanent Address</label>
                            <textarea class="form-control @error('permanent_address') is-invalid @enderror" id="permanent_address" name="permanent_address" rows="2">{{ old('permanent_address') }}</textarea>
                            <span class="error-message" id="permanent_address_error"></span>
                            @error('permanent_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="sameAddress">
                                <label class="form-check-label" for="sameAddress">Same as current address</label>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="section-title"><i class="bi bi-mortarboard me-2"></i>Academic Information</div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="program_id" class="form-label fw-semibold required-field">Program/Standard <span class="text-danger">*</span></label>
                                <select class="form-select @error('program_id') is-invalid @enderror" id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                    @endforeach
                                </select>
                                @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="division_id" class="form-label fw-semibold required-field">Division/Section <span class="text-danger">*</span></label>
                                <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id" required>
                                    <option value="">Select Division</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->division_name }}</option>
                                    @endforeach
                                </select>
                                @error('division_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="academic_session_id" class="form-label fw-semibold required-field">Academic Session <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_session_id') is-invalid @enderror" id="academic_session_id" name="academic_session_id" required>
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>{{ $session->session_name }}</option>
                                    @endforeach
                                </select>
                                @error('academic_session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="academic_year" class="form-label fw-semibold required-field">Academic Year <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" required>
                                    <option value="">Select Year</option>
                                    <option value="FY" {{ old('academic_year') == 'FY' ? 'selected' : '' }}>First Year (FY)</option>
                                    <option value="SY" {{ old('academic_year') == 'SY' ? 'selected' : '' }}>Second Year (SY)</option>
                                    <option value="TY" {{ old('academic_year') == 'TY' ? 'selected' : '' }}>Third Year (TY)</option>
                                </select>
                                @error('academic_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="admission_date" class="form-label fw-semibold required-field">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('admission_date') is-invalid @enderror" id="admission_date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required>
                                @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="student_status" class="form-label fw-semibold required-field">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('student_status') is-invalid @enderror" id="student_status" name="student_status" required>
                                    <option value="active" {{ old('student_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="graduated" {{ old('student_status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="dropped" {{ old('student_status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                    <option value="suspended" {{ old('student_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('student_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Documents Upload -->
                        <div class="section-title"><i class="bi bi-folder me-2"></i>Documents Upload</div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="photo" class="form-label fw-semibold">Student Photo</label>
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" onchange="previewFile(this, 'photoPreview')">
                                <img id="photoPreview" src="https://via.placeholder.com/150x200?text=Photo" class="img-thumbnail mt-2" style="width: 150px; height: 200px; object-fit: cover;">
                                <small class="text-muted">Max size: 2MB</small>
                                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="signature" class="form-label fw-semibold">Student Signature</label>
                                <input type="file" class="form-control @error('signature') is-invalid @enderror" id="signature" name="signature" accept="image/*" onchange="previewFile(this, 'signaturePreview')">
                                <img id="signaturePreview" src="https://via.placeholder.com/200x80?text=Signature" class="img-thumbnail mt-2" style="width: 200px; height: 80px; object-fit: cover;">
                                <small class="text-muted">Max size: 2MB</small>
                                @error('signature')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cast_certificate" class="form-label fw-semibold">Cast Certificate</label>
                                <input type="file" class="form-control @error('cast_certificate') is-invalid @enderror" id="cast_certificate" name="cast_certificate" accept=".pdf,image/*" onchange="showFileName(this, 'castFileName')">
                                <small id="castFileName" class="text-muted d-block"></small>
                                @error('cast_certificate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="marksheet" class="form-label fw-semibold">Previous Marksheet</label>
                                <input type="file" class="form-control @error('marksheet') is-invalid @enderror" id="marksheet" name="marksheet" accept=".pdf,image/*" onchange="showFileName(this, 'marksheetFileName')">
                                <small id="marksheetFileName" class="text-muted d-block"></small>
                                @error('marksheet')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5" style="border-radius: 10px;">
                                <i class="bi bi-check-circle me-2"></i>Create Student
                            </button>
                            <a href="{{ route('dashboard.students.index') }}" class="btn btn-secondary btn-lg px-4 ms-3" style="border-radius: 10px;">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('dashboard.students.index') }}" class="text-decoration-none text-primary fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i>Back to Students List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-title { color: #007bff; font-weight: 600; font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #007bff; }
.required-field::after { content: " *"; color: red; }
.form-control.error, .form-select.error { border-color: #dc3545 !important; box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important; }
.form-control.valid, .form-select.valid { border-color: #198754 !important; }
.error-message { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: none; }
.error-message.show { display: block; }
</style>

<script>
// Validation helper functions
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorSpan = document.getElementById(fieldId + '_error');
    field.classList.remove('valid'); field.classList.add('error');
    if (errorSpan) { errorSpan.textContent = message; errorSpan.classList.add('show'); }
}
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorSpan = document.getElementById(fieldId + '_error');
    field.classList.remove('error');
    if (errorSpan) { errorSpan.textContent = ''; errorSpan.classList.remove('show'); }
}
function showValid(fieldId) {
    const field = document.getElementById(fieldId);
    field.classList.remove('error'); field.classList.add('valid');
    const errorSpan = document.getElementById(fieldId + '_error');
    if (errorSpan) { errorSpan.textContent = ''; errorSpan.classList.remove('show'); }
}
function previewFile(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) { document.getElementById(previewId).src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}
function showFileName(input, spanId) {
    const fileName = input.files[0]?.name;
    document.getElementById(spanId).textContent = fileName || '';
}

// Same address checkbox
document.getElementById('sameAddress').addEventListener('change', function() {
    if (this.checked) { document.getElementById('permanent_address').value = document.getElementById('current_address').value; }
});

// Name validations
document.getElementById('first_name').addEventListener('input', function() {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
    if (this.value.trim().length < 2) showError('first_name', 'First name required (min 2 chars)');
    else showValid('first_name');
});
document.getElementById('last_name').addEventListener('input', function() {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
    if (this.value.trim().length < 2) showError('last_name', 'Last name required (min 2 chars)');
    else showValid('last_name');
});
document.getElementById('middle_name').addEventListener('input', function() {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
});

// Mobile validation - 10 digits starting with 6-9
document.getElementById('mobile_number').addEventListener('input', function() {
    this.value = this.value.replace(/[^\d]/g, '');
    if (this.value.length > 10) this.value = this.value.substring(0, 10);
    if (this.value.length > 0 && this.value.length < 10) showError('mobile_number', 'Must be 10 digits');
    else if (this.value.length === 10 && !/^[6-9]/.test(this.value)) showError('mobile_number', 'Must start with 6-9');
    else if (this.value.length === 10) showValid('mobile_number');
});
document.getElementById('mobile_number').addEventListener('blur', function() {
    if (this.value.length === 0) showError('mobile_number', 'Mobile number is required');
    else if (this.value.length < 10) showError('mobile_number', 'Must be exactly 10 digits');
    else if (!/^[6-9]\d{9}$/.test(this.value)) showError('mobile_number', 'Must start with 6-9');
    else clearError('mobile_number');
});

// Email validation
document.getElementById('email').addEventListener('blur', function() {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!pattern.test(this.value)) showError('email', 'Enter valid email');
    else showValid('email');
});

// Aadhar validation
document.getElementById('aadhar_number').addEventListener('input', function() {
    this.value = this.value.replace(/[^\d]/g, '');
    if (this.value.length > 12) this.value = this.value.substring(0, 12);
});

// Address validation
document.getElementById('current_address').addEventListener('blur', function() {
    if (this.value.trim().length < 10) showError('current_address', 'Min 10 characters required');
    else showValid('current_address');
});

// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    let isValid = true;
    const fields = [
        {id: 'first_name', check: () => document.getElementById('first_name').value.trim().length < 2, msg: 'First name required'},
        {id: 'last_name', check: () => document.getElementById('last_name').value.trim().length < 2, msg: 'Last name required'},
        {id: 'date_of_birth', check: () => !document.getElementById('date_of_birth').value, msg: 'Date of birth required'},
        {id: 'gender', check: () => !document.getElementById('gender').value, msg: 'Select gender'},
        {id: 'category', check: () => !document.getElementById('category').value, msg: 'Select category'},
        {id: 'email', check: () => !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(document.getElementById('email').value), msg: 'Valid email required'},
        {id: 'mobile_number', check: () => !/^[6-9]\d{9}$/.test(document.getElementById('mobile_number').value), msg: '10 digits starting with 6-9'},
        {id: 'current_address', check: () => document.getElementById('current_address').value.trim().length < 10, msg: 'Min 10 characters'}
    ];
    
    fields.forEach(f => {
        if (f.check()) { showError(f.id, f.msg); isValid = false; }
        else clearError(f.id);
    });
    
    if (!isValid) { e.preventDefault(); alert('Please correct the errors before submitting.'); }
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Accountant Profile')
@section('page-title', 'Accountant Profile')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                             style="width: 100px; height: 100px; font-size: 48px;">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge bg-primary">{{ ucfirst($user->roles->first()->name ?? 'Accountant') }}</span>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card shadow mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Role:</strong></td>
                            <td>{{ ucfirst($user->roles->first()->name ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Active:</strong></td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Change Password -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('accountant.change-password') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', this)" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="new_password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password', this)" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 6 characters</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation"
                                       class="form-control" id="confirm_password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password', this)" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
@endpush
@endsection

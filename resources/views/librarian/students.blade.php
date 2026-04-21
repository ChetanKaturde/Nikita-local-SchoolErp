@extends('librarian.layouts.app')

@section('title', 'Students List')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-people me-2 text-primary"></i>Students List</h2>
                    <p class="text-muted mb-0">View all students and their contact information</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or admission number..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('librarian.students') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-list me-2"></i>All Students</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Admission No</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Division</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td><strong>{{ $student->admission_number ?? 'N/A' }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($student->photo_path)
                                            <img src="{{ asset('storage/' . $student->photo_path) }}" class="rounded-circle me-2" width="40" height="40" alt="Photo">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                <strong>{{ strtoupper(substr($student->first_name, 0, 1)) }}</strong>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $student->email ?? $student->user->email ?? 'N/A' }}</td>
                                <td>{{ $student->mobile_number ?? 'N/A' }}</td>
                                <td>
                                    {{ $student->division->division_name ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $student->division->program->name ?? '' }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('librarian.student-details', $student) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fa-2x mb-2"></i>
                                        <h5>No students found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="card-footer bg-light">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('librarian.layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('librarian.students') }}">Students</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </nav>
                    <h2 class="mb-1">
                        <i class="bi bi-person-badge me-2 text-primary"></i>Student Details
                    </h2>
                </div>
                <a href="{{ route('librarian.students') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Student Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    @if($student->photo_path)
                        <img src="{{ asset('storage/' . $student->photo_path) }}" 
                             alt="{{ $student->first_name }}"
                             class="rounded-circle mb-3"
                             style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #667eea;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <span class="text-white fw-bold" style="font-size: 4rem;">
                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <h4 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h4>
                    <p class="text-muted mb-2">Admission No: {{ $student->admission_number }}</p>
                    <span class="badge bg-primary">{{ $student->division->division_name ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-telephone me-2 text-primary"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Email Address</label>
                        <div class="fw-semibold">{{ $student->email ?? $student->user->email ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Phone Number</label>
                        <div class="fw-semibold">{{ $student->mobile_number ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Division</label>
                        <div class="fw-semibold">{{ $student->division->division_name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $student->division->program->name ?? '' }}</small>
                    </div>
                </div>
            </div>

            <!-- Send Message -->
            <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-chat-dots me-2 text-primary"></i>Send Message
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('librarian.contact-student', $student) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Contact Method</label>
                            <select name="contact_method" class="form-select" required>
                                <option value="email">Email</option>
                                <option value="sms">SMS (if available)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="4" required placeholder="Enter your message here..."></textarea>
                            <small class="text-muted">Example: "Please return the book 'Introduction to Algorithms' by tomorrow."</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send me-1"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Issued Books History -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-book me-2 text-primary"></i>Issued Books History
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($issuedBooks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Book Title</th>
                                        <th>Author</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Return Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issuedBooks as $book)
                                        <tr>
                                            <td><strong>{{ $book->book->title ?? 'N/A' }}</strong></td>
                                            <td>{{ $book->book->author ?? 'N/A' }}</td>
                                            <td>{{ $book->issue_date->format('d M Y') }}</td>
                                            <td>
                                                {{ $book->due_date->format('d M Y') }}
                                                @if($book->due_date < now() && $book->status === 'issued')
                                                    <br><small class="text-danger"><i class="bi bi-exclamation-circle"></i> Overdue</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($book->return_date)
                                                    {{ $book->return_date->format('d M Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $book->status === 'issued' ? 'warning' : 'success' }}">
                                                    {{ ucfirst($book->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No books issued to this student</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

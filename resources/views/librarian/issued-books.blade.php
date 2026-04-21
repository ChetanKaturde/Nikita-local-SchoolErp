@extends('librarian.layouts.app')

@section('title', 'Issued Books')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Issued Books</h2>
                    <p class="text-muted mb-0">Track all issued and returned books</p>
                </div>
                <a href="{{ route('library.issues.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Issue New Book
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Student</label>
                    <select name="student_id" class="form-select">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('librarian.issued-books') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Issued Books Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-list me-2"></i>Book Issues</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Book</th>
                            <th>Student</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issuedBooks as $issue)
                            <tr>
                                <td>
                                    <strong>{{ $issue->book->title ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $issue->book->author ?? '' }}</small>
                                </td>
                                <td>
                                    {{ $issue->student->first_name ?? 'N/A' }} {{ $issue->student->last_name ?? '' }}
                                    <br><small class="text-muted">{{ $issue->student->admission_number ?? '' }}</small>
                                </td>
                                <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                <td>
                                    {{ $issue->due_date->format('d M Y') }}
                                    @if($issue->due_date < now() && $issue->status === 'issued')
                                        <br><small class="text-danger"><i class="bi bi-exclamation-circle"></i> Overdue</small>
                                    @endif
                                </td>
                                <td>
                                    @if($issue->return_date)
                                        {{ $issue->return_date->format('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $issue->status === 'issued' ? 'warning' : 'success' }}">
                                        {{ ucfirst($issue->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($issue->status === 'issued')
                                        <form action="{{ route('library.issues.return', $issue) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this book as returned?')">
                                                <i class="bi bi-check-circle"></i> Return
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted"><i class="bi bi-check-circle"></i> Returned</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fa-2x mb-2"></i>
                                        <h5>No book issues found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($issuedBooks->hasPages())
                <div class="card-footer bg-light">
                    {{ $issuedBooks->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

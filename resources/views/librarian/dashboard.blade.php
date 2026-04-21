@extends('librarian.layouts.app')

@section('title', 'Librarian Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold">
                                <i class="bi bi-person-badge me-2"></i>Welcome, {{ $librarian->name ?? 'Librarian' }}!
                            </h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-building me-1"></i>Library Management System |
                                <i class="bi bi-calendar me-1"></i>{{ now()->format('l, F d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-inline-flex align-items-center p-3 bg-white bg-opacity-20 rounded-3">
                                <i class="bi bi-clock me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <small class="opacity-75 d-block">Current Time</small>
                                    <strong id="currentTime">{{ now()->format('h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Books</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalBooks) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Available</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($availableBooks) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Issued</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($issuedBooks) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stats-card h-100 border-0" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Overdue</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($overdueBooks) }}</h2>
                        </div>
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Issued Books -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-clock-history me-2 text-primary"></i>Recent Issued Books
                        </h5>
                        <a href="{{ route('librarian.issued-books') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentIssues->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Book</th>
                                        <th>Student</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentIssues as $issue)
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
                                                <span class="badge bg-{{ $issue->status === 'issued' ? 'warning' : 'success' }}">
                                                    {{ ucfirst($issue->status) }}
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
                            <p class="text-muted mt-3">No issued books</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Overdue Books -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Overdue Books
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($overdueList->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($overdueList as $overdue)
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <strong class="mb-1">{{ $overdue->student->first_name ?? 'N/A' }} {{ $overdue->student->last_name ?? '' }}</strong>
                                        <span class="badge bg-danger">Overdue</span>
                                    </div>
                                    <p class="mb-1 small">{{ $overdue->book->title ?? 'N/A' }}</p>
                                    <small class="text-danger">
                                        <i class="bi bi-calendar-x me-1"></i>Due: {{ $overdue->due_date->format('d M Y') }}
                                        <br>
                                        <i class="bi bi-clock me-1"></i>{{ $overdue->due_date->diffForHumans() }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer bg-white border-0 py-3">
                            <a href="{{ route('librarian.issued-books', ['status' => 'issued']) }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-eye me-1"></i>View All Overdue
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <p class="text-success mt-3 mb-0">No overdue books!</p>
                            <small class="text-muted">All books returned on time</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('library.issues.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Issue Book
                        </a>
                        <a href="{{ route('library.books.index') }}" class="btn btn-outline-success">
                            <i class="bi bi-book me-2"></i>Manage Books
                        </a>
                        <a href="{{ route('librarian.students') }}" class="btn btn-outline-info">
                            <i class="bi bi-people me-2"></i>Students List
                        </a>
                        <a href="{{ route('librarian.issued-books') }}" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-left-right me-2"></i>Issued Books
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}
setInterval(updateTime, 1000);
updateTime();
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'All Results')

@section('styles')
<style>
    .pagination .page-link {
        color: #667eea;
        border-color: #e9ecef;
        padding: 0.5rem 0.75rem;
    }
    .pagination .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
    }
    .pagination .page-link:hover {
        color: #667eea;
        background-color: #f8f9fa;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.principal') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Results</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-clipboard-data me-2"></i>Student Results
                    </h2>
                    <p class="text-muted mb-0">View all student examination results across all divisions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-body">
            <form method="GET" action="{{ route('principal.results') }}">
                <div class="row">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="division_id" class="form-label">Select Division</label>
                            <select name="division_id" id="division_id" class="form-select">
                                <option value="">-- All Divisions --</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->division_name }} - {{ $division->program->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="examination_id" class="form-label">Select Examination</label>
                            <select name="examination_id" id="examination_id" class="form-select">
                                <option value="">-- Select Examination --</option>
                                @foreach($examinations as $exam)
                                    <option value="{{ $exam->id }}" {{ request('examination_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ ucfirst($exam->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3 d-grid">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    @if($selectedDivision && $selectedExam)
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-table me-2 text-primary"></i>
                    Results: {{ $selectedDivision->division_name }} - {{ $selectedExam->name }}
                </h5>
                <div>
                    <span class="badge bg-primary">{{ $students->total() }} Students (Page {{ $students->currentPage() }} of {{ $students->lastPage() }})</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No.</th>
                                <th>Student Name</th>
                                <th class="text-center">Subjects</th>
                                <th class="text-center">Total Marks</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $studentMarks = $results->where('student_id', $student->id);
                                    $subjectsCount = $studentMarks->count();
                                    $totalMarks = $studentMarks->sum('marks_obtained');
                                    $totalMaxMarks = $studentMarks->sum('max_marks');
                                    $hasMarks = $subjectsCount > 0;
                                    $percentage = $hasMarks && $totalMaxMarks > 0 ? ($totalMarks / $totalMaxMarks) * 100 : null;
                                    $passPercentage = 40;
                                    $isPass = $hasMarks && $percentage !== null && $percentage >= $passPercentage;
                                    
                                    // Calculate grade only if marks exist
                                    $grade = '';
                                    if ($hasMarks && $percentage !== null) {
                                        if ($percentage >= 90) $grade = 'A+';
                                        elseif ($percentage >= 80) $grade = 'A';
                                        elseif ($percentage >= 70) $grade = 'B+';
                                        elseif ($percentage >= 60) $grade = 'B';
                                        elseif ($percentage >= 50) $grade = 'C';
                                        elseif ($percentage >= 40) $grade = 'D';
                                        else $grade = 'F';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $student->roll_number }}</td>
                                    <td>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    </td>
                                    <td class="text-center">{{ $subjectsCount }}</td>
                                    <td class="text-center">
                                        @if($hasMarks)
                                            {{ $totalMarks }} / {{ $totalMaxMarks }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($hasMarks && $percentage !== null)
                                            <span class="badge bg-{{ $isPass ? 'success' : 'danger' }}">
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($hasMarks)
                                            <span class="badge bg-{{ $isPass ? 'success' : 'danger' }}">{{ $grade }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$hasMarks)
                                            <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                                        @elseif($isPass)
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Pass</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Fail</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->total() > 0)
                    <div class="mt-4">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{-- Previous Page Link --}}
                                @if ($students->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="bi bi-chevron-left"></i> Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i> Previous</a>
                                    </li>
                                @endif

                                {{-- Simple page number links --}}
                                @for ($i = 1; $i <= $students->lastPage(); $i++)
                                    @if ($i == 1 || $i == $students->lastPage() || abs($i - $students->currentPage()) <= 1)
                                        @if ($i == $students->currentPage())
                                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $students->url($i) }}">{{ $i }}</a></li>
                                        @endif
                                    @elseif ($i == $students->currentPage() - 2 || $i == $students->currentPage() + 2)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                @endfor

                                {{-- Next Page Link --}}
                                @if ($students->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next">Next <i class="bi bi-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next <i class="bi bi-chevron-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results
                            </small>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No students found in this division</p>
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="card-body text-center py-5">
            <i class="bi bi-clipboard-data text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">Select a division and examination to view results</h5>
            <p class="text-muted">Use the filters above to search for student results</p>
        </div>
    </div>
    @endif
</div>
@endsection

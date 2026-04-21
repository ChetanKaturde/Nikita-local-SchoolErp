@extends('student.layouts.app')

@section('title', 'My Results')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1"><i class="bi bi-clipboard-data me-2 text-primary"></i>My Results</h2>
            <p class="text-muted mb-0">View your examination results and grades</p>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-award me-2 text-primary"></i>Examination Results</h5>
        </div>
        <div class="card-body">
            @if($results->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Examination</th>
                                <th class="text-center">Marks Obtained</th>
                                <th class="text-center">Total Marks</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tr>
                                    <td>
                                        <strong>{{ $result->subject->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $result->examination->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <strong>{{ $result->marks_obtained }}</strong>
                                    </td>
                                    <td class="text-center">{{ $result->total_marks }}</td>
                                    <td class="text-center">
                                        @php
                                            // Handle case where total_marks might be 0 or null
                                            $percentage = 0;
                                            $grade = '';
                                            if ($result->total_marks > 0 && $result->marks_obtained !== null) {
                                                $percentage = ($result->marks_obtained / $result->total_marks) * 100;
                                                
                                                if ($percentage >= 90) $grade = 'A+';
                                                elseif ($percentage >= 80) $grade = 'A';
                                                elseif ($percentage >= 70) $grade = 'B+';
                                                elseif ($percentage >= 60) $grade = 'B';
                                                elseif ($percentage >= 50) $grade = 'C';
                                                elseif ($percentage >= 40) $grade = 'D';
                                                else $grade = 'F';
                                            } else {
                                                $grade = 'N/A';
                                            }
                                            
                                            $gradeClass = $percentage >= 60 ? 'success' : ($percentage >= 40 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $gradeClass }}">{{ $grade }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($result->total_marks > 0 && $result->marks_obtained !== null && $percentage >= 40)
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
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No results available yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

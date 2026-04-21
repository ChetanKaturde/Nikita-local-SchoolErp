@extends('layouts.app')

@section('title', 'Examinations')
@section('page-title', 'Examinations')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm" style="border: none; border-radius: 16px; overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 16px 24px;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white fw-bold">
                    <i class="fas fa-clipboard-check me-2"></i>Examinations
                </h5>
                <a href="{{ route('examinations.create') }}" class="btn btn-light btn-sm fw-bold">
                    <i class="bi bi-plus-circle me-1"></i>Create Exam
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-bold text-dark">Exam Name</th>
                            <th class="border-0 py-3 fw-bold text-dark">Subject</th>
                            <th class="border-0 py-3 fw-bold text-dark">Code</th>
                            <th class="border-0 py-3 fw-bold text-dark">Type</th>
                            <th class="border-0 py-3 fw-bold text-dark">Start Date</th>
                            <th class="border-0 py-3 fw-bold text-dark">End Date</th>
                            <th class="border-0 py-3 fw-bold text-dark">Status</th>
                            <th class="border-0 py-3 fw-bold text-dark text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examinations as $exam)
                        <tr style="transition: all 0.2s;">
                            <td class="px-4 py-3">
                                <strong class="text-dark">{{ $exam->name }}</strong>
                            </td>
                            <td class="py-3">
                                @if($exam->subject)
                                    <span class="badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                        {{ $exam->subject->name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3">{{ $exam->code ?? 'N/A' }}</td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark">{{ ucfirst($exam->type) }}</span>
                            </td>
                            <td class="py-3">{{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }}</td>
                            <td class="py-3">{{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</td>
                            <td class="py-3">
                                <span class="badge bg-{{ $exam->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('examinations.show', $exam) }}" class="btn btn-sm" style="background: #e3f2fd; color: #1976d2;" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('examinations.edit', $exam) }}" class="btn btn-sm" style="background: #fff3e0; color: #f57c00;" title="Edit">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('examinations.destroy', $exam) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background: #ffebee; color: #c62828;" onclick="return confirm('Delete this exam?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="text-muted">No examinations found</h5>
                                <p class="text-muted">Create your first examination to get started</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($examinations->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <span class="text-muted mb-2 mb-md-0">
                        Showing <strong>{{ $examinations->firstItem() ?? 0 }}</strong> to <strong>{{ $examinations->lastItem() ?? 0 }}</strong> of <strong>{{ $examinations->total() }}</strong> examinations
                    </span>
                    <nav aria-label="Exam pagination">
                        <ul class="pagination pagination-sm mb-0">
                            {{ $examinations->links('pagination::bootstrap-4') }}
                        </ul>
                    </nav>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.pagination { margin: 0; }
.page-link {
    border: none;
    color: #667eea;
    padding: 8px 12px;
    margin: 0 2px;
    border-radius: 8px;
    font-weight: 500;
}
.page-link:hover {
    background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
    color: #764ba2;
}
.page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.page-item.disabled .page-link {
    color: #6c757d;
    background: transparent;
}
.table tr:hover {
    background-color: #f8f9fa !important;
}
</style>
@endsection

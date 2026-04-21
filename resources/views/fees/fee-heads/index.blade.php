@extends('layouts.app')

@section('title', 'Fee Heads')
@section('page-title', 'Fee Heads')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Fee Heads</h5>
                    <a href="{{ route('fees.fee-heads.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add New Fee Head
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('fees.fee-heads.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by name or code..." 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('fees.fee-heads.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('fees.fee-heads.index') }}" class="d-flex justify-content-end gap-2">
                                <select name="per_page" class="form-select" style="width: auto;" onchange="this.form.submit()">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 per page</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Fee Heads Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Refundable</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeHeads as $feeHead)
                                    <tr>
                                        <td>{{ $feeHead->id }}</td>
                                        <td>{{ $feeHead->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $feeHead->code }}</span></td>
                                        <td>{{ $feeHead->description ?? '-' }}</td>
                                        <td>
                                            @if($feeHead->is_refundable)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning text-dark">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($feeHead->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('fees.fee-heads.edit', $feeHead->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('fees.fee-heads.destroy', $feeHead->id) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this fee head?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                No fee heads found.
                                                <a href="{{ route('fees.fee-heads.create') }}">Create one</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $feeHeads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

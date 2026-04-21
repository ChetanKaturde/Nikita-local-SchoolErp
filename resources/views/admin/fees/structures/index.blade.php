@extends('layouts.app')

@section('title', 'Fee Structures')
@section('page-title', 'Fee Structure Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Fee Structures</h4>
        <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Fee Structure
        </a>
    </div>

    <!-- Fee Structures Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Program</th>
                            <th>Division</th>
                            <th>Academic Year</th>
                            <th>Fee Head</th>
                            <th>Amount</th>
                            <th>Installments</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $structure)
                        <tr>
                            <td>{{ $structure->id }}</td>
                            <td>{{ $structure->program?->name ?? 'N/A' }}</td>
                            <td>{{ $structure->division?->name ?? 'All Divisions' }}</td>
                            <td>{{ $structure->academic_year }}</td>
                            <td>{{ $structure->feeHead?->name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($structure->amount, 2) }}</td>
                            <td>{{ $structure->installments }}</td>
                            <td>
                                @if($structure->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Edit</a></li>
                                        <li><a class="dropdown-item" href="#">View</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No fee structures found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $structures->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Create Fee Structure')
@section('page-title', 'Create New Fee Structure')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Fee Structure Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.structures.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="program_id" class="form-label">Program <span class="text-danger">*</span></label>
                            <select class="form-select" id="program_id" name="program_id" required>
                                <option value="">Select Program</option>
                                @foreach($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="division_id" class="form-label">Division (Optional)</label>
                            <select class="form-select" id="division_id" name="division_id">
                                <option value="">All Divisions ({{ $divisions->count() }} found)</option>
                                @forelse($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->division_name }}</option>
                                @empty
                                <option value="">No divisions available</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                   placeholder="e.g., 2024-25" required>
                        </div>

                        <div class="mb-3">
                            <label for="fee_head_id" class="form-label">Fee Head <span class="text-danger">*</span></label>
                            <select class="form-select" id="fee_head_id" name="fee_head_id" required>
                                <option value="">Select Fee Head</option>
                                @forelse($feeHeads as $feeHead)
                                <option value="{{ $feeHead->id }}">{{ $feeHead->name }}</option>
                                @empty
                                <option value="">No fee heads available</option>
                                @endforelse
                            </select>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFeeHeadModal">
                                    <i class="bi bi-plus-circle"></i> Add New Fee Head
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0" placeholder="0.00" required>
                        </div>

                        <div class="mb-3">
                            <label for="installments" class="form-label">Number of Installments</label>
                            <select class="form-select" id="installments" name="installments">
                                <option value="1">One Time</option>
                                <option value="2">2 Installments</option>
                                <option value="3">3 Installments</option>
                                <option value="4">4 Installments</option>
                                <option value="5">5 Installments</option>
                                <option value="6">6 Installments (Monthly)</option>
                                <option value="10">10 Installments (Monthly)</option>
                                <option value="12">12 Installments (Monthly)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="frequency" class="form-label">Frequency <span class="text-danger">*</span></label>
                            <select class="form-select" id="frequency" name="frequency" required>
                                <option value="once">One Time</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="half_yearly">Half Yearly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" placeholder="Optional description"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.fees.structures') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Fee Structure</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Fee Head Modal -->
<div class="modal fade" id="addFeeHeadModal" tabindex="-1" aria-labelledby="addFeeHeadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFeeHeadModalLabel">Add New Fee Head</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addFeeHeadForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fee_head_name" class="form-label">Fee Head Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fee_head_name" name="name" 
                               placeholder="e.g., Tuition Fee, Lab Fee" required>
                    </div>
                    <div class="mb-3">
                        <label for="fee_head_code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fee_head_code" name="code" 
                               placeholder="e.g., TF, LF" required maxlength="10">
                    </div>
                    <div class="mb-3">
                        <label for="fee_head_description" class="form-label">Description</label>
                        <textarea class="form-control" id="fee_head_description" name="description" 
                                  rows="2" placeholder="Optional description"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_refundable" name="is_refundable" value="1">
                            <label class="form-check-label" for="is_refundable">
                                Is Refundable
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Fee Head</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('addFeeHeadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Adding...';
    
    fetch('{{ route('admin.fees.fee-heads.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new option to the select
            const select = document.getElementById('fee_head_id');
            const option = document.createElement('option');
            option.value = data.feeHead.id;
            option.textContent = data.feeHead.name;
            select.appendChild(option);
            
            // Select the new option
            select.value = data.feeHead.id;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addFeeHeadModal'));
            modal.hide();
            
            // Reset form
            this.reset();
            
            // Show success message
            alert('Fee Head added successfully!');
        } else {
            alert('Error: ' + (data.message || 'Something went wrong'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the fee head.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endpush
@endsection
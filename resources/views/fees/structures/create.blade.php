@extends('layouts.app')

@section('title', 'Create Fee Structure')
@section('page-title', 'Create Fee Structure')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Create Fee Structure</h5>
                    <a href="{{ route('fees.fee-heads.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-gear me-1"></i> Manage Fee Heads
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fees.structures.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Program <span class="text-danger">*</span></label>
                                    <select name="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fee Head <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="fee_head_id" id="fee_head_select" class="form-select @error('fee_head_id') is-invalid @enderror" required>
                                            <option value="">Select Fee Head</option>
                                            @foreach($feeHeads as $feeHead)
                                                <option value="{{ $feeHead->id }}" {{ old('fee_head_id') == $feeHead->id ? 'selected' : '' }}>
                                                    {{ $feeHead->name }} ({{ $feeHead->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feeHeadModal" title="Add New Fee Head">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    @error('fee_head_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" class="form-control @error('academic_year') is-invalid @enderror" 
                                           value="{{ old('academic_year', '2024-25') }}" placeholder="2024-25" required>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount') }}" step="0.01" min="0" placeholder="5000.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Installments <span class="text-danger">*</span></label>
                                    <select name="installments" class="form-select @error('installments') is-invalid @enderror" required>
                                        <option value="1" {{ old('installments') == 1 ? 'selected' : '' }}>1 (Full Payment)</option>
                                        <option value="2" {{ old('installments') == 2 ? 'selected' : '' }}>2 Installments</option>
                                        <option value="3" {{ old('installments') == 3 ? 'selected' : '' }}>3 Installments</option>
                                        <option value="4" {{ old('installments') == 4 ? 'selected' : '' }}>4 Installments</option>
                                    </select>
                                    @error('installments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Fee Structure
                            </button>
                            <a href="{{ route('fees.structures.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fee Head Modal -->
<div class="modal fade" id="feeHeadModal" tabindex="-1" aria-labelledby="feeHeadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="feeHeadModalLabel">Add New Fee Head</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="feeHeadForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="fee_head_name" class="form-control" placeholder="e.g., Tuition Fee" required>
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="fee_head_code" class="form-control" placeholder="e.g., TUFEE" maxlength="10" required>
                        <div class="invalid-feedback" id="code_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="fee_head_description" class="form-control" rows="2" placeholder="Optional description..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" name="is_refundable" id="fee_head_is_refundable" class="form-check-input" value="1">
                                <label class="form-check-label" for="fee_head_is_refundable">Refundable</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="fee_head_is_active" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="fee_head_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveFeeHeadBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Save Fee Head
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Fee Head Modal -->
<div class="modal fade" id="editFeeHeadModal" tabindex="-1" aria-labelledby="editFeeHeadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editFeeHeadModalLabel">Edit Fee Head</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFeeHeadForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_fee_head_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_fee_head_name" class="form-control" required>
                        <div class="invalid-feedback" id="edit_name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit_fee_head_code" class="form-control" maxlength="10" required>
                        <div class="invalid-feedback" id="edit_code_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_fee_head_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" name="is_refundable" id="edit_fee_head_is_refundable" class="form-check-input" value="1">
                                <label class="form-check-label" for="edit_fee_head_is_refundable">Refundable</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="edit_fee_head_is_active" class="form-check-input" value="1" checked>
                                <label class="form-check-label" for="edit_fee_head_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteFeeHeadBtn">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Update Fee Head
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const feeHeadSelect = document.getElementById('fee_head_select');
    const feeHeadModal = new bootstrap.Modal(document.getElementById('feeHeadModal'));
    const editFeeHeadModal = new bootstrap.Modal(document.getElementById('editFeeHeadModal'));
    const feeHeadForm = document.getElementById('feeHeadForm');
    const editFeeHeadForm = document.getElementById('editFeeHeadForm');
    
    // Store all fee heads for inline editing
    let allFeeHeads = @json($feeHeads);
    
    // Function to refresh fee head dropdown
    function refreshFeeHeads() {
        fetch('{{ route("fees.fee-heads.active") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allFeeHeads = data.feeHeads;
                    const currentValue = feeHeadSelect.value;
                    feeHeadSelect.innerHTML = '<option value="">Select Fee Head</option>';
                    data.feeHeads.forEach(fh => {
                        const option = document.createElement('option');
                        option.value = fh.id;
                        option.textContent = `${fh.name} (${fh.code})`;
                        feeHeadSelect.appendChild(option);
                    });
                    if (currentValue) {
                        feeHeadSelect.value = currentValue;
                    }
                }
            })
            .catch(error => console.error('Error fetching fee heads:', error));
    }
    
    // Handle Create Fee Head Form Submit
    feeHeadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveFeeHeadBtn');
        const spinner = saveBtn.querySelector('.spinner-border');
        
        // Clear previous errors
        document.getElementById('name_error').textContent = '';
        document.getElementById('code_error').textContent = '';
        document.getElementById('fee_head_name').classList.remove('is-invalid');
        document.getElementById('fee_head_code').classList.remove('is-invalid');
        
        saveBtn.disabled = true;
        spinner.classList.remove('d-none');
        
        const formData = new FormData(feeHeadForm);
        
        fetch('{{ route("fees.fee-heads.store.ajax") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            saveBtn.disabled = false;
            spinner.classList.add('d-none');
            
            if (data.success) {
                // Refresh dropdown
                refreshFeeHeads();
                
                // Select the newly created fee head
                feeHeadSelect.value = data.feeHead.id;
                
                // Close modal and reset form
                feeHeadModal.hide();
                feeHeadForm.reset();
                document.getElementById('fee_head_is_active').checked = true;
                
                // Show success message
                alert('Fee Head created successfully!');
            } else {
                if (data.errors) {
                    if (data.errors.name) {
                        document.getElementById('fee_head_name').classList.add('is-invalid');
                        document.getElementById('name_error').textContent = data.errors.name[0];
                    }
                    if (data.errors.code) {
                        document.getElementById('fee_head_code').classList.add('is-invalid');
                        document.getElementById('code_error').textContent = data.errors.code[0];
                    }
                }
            }
        })
        .catch(error => {
            saveBtn.disabled = false;
            spinner.classList.add('d-none');
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    // Handle Edit button click on dropdown options
    feeHeadSelect.addEventListener('dblclick', function() {
        const selectedValue = this.value;
        if (!selectedValue) return;
        
        const feeHead = allFeeHeads.find(fh => fh.id == selectedValue);
        if (!feeHead) return;
        
        // Populate edit form
        document.getElementById('edit_fee_head_id').value = feeHead.id;
        document.getElementById('edit_fee_head_name').value = feeHead.name;
        document.getElementById('edit_fee_head_code').value = feeHead.code;
        document.getElementById('edit_fee_head_description').value = feeHead.description || '';
        document.getElementById('edit_fee_head_is_refundable').checked = feeHead.is_refundable;
        document.getElementById('edit_fee_head_is_active').checked = feeHead.is_active;
        
        editFeeHeadModal.show();
    });
    
    // Add edit button to existing options (right-click context)
    feeHeadSelect.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        const selectedValue = this.value;
        if (!selectedValue) return;
        
        const feeHead = allFeeHeads.find(fh => fh.id == selectedValue);
        if (!feeHead) return;
        
        // Populate edit form
        document.getElementById('edit_fee_head_id').value = feeHead.id;
        document.getElementById('edit_fee_head_name').value = feeHead.name;
        document.getElementById('edit_fee_head_code').value = feeHead.code;
        document.getElementById('edit_fee_head_description').value = feeHead.description || '';
        document.getElementById('edit_fee_head_is_refundable').checked = feeHead.is_refundable;
        document.getElementById('edit_fee_head_is_active').checked = feeHead.is_active;
        
        editFeeHeadModal.show();
    });
    
    // Handle Update Fee Head Form Submit
    editFeeHeadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const feeHeadId = document.getElementById('edit_fee_head_id').value;
        const submitBtn = this.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');
        
        // Clear previous errors
        document.getElementById('edit_name_error').textContent = '';
        document.getElementById('edit_code_error').textContent = '';
        document.getElementById('edit_fee_head_name').classList.remove('is-invalid');
        document.getElementById('edit_fee_head_code').classList.remove('is-invalid');
        
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        
        const formData = new FormData(editFeeHeadForm);
        
        fetch(`{{ route("fees.fee-heads.update.ajax.post", ["feeHead" => ":id"]) }}`.replace(':id', feeHeadId), {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            }
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            
            if (data.success) {
                // Refresh dropdown
                refreshFeeHeads();
                
                // Select the updated fee head
                feeHeadSelect.value = data.feeHead.id;
                
                // Close modal
                editFeeHeadModal.hide();
                
                // Show success message
                alert('Fee Head updated successfully!');
            } else {
                if (data.errors) {
                    if (data.errors.name) {
                        document.getElementById('edit_fee_head_name').classList.add('is-invalid');
                        document.getElementById('edit_name_error').textContent = data.errors.name[0];
                    }
                    if (data.errors.code) {
                        document.getElementById('edit_fee_head_code').classList.add('is-invalid');
                        document.getElementById('edit_code_error').textContent = data.errors.code[0];
                    }
                }
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    // Handle Delete Fee Head
    document.getElementById('deleteFeeHeadBtn').addEventListener('click', function() {
        const feeHeadId = document.getElementById('edit_fee_head_id').value;
        
        if (!confirm('Are you sure you want to delete this fee head? This action cannot be undone.')) {
            return;
        }
        
        fetch(`{{ route("fees.fee-heads.destroy.ajax.post", ["feeHead" => ":id"]) }}`.replace(':id', feeHeadId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh dropdown
                refreshFeeHeads();
                
                // Close modal
                editFeeHeadModal.hide();
                
                // Show success message
                alert('Fee Head deleted successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    // Reset forms when modals are closed
    document.getElementById('feeHeadModal').addEventListener('hidden.bs.modal', function() {
        feeHeadForm.reset();
        document.getElementById('fee_head_is_active').checked = true;
        document.getElementById('fee_head_name').classList.remove('is-invalid');
        document.getElementById('fee_head_code').classList.remove('is-invalid');
    });
    
    document.getElementById('editFeeHeadModal').addEventListener('hidden.bs.modal', function() {
        editFeeHeadForm.reset();
        document.getElementById('edit_fee_head_name').classList.remove('is-invalid');
        document.getElementById('edit_fee_head_code').classList.remove('is-invalid');
    });
});
</script>
@endpush
@endsection

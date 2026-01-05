@extends('layouts.app')

@section('title', 'Create New Petty Cash Request')

@section('breadcrumb')
<div class="db-breadcrumb">
    <h4 class="breadcrumb-title">Create New Petty Cash Request</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('petty-cash.index') }}">Petty Cash Dashboard</a></li>
            <li class="breadcrumb-item active">Create New Request</li>
        </ol>
    </nav>
</div>
@endsection

@push('styles')
<style>
    .create-card {
        @apply rounded-2xl shadow-lg border-0 transition-all duration-300;
        backdrop-filter: blur(10px);
    }
    
    .form-section {
        @apply bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 mb-6 border border-gray-200;
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        @apply shadow-md;
        transform: translateY(-2px);
    }
    
    .form-label {
        @apply font-semibold text-gray-700 mb-2 block;
    }
    
    .form-control:focus, .form-select:focus {
        @apply border-primary ring-2 ring-primary/20 outline-none;
        transition: all 0.2s ease;
    }
    
    .form-control, .form-select {
        @apply transition-all duration-200;
    }
    
    .form-control:hover, .form-select:hover {
        @apply border-gray-400;
    }
    
    .table {
        @apply rounded-lg overflow-hidden;
    }
    
    .table thead {
        @apply bg-gradient-to-r from-primary to-primary-dark text-white;
    }
    
    .table tbody tr {
        @apply transition-colors duration-150;
    }
    
    .table tbody tr:hover {
        @apply bg-gray-50;
    }
    
    .btn-primary {
        @apply bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(148, 0, 0, 0.3);
    }
    
    .file-upload-area {
        @apply border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .file-upload-area:hover {
        @apply border-primary bg-gradient-to-br from-primary/5 to-primary/10;
        transform: scale(1.02);
    }
    
    .alert {
        @apply rounded-xl border-l-4 shadow-md;
    }
    
    .spinner-border-sm {
        @apply animate-spin;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-primary">
        <div class="card-body text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="fw-bold mb-2 text-white">
                        <i class="bx bx-plus-circle me-2"></i>Create New Petty Cash Request
                    </h2>
                    <p class="mb-0 opacity-90">Fill in the details below to create a new petty cash request</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('petty-cash.index') }}" class="btn btn-light btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        $hasActiveRequest = \App\Models\PettyCashVoucher::where('created_by', auth()->id())
            ->whereNotIn('status', ['retired', 'rejected'])
            ->exists();
        $activeVoucher = null;
        if ($hasActiveRequest) {
            $activeVoucher = \App\Models\PettyCashVoucher::where('created_by', auth()->id())
                ->whereNotIn('status', ['retired', 'rejected'])
                ->orderBy('created_at', 'desc')
                ->first();
        }
    @endphp

    @if($hasActiveRequest && $activeVoucher)
    <!-- Warning Alert -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="bx bx-error-circle me-2"></i>Active Request Found</h5>
        <p class="mb-2">You have an active petty cash request that must be completed before creating a new one:</p>
        <ul class="mb-2">
            <li><strong>Voucher #:</strong> {{ $activeVoucher->voucher_no }}</li>
            <li><strong>Status:</strong> <span class="badge bg-{{ $activeVoucher->status_badge_class }}">{{ ucwords(str_replace('_', ' ', $activeVoucher->status)) }}</span></li>
            <li><strong>Amount:</strong> TZS {{ number_format($activeVoucher->amount, 2) }}</li>
            <li><strong>Created:</strong> {{ $activeVoucher->created_at->format('M d, Y') }}</li>
        </ul>
        <hr>
        <p class="mb-0">
            <a href="{{ route('petty-cash.show', $activeVoucher->id) }}" class="btn btn-sm btn-primary me-2">
                <i class="bx bx-show me-1"></i>View Active Request
            </a>
            <a href="{{ route('petty-cash.my-requests') }}" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-list-ul me-1"></i>My Requests
            </a>
        </p>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bx bx-error-circle me-2"></i>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Form Card -->
    <div class="card border-0 shadow-sm create-card">
        <div class="card-body p-4">
            <form id="pettyCashCreateForm" action="{{ route('petty-cash.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="bx bx-info-circle me-2 text-primary"></i>Basic Information
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control form-control-lg" name="date" id="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                Payee <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" name="payee" id="payee" value="{{ auth()->user()->name }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                Purpose <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="purpose" id="purpose" rows="2" required placeholder="Brief description of the request"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Expense Details Section -->
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="bx bx-list-ul me-2 text-success"></i>Expense Details
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="expense-lines-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Description <span class="text-danger">*</span></th>
                                    <th style="width: 18%;">Qty</th>
                                    <th style="width: 18%;">Unit Price</th>
                                    <th style="width: 15%;">Total</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="line_description[]" class="form-control form-control-sm" required placeholder="Item description"></td>
                                    <td><input type="number" name="line_qty[]" class="form-control form-control-sm line-qty" step="0.01" value="1" required></td>
                                    <td><input type="number" name="line_unit_price[]" class="form-control form-control-sm line-unit-price" step="0.01" value="0.00" required></td>
                                    <td><input type="text" class="form-control form-control-sm line-total" readonly></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-line"><i class="bx bx-minus"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                                    <td><input type="text" id="grand-total" class="form-control form-control-sm" readonly></td>
                                    <td><button type="button" class="btn btn-primary btn-sm add-line"><i class="bx bx-plus"></i></button></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="bx bx-paperclip me-2 text-info"></i>Supporting Documents (Optional)
                    </h5>
                    <div class="file-upload-area mb-3" id="fileUploadArea" style="cursor: pointer;">
                        <input type="file" name="attachments[]" id="fileInput" class="d-none" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="previewFiles(this)">
                        <div id="filePlaceholder">
                            <i class="bx bx-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                            <p class="mb-2 mt-3">
                                <strong>Click to upload</strong> or drag and drop
                            </p>
                            <p class="text-muted small mb-0">
                                PDF, JPG, PNG (Max 10MB per file)
                            </p>
                        </div>
                        <div id="filePreview" class="d-none mt-3">
                            <div id="fileList" class="list-group"></div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="{{ route('petty-cash.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bx bx-x me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" {{ $hasActiveRequest ? 'disabled' : '' }}>
                        <i class="bx bx-check-circle me-1"></i>Submit Request
                    </button>
                </div>
                @if($hasActiveRequest)
                <div class="alert alert-info mt-3">
                    <i class="bx bx-info-circle me-2"></i>You cannot create a new request while you have an active request. Please wait until your current request is retired or rejected.
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pettyCashCreateForm');
    const table = document.getElementById('expense-lines-table');
    
    // Calculate line total
    function calculateLineTotal(row) {
        const qty = parseFloat(row.querySelector('.line-qty').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.line-unit-price').value) || 0;
        const total = qty * unitPrice;
        row.querySelector('.line-total').value = total.toFixed(2);
        calculateGrandTotal();
    }
    
    // Calculate grand total
    function calculateGrandTotal() {
        let total = 0;
        table.querySelectorAll('tbody tr').forEach(row => {
            const lineTotal = parseFloat(row.querySelector('.line-total').value) || 0;
            total += lineTotal;
        });
        document.getElementById('grand-total').value = total.toFixed(2);
    }
    
    // Add line
    table.querySelector('.add-line').addEventListener('click', function() {
        const tbody = table.querySelector('tbody');
        const newRow = tbody.querySelector('tr').cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => {
            if (input.type !== 'button') {
                input.value = '';
            }
        });
        newRow.querySelector('.line-total').value = '0.00';
        tbody.appendChild(newRow);
    });
    
    // Remove line
    table.addEventListener('click', function(e) {
        if (e.target.closest('.remove-line')) {
            const row = e.target.closest('tr');
            if (table.querySelectorAll('tbody tr').length > 1) {
                row.remove();
                calculateGrandTotal();
            } else {
                alert('You must have at least one expense line.');
            }
        }
    });
    
    // Calculate on input
    table.addEventListener('input', function(e) {
        if (e.target.classList.contains('line-qty') || e.target.classList.contains('line-unit-price')) {
            calculateLineTotal(e.target.closest('tr'));
        }
    });
    
    // Initial calculation
    calculateGrandTotal();
    
    // File upload handling
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('fileInput');
    const filePlaceholder = document.getElementById('filePlaceholder');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    
    fileUploadArea.addEventListener('click', function(e) {
        if (e.target !== fileInput && !e.target.closest('#fileList')) {
            fileInput.click();
        }
    });
    
    function previewFiles(input) {
        const files = input.files;
        if (!files || files.length === 0) {
            filePlaceholder.classList.remove('d-none');
            filePreview.classList.add('d-none');
            return;
        }
        
        filePlaceholder.classList.add('d-none');
        filePreview.classList.remove('d-none');
        
        let html = '';
        Array.from(files).forEach((file, index) => {
            const size = (file.size / 1024 / 1024).toFixed(2);
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bx bx-file me-2"></i>
                        <strong>${file.name}</strong>
                        <small class="text-muted ms-2">(${size} MB)</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-file-btn" data-index="${index}">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
            `;
        });
        fileList.innerHTML = html;
    }
    
    // Remove file
    fileList.addEventListener('click', function(e) {
        if (e.target.closest('.remove-file-btn')) {
            const index = parseInt(e.target.closest('.remove-file-btn').dataset.index);
            const dt = new DataTransfer();
            const files = Array.from(fileInput.files);
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
            previewFiles(fileInput);
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting...';
        
        // Validate at least one line has valid data
        let hasValidLine = false;
        table.querySelectorAll('tbody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.line-qty').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.line-unit-price').value) || 0;
            if (qty > 0 && unitPrice > 0) {
                hasValidLine = true;
            }
        });
        
        if (!hasValidLine) {
            e.preventDefault();
            alert('Please add at least one expense line with valid quantity and unit price.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endpush


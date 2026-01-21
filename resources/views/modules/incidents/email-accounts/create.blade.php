@extends('layouts.app')

@section('title', 'Add Email Account - Incident Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    .form-section {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .form-section h5 {
        color: #ffc107;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ffc107;
    }
    .quick-setup-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .quick-setup-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-warning" style="border-radius: 15px;">
                <div class="card-body text-dark p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="mb-2 text-dark fw-bold">
                                <i class="bx bx-envelope me-2"></i>Add Email Account
                            </h3>
                            <p class="mb-0 text-dark-50">Configure a new email account for incident synchronization</p>
                        </div>
                        <div class="d-flex gap-2 mt-3 mt-md-0">
                            <a href="{{ route('modules.incidents.email.accounts') }}" class="btn btn-dark btn-lg shadow-sm">
                                <i class="bx bx-arrow-back me-1"></i>Back to Accounts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Setup Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex justify-content-between align-items-center" style="border-radius: 12px;">
                <div>
                    <i class="bx bx-zap me-2"></i>
                    <strong>Quick Setup:</strong> Click the button below to auto-fill settings for david.ngungila@emca.tech
                </div>
                <button type="button" class="btn quick-setup-btn" onclick="quickSetupGmail()">
                    <i class="bx bx-zap me-1"></i>Quick Setup
                </button>
            </div>
        </div>
    </div>

    <!-- Add Email Account Form -->
    <div class="row">
        <div class="col-lg-10 col-xl-8 mx-auto">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-warning text-dark" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0 fw-bold">
                        <i class="bx bx-envelope me-2"></i>Email Account Configuration
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="emailConfigForm" method="POST" action="{{ route('modules.incidents.email.config.store') }}" novalidate>
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5><i class="bx bx-info-circle me-2"></i>Basic Information</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg" name="email_address" id="emailAddress" placeholder="your-email@gmail.com" required>
                                    <small class="text-muted">The email address to monitor for incidents</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Protocol Type <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" name="protocol" id="protocolSelect" required onchange="updatePort()">
                                        <option value="imap" selected>IMAP</option>
                                        <option value="pop3">POP3</option>
                                    </select>
                                    <small class="text-muted">IMAP is recommended for better functionality</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Host <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="host" id="host" placeholder="imap.gmail.com" required>
                                    <small class="text-muted">e.g., imap.gmail.com, mail.example.com</small>
                                </div>
                            </div>
                        </div>

                        <!-- Connection Settings Section -->
                        <div class="form-section">
                            <h5><i class="bx bx-network-chart me-2"></i>Connection Settings</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Port <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg" name="port" id="portInput" placeholder="993" min="1" max="65535" value="993" required>
                                    <small class="text-muted">IMAP: 993 (SSL) or 143 (TLS), POP3: 995 (SSL) or 110 (TLS)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Encryption <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="encryptionSelect" name="encryption" onchange="updatePort()">
                                        <option value="ssl" selected>SSL</option>
                                        <option value="tls">TLS</option>
                                    </select>
                                    <small class="text-muted">SSL is recommended for better security</small>
                                </div>
                            </div>
                        </div>

                        <!-- Authentication Section -->
                        <div class="form-section">
                            <h5><i class="bx bx-lock me-2"></i>Authentication</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="username" id="username" placeholder="username or email" required>
                                    <small class="text-muted">Usually your full email address</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-lg" name="password" id="passwordInput" placeholder="Enter email password or app password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordInput')">
                                            <i class="bx bx-show" id="passwordInput_icon"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">For Gmail, use App Password (not your regular password)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Settings Section -->
                        <div class="form-section">
                            <h5><i class="bx bx-cog me-2"></i>Advanced Settings</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Folder (IMAP only)</label>
                                    <input type="text" class="form-control form-control-lg" name="folder" id="folder" value="INBOX" placeholder="INBOX">
                                    <small class="text-muted">Leave blank for POP3. Default: INBOX</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status</label>
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCheck" checked style="width: 3rem; height: 1.5rem;">
                                        <label class="form-check-label ms-2" for="isActiveCheck">
                                            <strong>Active</strong>
                                            <br>
                                            <small class="text-muted">Enable automatic email syncing</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                            <a href="{{ route('modules.incidents.email.accounts') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bx bx-x me-1"></i>Cancel
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info btn-lg" onclick="testConnectionBeforeSave()">
                                    <i class="bx bx-refresh me-1"></i>Test Connection
                                </button>
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bx bx-save me-1"></i>Save Configuration
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Quick setup for david.ngungila@emca.tech
function quickSetupGmail() {
    document.getElementById('protocolSelect').value = 'imap';
    document.getElementById('host').value = 'imap.gmail.com';
    document.getElementById('portInput').value = '993';
    document.getElementById('encryptionSelect').value = 'ssl';
    document.getElementById('emailAddress').value = 'david.ngungila@emca.tech';
    document.getElementById('username').value = 'david.ngungila@emca.tech';
    document.getElementById('passwordInput').value = 'zoym lrqy ggnh giad';
    document.getElementById('folder').value = 'INBOX';
    document.getElementById('isActiveCheck').checked = true;
    updatePort();
    
    Swal.fire({
        icon: 'success',
        title: 'Quick Setup Applied!',
        text: 'Settings have been auto-filled. Please review and save.',
        timer: 2000,
        showConfirmButton: false
    });
}

function updatePort() {
    const protocol = document.getElementById('protocolSelect').value;
    const encryption = document.getElementById('encryptionSelect').value;
    const portInput = document.getElementById('portInput');
    if (protocol === 'imap') {
        portInput.value = encryption === 'ssl' ? '993' : '143';
    } else {
        portInput.value = encryption === 'ssl' ? '995' : '110';
    }
}

window.togglePassword = function(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    const icon = document.getElementById(fieldId + '_icon');
    if (field.type === 'password') {
        field.type = 'text';
        if (icon) {
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        }
    } else {
        field.type = 'password';
        if (icon) {
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        }
    }
}

// Test connection before saving
async function testConnectionBeforeSave() {
    const form = document.getElementById('emailConfigForm');
    const formData = new FormData(form);
    
    // Validate required fields first
    const email = formData.get('email_address');
    const host = formData.get('host');
    const port = formData.get('port');
    const username = formData.get('username');
    const password = formData.get('password');
    
    if (!email || !host || !port || !username || !password) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all required fields before testing the connection.'
        });
        return;
    }
    
    Swal.fire({
        title: 'Testing Connection...',
        html: 'Please wait while we test the email connection.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const res = await fetch('{{ route("modules.incidents.email.config.test.without.save") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await res.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Connection Successful!',
                text: data.message || 'Email connection test passed. You can now save the configuration.',
                confirmButtonText: 'Great!'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Connection Failed',
                html: data.message || 'Unable to connect to the email server. Please check your settings.',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Unable to test connection. Please check your internet connection.'
        });
    }
}

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const emailForm = document.getElementById('emailConfigForm');
    if (emailForm) {
        emailForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
            
            try {
                const res = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await res.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Email configuration saved successfully',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '{{ route("modules.incidents.email.accounts") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to save configuration'
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Network error. Please check your internet connection.'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    }
});
</script>
@endpush


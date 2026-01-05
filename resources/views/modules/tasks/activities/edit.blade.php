@extends('layouts.app')

@section('title', 'Edit Activity - ' . $activity->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    .section-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .attachment-item {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-warning" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-3 mb-md-0">
                            <h3 class="mb-2 text-white fw-bold">
                                <i class="bx bx-edit me-2"></i>Edit Activity
                            </h3>
                            <p class="mb-0 text-white-50 fs-6">{{ $activity->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('modules.tasks.activities.show', $activity->id) }}" class="btn btn-light btn-lg shadow-sm me-2">
                                <i class="bx bx-arrow-back me-2"></i>Back to Activity
                            </a>
                            <a href="{{ route('modules.tasks.show', $activity->mainTask->id) }}" class="btn btn-light btn-lg shadow-sm">
                                <i class="bx bx-task me-2"></i>View Task
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Activity Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="editActivityForm" method="POST" action="{{ route('modules.tasks.action') }}">
                        @csrf
                        <input type="hidden" name="action" value="task_update_activity">
                        <input type="hidden" name="activity_id" value="{{ $activity->id }}">

                        <!-- Basic Information -->
                        <div class="section-card">
                            <h4 class="mb-4"><i class="bx bx-info-circle me-2"></i>Basic Information</h4>
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Activity Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ $activity->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" id="startDate" value="{{ $activity->start_date ? $activity->start_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" id="endDate" value="{{ $activity->end_date ? $activity->end_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Not Started" {{ $activity->status == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="In Progress" {{ $activity->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Completed" {{ $activity->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Delayed" {{ $activity->status == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="Low" {{ ($activity->priority ?? 'Normal') == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Normal" {{ ($activity->priority ?? 'Normal') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="High" {{ ($activity->priority ?? 'Normal') == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Urgent" {{ ($activity->priority ?? 'Normal') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Timeframe</label>
                                    <input type="text" name="timeframe" class="form-control" id="timeframe" value="{{ $activity->timeframe }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Depends On</label>
                                    <select name="depends_on_id" class="form-select">
                                        <option value="">None</option>
                                        @foreach($activities as $act)
                                            <option value="{{ $act->id }}" {{ $activity->depends_on_id == $act->id ? 'selected' : '' }}>
                                                {{ $act->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Assign To</label>
                                    <select name="user_ids[]" class="form-select select2-users" multiple>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $activity->assignedUsers->contains('id', $user->id) ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="section-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0"><i class="bx bx-paperclip me-2"></i>Attachments ({{ $activity->attachments->count() }})</h4>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal">
                                    <i class="bx bx-upload me-1"></i>Upload File
                                </button>
                            </div>
                            
                            @if($activity->attachments->count() > 0)
                                <div class="row g-3">
                                    @foreach($activity->attachments as $attachment)
                                        <div class="col-md-6">
                                            <div class="attachment-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="bx bx-file fs-4 me-2 text-primary"></i>
                                                            <div>
                                                                <strong>{{ $attachment->file_name }}</strong><br>
                                                                <small class="text-muted">
                                                                    {{ $attachment->created_at->format('M d, Y') }} • 
                                                                    {{ $attachment->file_size ? number_format($attachment->file_size / 1024, 2) . ' KB' : 'N/A' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @php
                                                            $report = $attachment->report ?? null;
                                                        @endphp
                                                        @if($report)
                                                            <a href="{{ route('storage.activity-reports', ['reportId' => $report->id, 'filename' => basename($attachment->file_path)]) }}" target="_blank" class="btn btn-sm btn-outline-primary" download>
                                                                <i class="bx bx-download"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" download>
                                                                <i class="bx bx-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No attachments uploaded yet.</p>
                            @endif
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('modules.tasks.activities.show', $activity->id) }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update Activity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Attachment Modal -->
<div class="modal fade" id="uploadAttachmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Attachment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadAttachmentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                    <div class="mb-3">
                        <label class="form-label">File</label>
                        <input type="file" name="file" class="form-control">
                        <small class="text-muted">Upload documents, images, or other files</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Or Storage Link</label>
                        <input type="url" name="storage_link" class="form-control" placeholder="https://example.com/file.pdf">
                        <small class="text-muted">Enter a URL to an external file</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>You can either upload a file or provide a storage link, not both.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    const csrfToken = '{{ csrf_token() }}';
    const actionUrl = '{{ route("modules.tasks.action") }}';

    // Initialize Select2
    $('.select2-users').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select team members',
        allowClear: true
    });

    // Calculate timeframe
    function calculateTimeframe() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            let timeframe = '';
            if (diffDays < 7) {
                timeframe = diffDays + ' Day(s)';
            } else if (diffDays < 30) {
                const weeks = Math.floor(diffDays / 7);
                const days = diffDays % 7;
                timeframe = weeks + ' Week(s)';
                if (days > 0) timeframe += ' ' + days + ' Day(s)';
            } else if (diffDays < 365) {
                const months = Math.floor(diffDays / 30);
                const days = diffDays % 30;
                timeframe = months + ' Month(s)';
                if (days > 0) timeframe += ' ' + days + ' Day(s)';
            } else {
                const years = Math.floor(diffDays / 365);
                const months = Math.floor((diffDays % 365) / 30);
                timeframe = years + ' Year(s)';
                if (months > 0) timeframe += ' ' + months + ' Month(s)';
            }
            
            $('#timeframe').val(timeframe);
        }
    }

    $('#startDate, #endDate').on('change', calculateTimeframe);

    // Upload attachment
    $('#uploadAttachmentForm').on('submit', function(e) {
        e.preventDefault();
        const fileInput = $('input[name="file"]')[0];
        const storageLink = $('input[name="storage_link"]').val();

        if (!fileInput.files[0] && !storageLink) {
            Swal.fire('Error', 'Please upload a file or provide a storage link', 'error');
            return;
        }

        const formData = new FormData(this);
        formData.append('action', 'upload_activity_attachment');

        Swal.fire({
            title: 'Uploading...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'File uploaded successfully', 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message || 'Failed to upload file', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error', response?.message || 'Failed to upload file. Please try again.', 'error');
            }
        });
    });

    // Edit activity form
    $('#editActivityForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();

        Swal.fire({
            title: 'Updating Activity...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'Activity updated successfully', 'success').then(() => {
                        window.location.href = '{{ route("modules.tasks.activities.show", $activity->id) }}';
                    });
                } else {
                    Swal.fire('Error', response.message || 'Failed to update activity', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to update activity. Please try again.', 'error');
            }
        });
    });
});
</script>
@endpush


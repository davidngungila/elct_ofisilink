@extends('layouts.app')

@section('title', 'Activity Details - ' . $activity->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    .info-card {
        border-left: 4px solid #007bff;
    }
    .attachment-item {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    .attachment-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .image-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-primary" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-3 mb-md-0">
                            <h3 class="mb-2 text-white fw-bold">
                                <i class="bx bx-task me-2"></i>Activity Details
                            </h3>
                            <p class="mb-0 text-white-50 fs-6">
                                {{ $activity->name }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('modules.tasks.show', $activity->mainTask->id) }}" class="btn btn-light btn-lg shadow-sm me-2">
                                <i class="bx bx-arrow-back me-2"></i>Back to Task
                            </a>
                            @if($canEdit)
                                <a href="{{ route('modules.tasks.activities.edit', $activity->id) }}" class="btn btn-light btn-lg shadow-sm">
                                    <i class="bx bx-edit me-2"></i>Edit Activity
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Activity Information -->
            <div class="card mb-4 info-card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Activity Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-info-circle me-2 text-primary"></i>Activity Name:</strong><br>
                            <span class="h5 mb-0">{{ $activity->name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-list-check me-2 text-primary"></i>Task:</strong><br>
                            <a href="{{ route('modules.tasks.show', $activity->mainTask->id) }}" class="text-primary">
                                <strong>{{ $activity->mainTask->name }}</strong>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-check-circle me-2 text-primary"></i>Status:</strong><br>
                            @php
                                $statusBadge = [
                                    'Not Started' => 'bg-secondary',
                                    'In Progress' => 'bg-info',
                                    'Completed' => 'bg-success',
                                    'Delayed' => 'bg-danger'
                                ];
                                $badgeClass = $statusBadge[$activity->status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6 mt-2">
                                {{ $activity->status }}
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-star me-2 text-primary"></i>Priority:</strong><br>
                            @php
                                $priorityBadge = [
                                    'Low' => 'bg-secondary',
                                    'Normal' => 'bg-primary',
                                    'High' => 'bg-warning',
                                    'Urgent' => 'bg-danger'
                                ];
                                $priorityClass = $priorityBadge[$activity->priority ?? 'Normal'] ?? 'bg-primary';
                            @endphp
                            <span class="badge {{ $priorityClass }} fs-6 mt-2">
                                {{ $activity->priority ?? 'Normal' }}
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-calendar me-2 text-primary"></i>Start Date:</strong><br>
                            <span>{{ $activity->start_date ? $activity->start_date->format('l, F d, Y') : 'Not set' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-calendar-check me-2 text-primary"></i>End Date:</strong><br>
                            <span>{{ $activity->end_date ? $activity->end_date->format('l, F d, Y') : 'Not set' }}</span>
                        </div>
                        @if($activity->timeframe)
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-time me-2 text-primary"></i>Timeframe:</strong><br>
                            <span>{{ $activity->timeframe }}</span>
                        </div>
                        @endif
                        @if($activity->dependsOn)
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-link me-2 text-primary"></i>Depends On:</strong><br>
                            <span>{{ $activity->dependsOn->name }}</span>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-user me-2 text-primary"></i>Assigned To:</strong><br>
                            @if($activity->assignedUsers->count() > 0)
                                @foreach($activity->assignedUsers as $user)
                                    <span class="badge bg-info me-1">{{ $user->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-paperclip me-2"></i>Attachments ({{ $activity->attachments->count() }})</h5>
                    @if($canEdit)
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal">
                            <i class="bx bx-upload me-1"></i>Upload File
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($activity->attachments->count() > 0)
                        @foreach($activity->attachments as $attachment)
                            <div class="attachment-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bx bx-file fs-4 me-2 text-primary"></i>
                                            <div>
                                                <strong>{{ $attachment->file_name }}</strong><br>
                                                <small class="text-muted">
                                                    Uploaded by {{ $attachment->user->name ?? 'N/A' }} • 
                                                    {{ $attachment->created_at->format('M d, Y H:i') }} • 
                                                    {{ $attachment->file_size ? number_format($attachment->file_size / 1024, 2) . ' KB' : 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                        @php
                                            $isImage = in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        @if($isImage)
                                            <div>
                                                @php
                                                    $report = $attachment->report ?? null;
                                                @endphp
                                                @if($report)
                                                    <img src="{{ route('storage.activity-reports', ['reportId' => $report->id, 'filename' => basename($attachment->file_path)]) }}" alt="{{ $attachment->file_name }}" class="image-preview">
                                                @else
                                                    <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="image-preview">
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @php
                                            $report = $attachment->report ?? null;
                                        @endphp
                                        @if($report)
                                            <a href="{{ route('storage.activity-reports', ['reportId' => $report->id, 'filename' => basename($attachment->file_path)]) }}" target="_blank" class="btn btn-sm btn-outline-primary" download>
                                                <i class="bx bx-download"></i> Download
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" download>
                                                <i class="bx bx-download"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No attachments uploaded yet.</p>
                    @endif
                </div>
            </div>

            <!-- Progress Reports -->
            @if($activity->reports->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-file-blank me-2"></i>Progress Reports ({{ $activity->reports->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reporter</th>
                                    <th>Status</th>
                                    <th>Completion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activity->reports->take(10) as $report)
                                    <tr>
                                        <td>{{ $report->report_date->format('M d, Y') }}</td>
                                        <td>{{ $report->user->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $report->status == 'Approved' ? 'success' : ($report->status == 'Rejected' ? 'danger' : 'warning') }}">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $report->completion_status == 'Completed' ? 'success' : ($report->completion_status == 'Delayed' ? 'danger' : 'info') }}">
                                                {{ $report->completion_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('modules.tasks.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-show"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($activity->reports->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('modules.tasks.reports') }}?activity_id={{ $activity->id }}" class="btn btn-sm btn-outline-primary">
                                View All Reports
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-bar-chart me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Total Reports:</strong>
                        <span class="float-end">{{ $activity->reports->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Approved:</strong>
                        <span class="float-end text-success">{{ $activity->reports->where('status', 'Approved')->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Pending:</strong>
                        <span class="float-end text-warning">{{ $activity->reports->where('status', 'Pending')->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Attachments:</strong>
                        <span class="float-end">{{ $activity->attachments->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Assigned Users:</strong>
                        <span class="float-end">{{ $activity->assignedUsers->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Related Activities -->
            @if($activity->dependents->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-link me-2"></i>Dependent Activities</h5>
                </div>
                <div class="card-body">
                    @foreach($activity->dependents as $dependent)
                        <div class="mb-2">
                            <a href="{{ route('modules.tasks.activities.show', $dependent->id) }}" class="text-primary">
                                {{ $dependent->name }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Upload Attachment Modal -->
@if($canEdit)
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
                        <input type="file" name="file" class="form-control" required>
                        <small class="text-muted">Upload documents, images, or other files</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Or Storage Link</label>
                        <input type="url" name="storage_link" class="form-control" placeholder="https://example.com/file.pdf">
                        <small class="text-muted">Enter a URL to an external file</small>
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
@endif
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
$(document).ready(function() {
    const csrfToken = '{{ csrf_token() }}';
    const actionUrl = '{{ route("modules.tasks.action") }}';

    $('#uploadAttachmentForm').on('submit', function(e) {
        e.preventDefault();
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
});
</script>
@endpush


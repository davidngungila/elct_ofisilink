@extends('layouts.app')

@section('title', $task->name . ' - Task Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title text-white mb-1">
                                <i class="bx bx-task me-2"></i>{{ $task->name }}
                            </h4>
                            <p class="card-text text-white-50 mb-0">
                                <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                <span class="badge bg-{{ $task->priority == 'Critical' ? 'danger' : ($task->priority == 'High' ? 'warning' : ($task->priority == 'Low' ? 'secondary' : 'primary')) }} ms-2">
                                    {{ $task->priority }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('modules.tasks.index') }}" class="btn btn-light me-2">
                                <i class="bx bx-arrow-back me-1"></i>Back
                            </a>
                            @if($canEdit)
                                <a href="{{ route('modules.tasks.activities.create', $task->id) }}" class="btn btn-success me-2">
                                    <i class="bx bx-plus me-1"></i>Add Activity
                                </a>
                            @endif
                            <a href="{{ route('modules.tasks.report-pdf', $task->id) }}" class="btn btn-danger me-2" target="_blank">
                                <i class="bx bx-file-pdf me-1"></i>Generate PDF Report
                            </a>
                            @if($canEdit)
                                <a href="{{ route('modules.tasks.edit', $task->id) }}" class="btn btn-warning">
                                    <i class="bx bx-edit me-1"></i>Edit
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
            <!-- Task Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-calendar me-2 text-primary"></i>Start Date:</strong>
                            <p class="mb-0">{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('l, F d, Y') : 'Not set' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-calendar-check me-2 text-primary"></i>End Date:</strong>
                            <p class="mb-0">{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('l, F d, Y') : 'Not set' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-user me-2 text-primary"></i>Team Leader:</strong>
                            <p class="mb-0">{{ $task->teamLeader->name ?? 'Not assigned' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-category me-2 text-primary"></i>Category:</strong>
                            <p class="mb-0">{{ $task->category ?? 'Uncategorized' }}</p>
                        </div>
                        @if($task->budget)
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-dollar me-2 text-primary"></i>Budget:</strong>
                            <p class="mb-0">{{ number_format($task->budget, 2) }}</p>
                        </div>
                        @endif
                        @if($task->progress_percentage !== null)
                        <div class="col-md-6 mb-3">
                            <strong><i class="bx bx-trending-up me-2 text-primary"></i>Progress:</strong>
                            <div class="progress mt-1" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $task->progress_percentage }}%">
                                    {{ $task->progress_percentage }}%
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($task->description)
                        <div class="col-12 mb-3">
                            <strong><i class="bx bx-file me-2 text-primary"></i>Description:</strong>
                            <p class="mb-0">{{ $task->description }}</p>
                        </div>
                        @endif
                        @if($task->tags && count($task->tags) > 0)
                        <div class="col-12 mb-3">
                            <strong><i class="bx bx-tag me-2 text-primary"></i>Tags:</strong>
                            <div class="mt-1">
                                @foreach($task->tags as $tag)
                                    <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activities -->
            @if($task->activities->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-list-check me-2"></i>Activities ({{ $task->activities->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($task->activities as $activity)
                                <tr>
                                    <td>
                                        <strong>{{ $activity->name }}</strong>
                                        @if($activity->priority)
                                            <span class="badge bg-{{ $activity->priority == 'High' ? 'warning' : 'secondary' }} ms-1">
                                                {{ $activity->priority }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->assignedUsers->count() > 0)
                                            @foreach($activity->assignedUsers as $user)
                                                <span class="badge bg-info">{{ $user->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->status == 'Completed' ? 'success' : ($activity->status == 'In Progress' ? 'info' : 'warning') }}">
                                            {{ $activity->status }}
                                        </span>
                                    </td>
                                    <td>{{ $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->format('M d, Y') : 'No deadline' }}</td>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            @if($activity->reports->count() > 0)
                                                <span class="badge bg-success">{{ $activity->reports->count() }} Reports</span>
                                            @endif
                                            @php
                                                $isAssigned = $activity->assignedUsers->contains('id', Auth::id());
                                                // Check if there's a pending unapproved report
                                                $hasPendingReport = $activity->reports
                                                    ->where('user_id', Auth::id())
                                                    ->where('status', 'Pending')
                                                    ->isNotEmpty();
                                            @endphp
                                            @if($isAssigned && !$hasPendingReport)
                                                <a href="{{ route('modules.tasks.activities.report', $activity->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bx bx-upload me-1"></i>Report Progress
                                                </a>
                                            @elseif($isAssigned && $hasPendingReport)
                                                <span class="badge bg-warning">
                                                    <i class="bx bx-time me-1"></i>Pending Approval
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- All Submitted Reports -->
            @if(isset($allReports) && $allReports->count() > 0)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-file-blank me-2"></i>All Submitted Reports ({{ $allReports->count() }})
                    </h5>
                    <a href="{{ route('modules.tasks.reports') }}?task_id={{ $task->id }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-list-ul me-1"></i>View All Reports
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Report Date</th>
                                    <th>Activity</th>
                                    <th>Reporter</th>
                                    <th>Completion Status</th>
                                    <th>Approval Status</th>
                                    <th>Approved By</th>
                                    <th>Attachments</th>
                                    <th style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allReports->take(20) as $report)
                                    <tr>
                                        <td>
                                            <strong>{{ $report->report_date->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">{{ $report->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('modules.tasks.activities.show', $report->activity->id) }}" class="text-primary">
                                                {{ $report->activity->name ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-primary">{{ substr($report->user->name ?? 'N/A', 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <strong>{{ $report->user->name ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $report->user->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $completionBadge = [
                                                    'In Progress' => 'bg-info',
                                                    'Completed' => 'bg-success',
                                                    'Delayed' => 'bg-danger'
                                                ];
                                                $badgeClass = $completionBadge[$report->completion_status] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $report->completion_status }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = [
                                                    'Pending' => 'bg-warning',
                                                    'Approved' => 'bg-success',
                                                    'Rejected' => 'bg-danger'
                                                ];
                                                $badgeClass = $statusBadge[$report->status] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($report->approver)
                                                <div>
                                                    <strong>{{ $report->approver->name }}</strong><br>
                                                    <small class="text-muted">{{ $report->approved_at ? $report->approved_at->format('M d, Y') : '' }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($report->attachments->count() > 0)
                                                <span class="badge bg-info">
                                                    <i class="bx bx-paperclip"></i> {{ $report->attachments->count() }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('modules.tasks.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bx bx-show"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($allReports->count() > 20)
                        <div class="text-center mt-3">
                            <a href="{{ route('modules.tasks.reports') }}?task_id={{ $task->id }}" class="btn btn-outline-primary">
                                <i class="bx bx-list-ul me-1"></i>View All {{ $allReports->count() }} Reports
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-file-blank me-2"></i>Submitted Reports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="bx bx-file-blank fs-1 text-muted mb-3"></i>
                        <p class="text-muted mb-0">No reports have been submitted yet for this task.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-bar-chart me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Total Activities:</strong>
                        <span class="float-end">{{ $task->activities->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Completed:</strong>
                        <span class="float-end">{{ $task->activities->where('status', 'Completed')->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>In Progress:</strong>
                        <span class="float-end">{{ $task->activities->where('status', 'In Progress')->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Not Started:</strong>
                        <span class="float-end">{{ $task->activities->where('status', 'Not Started')->count() }}</span>
                    </div>
                    @if(isset($allReports))
                    <hr>
                    <div class="mb-3">
                        <strong>Total Reports:</strong>
                        <span class="float-end">{{ $allReports->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Approved:</strong>
                        <span class="float-end text-success">{{ $allReports->where('status', 'Approved')->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Pending:</strong>
                        <span class="float-end text-warning">{{ $allReports->where('status', 'Pending')->count() }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Rejected:</strong>
                        <span class="float-end text-danger">{{ $allReports->where('status', 'Rejected')->count() }}</span>
                    </div>
                    @endif
                    @if($task->progress_percentage !== null)
                    <hr>
                    <div>
                        <strong>Overall Progress:</strong>
                        <div class="progress mt-2">
                            <div class="progress-bar" role="progressbar" style="width: {{ $task->progress_percentage }}%">
                                {{ $task->progress_percentage }}%
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>Quick Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Created:</small><br>
                        <strong>{{ $task->created_at->format('M d, Y') }}</strong>
                    </div>
                    @if($task->creator)
                    <div class="mb-2">
                        <small class="text-muted">Created By:</small><br>
                        <strong>{{ $task->creator->name }}</strong>
                    </div>
                    @endif
                    @if($task->updated_at != $task->created_at)
                    <div class="mb-2">
                        <small class="text-muted">Last Updated:</small><br>
                        <strong>{{ $task->updated_at->format('M d, Y') }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
function setActivityForReport(activityId, activityName) {
    $('#modalProgressActivitySelect').val(activityId).trigger('change');
    // Update the modal title to show activity name
    if (activityName) {
        $('#submitProgressModal .modal-title').html('<i class="bx bx-upload"></i> Submit Progress Report - ' + activityName);
    }
}
</script>
@endpush

@include('modules.tasks.partials.submit-progress-modal', ['flatActivities' => $flatActivities ?? []])

@php
    // Get flat activities list for the modal
    $flatActivities = [];
    foreach($task->activities as $activity) {
        $flatActivities[] = [
            'id' => $activity->id,
            'name' => $activity->name,
            'task' => $task->name
        ];
    }
@endphp

@include('modules.tasks.partials.submit-progress-modal', ['flatActivities' => $flatActivities])


@extends('layouts.app')

@section('title', 'Edit Task - ' . $task->name)

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
    .section-card-header {
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 16px;
        margin-bottom: 24px;
    }
    .activity-row:hover {
        background-color: #f8f9fa;
    }
    .activity-details-popup .swal2-html-container {
        text-align: left;
    }
    .activity-details-view {
        text-align: left;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title text-white mb-1">
                                <i class="bx bx-edit me-2"></i>Edit Task
                            </h4>
                            <p class="card-text text-white-50 mb-0">{{ $task->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('modules.tasks.show', $task->id) }}" class="btn btn-light me-2">
                                <i class="bx bx-arrow-back me-1"></i>Back to Task
                            </a>
                            <a href="{{ route('modules.tasks.index') }}" class="btn btn-light">
                                <i class="bx bx-list-ul me-1"></i>All Tasks
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Task Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="editTaskForm" method="POST" action="{{ route('modules.tasks.action') }}">
                        @csrf
                        <input type="hidden" name="action" value="task_update_main">
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <input type="hidden" name="main_task_id" value="{{ $task->id }}">
                        
                        <!-- Basic Information -->
                        <div class="section-card">
                            <div class="section-card-header">
                                <h3 class="section-card-title">
                                    <i class="bx bx-info-circle me-2"></i>Basic Information
                                </h3>
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Task Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ $task->name }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="4" class="form-control">{{ $task->description }}</textarea>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}" {{ $task->category == $category->name ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="Low" {{ $task->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Normal" {{ $task->priority == 'Normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="High" {{ $task->priority == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Critical" {{ $task->priority == 'Critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Team Leader <span class="text-danger">*</span></label>
                                    <select name="team_leader_id" class="form-select" required>
                                        <option value="">Select Team Leader</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $task->team_leader_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $task->start_date ? $task->start_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $task->end_date ? $task->end_date->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="planning" {{ $task->status == 'planning' ? 'selected' : '' }}>Planning</option>
                                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="delayed" {{ $task->status == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                        <option value="on_hold" {{ $task->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Budget</label>
                                    <input type="text" name="budget" class="form-control" value="{{ $task->budget }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tags</label>
                                    <input type="text" name="tags" class="form-control" value="{{ $task->tags ? implode(', ', $task->tags) : '' }}" placeholder="Comma-separated tags">
                                </div>
                            </div>
                        </div>

                        <!-- Activities Section -->
                        <div class="section-card mt-4">
                            <div class="section-card-header d-flex justify-content-between align-items-center">
                                <h3 class="section-card-title mb-0">
                                    <i class="bx bx-list-check me-2"></i>Activities ({{ $task->activities->count() }})
                                </h3>
                                <a href="{{ route('modules.tasks.activities.create', $task->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-plus me-1"></i>Add Activity
                                </a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Activity Name</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Dates</th>
                                            <th>Assigned To</th>
                                            <th>Reports</th>
                                            <th style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="activitiesContainer">
                                        @foreach($task->activities as $index => $activity)
                                            <tr class="activity-row" data-activity-id="{{ $activity->id }}">
                                                <td>
                                                    <span class="badge bg-primary">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $activity->name }}</strong>
                                                    <div class="collapse activity-details-{{ $activity->id }}" id="activityCollapse{{ $activity->id }}">
                                                        <div class="mt-3 p-3 bg-light rounded">
                                                            <div class="row g-3">
                                                                <div class="col-md-12">
                                                                    <label class="form-label small">Activity Name <span class="text-danger">*</span></label>
                                                                    <input type="text" name="activities[{{ $activity->id }}][name]" class="form-control form-control-sm activity-name" value="{{ $activity->name }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">Start Date</label>
                                                                    <input type="date" name="activities[{{ $activity->id }}][start_date]" class="form-control form-control-sm activity-start-date" value="{{ $activity->start_date ? $activity->start_date->format('Y-m-d') : '' }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">End Date</label>
                                                                    <input type="date" name="activities[{{ $activity->id }}][end_date]" class="form-control form-control-sm activity-end-date" value="{{ $activity->end_date ? $activity->end_date->format('Y-m-d') : '' }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">Timeframe</label>
                                                                    <input type="text" name="activities[{{ $activity->id }}][timeframe]" class="form-control form-control-sm activity-timeframe" value="{{ $activity->timeframe }}" readonly>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label small">Status</label>
                                                                    <select name="activities[{{ $activity->id }}][status]" class="form-select form-select-sm activity-status">
                                                                        <option value="Not Started" {{ $activity->status == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                                                        <option value="In Progress" {{ $activity->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                                        <option value="Completed" {{ $activity->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                                        <option value="Delayed" {{ $activity->status == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label small">Priority</label>
                                                                    <select name="activities[{{ $activity->id }}][priority]" class="form-select form-select-sm activity-priority">
                                                                        <option value="Low" {{ $activity->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                                                        <option value="Normal" {{ $activity->priority == 'Normal' ? 'selected' : '' }}>Normal</option>
                                                                        <option value="High" {{ $activity->priority == 'High' ? 'selected' : '' }}>High</option>
                                                                        <option value="Urgent" {{ $activity->priority == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label class="form-label small">Assign To</label>
                                                                    <select name="activities[{{ $activity->id }}][user_ids][]" class="form-select form-select-sm select2-activity-users" multiple>
                                                                        @foreach($users as $user)
                                                                            <option value="{{ $user->id }}" {{ $activity->assignedUsers->contains('id', $user->id) ? 'selected' : '' }}>
                                                                                {{ $user->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-12">
                                                                    <button type="button" class="btn btn-sm btn-success" onclick="saveActivity({{ $activity->id }})">
                                                                        <i class="bx bx-save me-1"></i>Save Changes
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusBadge = [
                                                            'Not Started' => 'bg-secondary',
                                                            'In Progress' => 'bg-info',
                                                            'Completed' => 'bg-success',
                                                            'Delayed' => 'bg-danger'
                                                        ];
                                                        $badgeClass = $statusBadge[$activity->status] ?? 'bg-secondary';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $activity->status }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $priorityBadge = [
                                                            'Low' => 'bg-secondary',
                                                            'Normal' => 'bg-primary',
                                                            'High' => 'bg-warning',
                                                            'Urgent' => 'bg-danger'
                                                        ];
                                                        $priorityClass = $priorityBadge[$activity->priority ?? 'Normal'] ?? 'bg-primary';
                                                    @endphp
                                                    <span class="badge {{ $priorityClass }}">{{ $activity->priority ?? 'Normal' }}</span>
                                                </td>
                                                <td>
                                                    @if($activity->start_date && $activity->end_date)
                                                        <small class="text-muted">
                                                            <i class="bx bx-calendar"></i> {{ $activity->start_date->format('M d') }} - {{ $activity->end_date->format('M d, Y') }}
                                                        </small><br>
                                                        <small class="text-muted">{{ $activity->timeframe ?? 'N/A' }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($activity->assignedUsers->count() > 0)
                                                        @foreach($activity->assignedUsers->take(2) as $user)
                                                            <span class="badge bg-info me-1">{{ $user->name }}</span>
                                                        @endforeach
                                                        @if($activity->assignedUsers->count() > 2)
                                                            <span class="badge bg-secondary">+{{ $activity->assignedUsers->count() - 2 }}</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($activity->reports->count() > 0)
                                                        <span class="badge bg-success">
                                                            <i class="bx bx-file-blank"></i> {{ $activity->reports->count() }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('modules.tasks.activities.show', $activity->id) }}" class="btn btn-outline-info" title="View Details">
                                                            <i class="bx bx-show"></i>
                                                        </a>
                                                        <a href="{{ route('modules.tasks.activities.edit', $activity->id) }}" class="btn btn-outline-primary" title="Edit">
                                                            <i class="bx bx-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" onclick="deleteActivity({{ $activity->id }})" title="Delete">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('modules.tasks.show', $task->id) }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update Task
                            </button>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    let activityCounter = {{ $task->activities->count() }};
    const csrfToken = '{{ csrf_token() }}';
    const actionUrl = '{{ route("modules.tasks.action") }}';
    const taskId = {{ $task->id }};

    // Initialize Select2
    $('.form-select').select2({
        theme: 'bootstrap-5'
    });
    
    // Initialize Select2 for existing activity users
    $('.select2-activity-users').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select team members',
        allowClear: true
    });

    // Calculate timeframe when dates change
    $(document).on('change', '.activity-start-date, .activity-end-date', function() {
        const activityRow = $(this).closest('.activity-row');
        const startDate = activityRow.find('.activity-start-date').val();
        const endDate = activityRow.find('.activity-end-date').val();
        
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
            
            activityRow.find('.activity-timeframe').val(timeframe);
        }
    });



    // Save existing activity
    window.saveActivity = function(activityId) {
        const activityRow = $(`.activity-row[data-activity-id="${activityId}"]`);
        const name = activityRow.find('.activity-name').val();
        const startDate = activityRow.find('.activity-start-date').val();
        const endDate = activityRow.find('.activity-end-date').val();
        const status = activityRow.find('.activity-status').val();
        const priority = activityRow.find('.activity-priority').val();
        const timeframe = activityRow.find('.activity-timeframe').val();
        const userIds = activityRow.find('.select2-activity-users').val() || [];

        if (!name) {
            Swal.fire('Error', 'Activity name is required', 'error');
            return;
        }

        Swal.fire({
            title: 'Saving Activity...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: {
                _token: csrfToken,
                action: 'task_update_activity',
                activity_id: activityId,
                name: name,
                start_date: startDate,
                end_date: endDate,
                status: status,
                priority: priority,
                timeframe: timeframe,
                user_ids: userIds
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'Activity updated successfully', 'success');
                } else {
                    Swal.fire('Error', response.message || 'Failed to update activity', 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error', response?.message || 'Failed to update activity. Please try again.', 'error');
            }
        });
    };


    // Delete activity
    window.deleteActivity = function(activityId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the activity and all its reports. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting Activity...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        action: 'task_delete_activity',
                        activity_id: activityId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message || 'Activity deleted successfully', 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Failed to delete activity', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error', response?.message || 'Failed to delete activity. Please try again.', 'error');
                    }
                });
            }
        });
    };


    // Update task form submission
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Updating Task...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message || 'Task updated successfully', 'success').then(() => {
                        window.location.href = '/modules/tasks/' + taskId;
                    });
                } else {
                    Swal.fire('Error', response.message || 'Failed to update task', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to update task. Please try again.', 'error');
            }
        });
    });
});
</script>
@endpush


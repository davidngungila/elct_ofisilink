@extends('layouts.app')

@section('title', 'Task Management')

@section('breadcrumb')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold py-3 mb-2">
                    <i class="bx bx-task"></i> Task Management
                </h4>
                <p class="text-muted">Plan, assign, and track activities with advanced features, calendar views, and comprehensive analytics</p>
            </div>
            <div class="btn-group" role="group">
                @if($isManager)
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                        <i class="bx bx-plus"></i> New Task
                    </button>
                @endif
                <button class="btn btn-success" id="submitProgressBtn">
                    <i class="bx bx-upload"></i> Submit Progress
                </button>
                <a class="btn btn-info" href="{{ route('modules.tasks.pdf') }}" target="_blank">
                    <i class="bx bx-download"></i> Export PDF
                </a>
                <a class="btn btn-secondary" href="{{ route('modules.tasks.analytics.pdf') }}" target="_blank">
                    <i class="bx bx-bar-chart"></i> Analytics PDF
                </a>
                <button class="btn btn-outline-dark" id="refreshTasksBtn">
                    <i class="bx bx-refresh"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    :root {
        --task-primary: #2563eb;
        --task-success: #16a34a;
        --task-warning: #d97706;
        --task-danger: #dc2626;
        --task-info: #4f46e5;
        --task-gray: #6b7280;
        --task-light: #f8fafc;
        --task-border: #e5e7eb;
        --task-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --task-shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    /* Hero Header Section - Independent */
    .hero-header-section {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
        border-radius: 20px;
        padding: 32px;
        color: #ffffff;
        margin-bottom: 32px;
        box-shadow: var(--task-shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .hero-header-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .hero-header-section .eyebrow {
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-size: 11px;
        opacity: 0.9;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    .hero-header-section h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 12px;
        line-height: 1.2;
    }

    .hero-header-section .hero-description {
        font-size: 15px;
        opacity: 0.95;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    /* Dashboard Stats Section - Independent Cards */
    .stats-section {
        margin-bottom: 32px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid var(--task-border);
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        box-shadow: var(--task-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--task-primary);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--task-shadow-lg);
        border-color: var(--task-primary);
    }

    .stat-card:hover::before {
        transform: scaleY(1);
    }

    .stat-card.stat-success::before { background: var(--task-success); }
    .stat-card.stat-warning::before { background: var(--task-warning); }
    .stat-card.stat-danger::before { background: var(--task-danger); }
    .stat-card.stat-info::before { background: var(--task-info); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--task-primary) 0%, #1d4ed8 100%);
        color: white;
    }

    .stat-card.stat-success .stat-icon { background: linear-gradient(135deg, var(--task-success) 0%, #15803d 100%); }
    .stat-card.stat-warning .stat-icon { background: linear-gradient(135deg, var(--task-warning) 0%, #b45309 100%); }
    .stat-card.stat-danger .stat-icon { background: linear-gradient(135deg, var(--task-danger) 0%, #b91c1c 100%); }
    .stat-card.stat-info .stat-icon { background: linear-gradient(135deg, var(--task-info) 0%, #4338ca 100%); }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--task-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #111827;
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Independent Section Cards */
    .section-card {
        background: #ffffff;
        border: 1px solid var(--task-border);
        border-radius: 20px;
        box-shadow: var(--task-shadow);
        margin-bottom: 32px;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .section-card:hover {
        box-shadow: var(--task-shadow-lg);
    }

    .section-card-header {
        padding: 24px;
        border-bottom: 1px solid var(--task-border);
        background: linear-gradient(to bottom, #ffffff 0%, #f9fafb 100%);
    }

    .section-card-title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .section-card-subtitle {
        font-size: 13px;
        color: var(--task-gray);
        margin: 0;
    }

    .section-card-body {
        padding: 24px;
    }

    .section-card-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--task-border);
        background: #f9fafb;
        font-size: 12px;
        color: var(--task-gray);
    }

    /* Task Card Component - Independent */
    .task-card {
        background: #ffffff;
        border: 1px solid var(--task-border);
        border-radius: 16px;
        padding: 20px;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .task-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--task-primary), var(--task-info));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--task-shadow-lg);
        border-color: var(--task-primary);
    }

    .task-card:hover::after {
        transform: scaleX(1);
    }

    /* Pill Badges */
    .pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .pill-success { background: #ecfdf3; color: var(--task-success); border: 1px solid #bbf7d0; }
    .pill-danger { background: #fef2f2; color: var(--task-danger); border: 1px solid #fecaca; }
    .pill-warning { background: #fffbeb; color: var(--task-warning); border: 1px solid #fde68a; }
    .pill-info { background: #eef2ff; color: var(--task-info); border: 1px solid #c7d2fe; }
    .pill-secondary { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }

    /* Avatar Chip */
    .avatar-chip {
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        border: 1px solid #bfdbfe;
        display: inline-block;
    }

    /* Need Action Section - Special Styling */
    .need-action-section {
        border-left: 5px solid #f97316;
        background: linear-gradient(to right, #fff7ed 0%, #ffffff 100%);
    }

    .need-action-section .section-card-header {
        background: linear-gradient(to bottom, #fff7ed 0%, #ffffff 100%);
    }

    /* Form Elements */
    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--task-border);
    }

    .input-hint {
        font-size: 12px;
        color: var(--task-gray);
        margin-top: 4px;
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
    }

    .table thead th {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--task-gray);
        border-bottom: 2px solid var(--task-border);
        padding: 12px 16px;
    }

    .table tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .table tbody tr:hover {
        background: #f9fafb;
    }

    /* Progress Bar */
    .progress {
        height: 8px;
        border-radius: 10px;
        background: #f3f4f6;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 10px;
        background: linear-gradient(90deg, var(--task-primary), var(--task-info));
    }

    /* Tab Navigation */
    .nav-tabs-custom {
        border-bottom: 2px solid var(--task-border);
        margin-bottom: 24px;
        background: white;
        padding: 0 24px;
        border-radius: 12px 12px 0 0;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--task-gray);
        font-weight: 600;
        padding: 16px 24px;
        transition: all 0.3s;
        background: transparent;
    }
    .nav-tabs-custom .nav-link:hover {
        border-bottom-color: var(--task-primary);
        color: var(--task-primary);
    }
    .nav-tabs-custom .nav-link.active {
        color: var(--task-primary);
        border-bottom-color: var(--task-primary);
        background: transparent;
    }

    /* Tab Content */
    .tab-content-custom {
        background: white;
        border-radius: 0 0 12px 12px;
        min-height: 500px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .section-card-body {
            padding: 16px;
        }

        .stat-card {
            padding: 20px;
        }

        .nav-tabs-custom .nav-link {
            padding: 12px 16px;
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-info">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Total Tasks</h6>
                        <h3 class="mb-0 text-primary">{{ $dashboardStats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-list-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-warning">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">In Progress</h6>
                        <h3 class="mb-0 text-warning">{{ $dashboardStats['in_progress'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-loader-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-success">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Completed</h6>
                        <h3 class="mb-0 text-success">{{ $dashboardStats['completed'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-danger">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Overdue</h6>
                        <h3 class="mb-0 text-danger">{{ $dashboardStats['overdue'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-error-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs nav-tabs-custom" id="tasksTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                <i class="bx bx-home me-2"></i>Dashboard
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-view" type="button" role="tab">
                <i class="bx bx-list-ul me-2"></i>List View
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-view" type="button" role="tab">
                <i class="bx bx-calendar me-2"></i>Calendar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics-view" type="button" role="tab">
                <i class="bx bx-bar-chart-alt-2 me-2"></i>Analytics
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports-view" type="button" role="tab">
                <i class="bx bx-file-blank me-2"></i>Reports
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content tab-content-custom" id="tasksTabContent">
        <!-- Dashboard Section - Independent -->
        <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
            @include('modules.tasks.partials.dashboard', [
                'pendingReports' => $pendingReports,
                'isManager' => $isManager,
                'mainTasks' => $mainTasks,
                'flatActivities' => $flatActivities,
                'users' => $users,
                'categories' => $categories
            ])
        </div>

        <!-- List View Section - Independent -->
        <div class="tab-pane fade" id="list-view" role="tabpanel">
            @include('modules.tasks.partials.list-view', [
                'mainTasks' => $mainTasks,
                'isManager' => $isManager,
                'users' => $users,
                'categories' => $categories,
                'filters' => $filters ?? []
            ])
        </div>

        <!-- Calendar View Section - Independent -->
        <div class="tab-pane fade" id="calendar-view" role="tabpanel">
            @include('modules.tasks.partials.calendar', [
                'mainTasks' => $mainTasks,
                'isManager' => $isManager
            ])
        </div>

        <!-- Analytics View Section - Independent -->
        <div class="tab-pane fade" id="analytics-view" role="tabpanel">
            @include('modules.tasks.partials.analytics', [
                'dashboardStats' => $dashboardStats,
                'mainTasks' => $mainTasks,
                'isManager' => $isManager
            ])
        </div>

        <!-- Reports View Section - Independent -->
        <div class="tab-pane fade" id="reports-view" role="tabpanel">
            @include('modules.tasks.partials.reports', [
                'pendingReports' => $pendingReports,
                'isManager' => $isManager
            ])
        </div>
    </div>
</div>

<!-- Create Task Modal -->
@if($isManager)
@include('modules.tasks.partials.create-task-modal', [
    'users' => $users,
    'categories' => $categories
])
@endif

<!-- Submit Progress Modal -->
@include('modules.tasks.partials.submit-progress-modal', [
    'flatActivities' => $flatActivities
])
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
<script>
$(document).ready(function() {
    const actionUrl = '{{ route("modules.tasks.action") }}';
    const csrfToken = '{{ csrf_token() }}';
    
    // Tab initialization
    $('#tasksTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).data('bs-target');
        console.log('Tab switched to:', target);
        
        // Initialize components based on active tab
        if (target === '#calendar-view') {
            setTimeout(() => initializeCalendar(), 100);
        } else if (target === '#analytics-view') {
            setTimeout(() => initializeAnalytics(), 100);
        }
    });
    
    // Refresh button
    $('#refreshTasksBtn').on('click', function() {
        location.reload();
    });
    
    // Submit Progress button
    $('#submitProgressBtn').on('click', function() {
        $('#submitProgressModal').modal('show');
    });
});
</script>
@include('modules.tasks.partials.scripts')
@endpush
        <div class="section-card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="section-card-title">
                        <i class="bx bx-error-circle text-warning me-2"></i>
                        Need Action
                    </h3>
                    <p class="section-card-subtitle">Latest submissions waiting for review — Newest first, documents included</p>
                </div>
                <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 13px; font-weight: 600;">
                    {{ $pendingReports->count() }} Pending
                </span>
            </div>
        </div>
        <div class="section-card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($pendingReports as $report)
                    @php
                        $taskName = $report->activity->mainTask->name ?? 'Task';
                        $activityName = $report->activity->name ?? 'Activity';
                    @endphp
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="fw-semibold">{{ $activityName }}</div>
                                <div class="text-muted small mb-1">{{ $taskName }}</div>
                                <div class="d-flex flex-wrap gap-2 small align-items-center">
                                    <span class="pill pill-info">{{ $report->completion_status ?? 'Pending' }}</span>
                                    <span class="text-muted">{{ \Illuminate\Support\Carbon::parse($report->report_date ?? $report->created_at)->format('M d, Y') }}</span>
                                    @if($report->attachment_path)
                                        <a class="attachment-link" target="_blank" href="{{ Storage::url($report->attachment_path) }}"><i class="bx bx-link-alt"></i> Attachment</a>
                                    @endif
                                </div>
                                @if($report->work_description)
                                    <div class="text-muted small mt-1">{{ \Illuminate\Support\Str::limit($report->work_description, 140) }}</div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="avatar-chip mb-1">{{ $report->user->name ?? 'Reporter' }}</div>
                                @if($isManager)
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-success" onclick="approveReport({{ $report->id }})">Approve</button>
                                        <button class="btn btn-outline-danger" onclick="rejectReport({{ $report->id }})">Reject</button>
                                    </div>
                                @else
                                    <span class="pill pill-warning">Pending review</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-4">
                        Everything is up to date. No pending reports.
                    </div>
                @endforelse
            </div>
        </div>
        <div class="section-card-footer">
            <i class="bx bx-info-circle me-1"></i>
            SMS alerts are automatically sent to leaders and approvers whenever progress is submitted or reviewed.
        </div>
    </div>

    <!-- Task Creation & Progress Report Section - Independent Components -->
    <div class="row g-4">
        @if($isManager)
        <div class="col-xl-5">
            <div class="section-card h-100" id="createTaskSection">
                <div class="section-card-header">
                    <h3 class="section-card-title">
                        <i class="bx bx-plus-circle me-2"></i>
                        Create Task
                    </h3>
                    <p class="section-card-subtitle">Comprehensive task creation form with full details and activity setup</p>
                </div>
                <div class="section-card-body">
                    <form id="createTaskForm">
                        @csrf
                        <div class="form-section-title mb-2">Basics</div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Task Name *</label>
                                <input type="text" id="taskName" name="name" class="form-control" required>
                                <div class="input-hint">Clear, action-oriented title.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">Select</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="Normal">Normal</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-section-title mb-2">Ownership & Dates</div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Team Leader *</label>
                                <select name="team_leader_id" class="form-select" required>
                                    <option value="">Select</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Timeframe</label>
                                <input type="text" name="timeframe" class="form-control" placeholder="e.g. 3 Weeks">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Budget</label>
                                <input type="number" name="budget" class="form-control" placeholder="Optional">
                            </div>
                        </div>

                        <div class="form-section-title mb-2">Details</div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Objectives, outcomes, constraints"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <input type="text" name="tags" class="form-control" placeholder="comma separated">
                        </div>

                        <div class="form-section-title mb-2">Kick-off Activity (optional)</div>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Activity Title</label>
                                <input type="text" id="initialActivity" class="form-control" placeholder="First activity">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assign To</label>
                                <select id="initialActivityUsers" class="form-select" multiple>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <small class="input-hint">Select team members (optional)</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary">Save Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="{{ $isManager ? 'col-xl-7' : 'col-xl-8 offset-xl-2' }}">
            <div class="section-card h-100" id="progressSection">
                <div class="section-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="section-card-title">
                                <i class="bx bx-upload me-2"></i>
                                Report Progress with Documents
                            </h3>
                            <p class="section-card-subtitle">Attach evidence, add context, and flag blockers. SMS notifications sent automatically.</p>
                        </div>
                        <span class="stat-badge pill-info">
                            <i class="bx bx-check"></i> Live
                        </span>
                    </div>
                </div>
                <div class="section-card-body">
                    <form id="progressForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Activity *</label>
                                <select name="activity_id" id="progressActivitySelect" class="form-select" required>
                                    <option value="">Select activity</option>
                                    @foreach($flatActivities as $activity)
                                        <option value="{{ $activity['id'] }}">
                                            {{ $activity['name'] }} — {{ $activity['task'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Report Date</label>
                                <input type="date" name="report_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Completion Status</label>
                                <select name="completion_status" id="completionStatus" class="form-select" required>
                                    <option value="On Track">On Track</option>
                                    <option value="Ahead">Ahead</option>
                                    <option value="Behind Schedule">Behind Schedule</option>
                                    <option value="Delayed">Delayed</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Attachment (doc/photo)</label>
                                <input type="file" name="attachment" class="form-control">
                                <div class="input-hint">Upload evidence or supporting document.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Work Done *</label>
                                <textarea name="work_description" rows="3" class="form-control" placeholder="Be concise but detailed. What did you complete?" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Next Activities</label>
                                <textarea name="next_activities" rows="2" class="form-control" placeholder="What’s next? Who needs to act?"></textarea>
                            </div>
                            <div class="col-12" id="delayReasonWrap" style="display:none;">
                                <label class="form-label">Reason / Blockers</label>
                                <textarea name="reason_if_delayed" rows="2" class="form-control" placeholder="Why is it behind? What support is needed?"></textarea>
                            </div>
                            <div class="alert alert-info mb-0">
                                SMS alerts are dispatched to leaders, approvers, and other action owners the moment you submit.
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="reset" class="btn btn-light">Clear</button>
                            <button type="submit" class="btn btn-primary">Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Board Section - Independent Component -->
    <div class="section-card">
        <div class="section-card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="section-card-title">
                        <i class="bx bx-grid-alt me-2"></i>
                        Task Board
                    </h3>
                    <p class="section-card-subtitle">Quick overview of all tasks with progress tracking</p>
                </div>
                <span class="badge bg-light text-dark px-3 py-2" style="font-size: 12px;">
                    Updated {{ now()->format('M d, Y') }}
                </span>
            </div>
        </div>
        <div class="section-card-body">
            <div class="row g-3">
                @forelse($mainTasks->take(8) as $task)
                    @php
                        $totalActivities = $task->activities->count();
                        $done = $task->activities->where('status', 'Completed')->count();
                        $progress = $totalActivities > 0 ? round(($done / $totalActivities) * 100) : ($task->status === 'completed' ? 100 : 15);
                        $due = $task->end_date ? \Illuminate\Support\Carbon::parse($task->end_date)->format('M d') : 'No date';
                    @endphp
                    <div class="col-md-6 col-xl-4">
                        <div class="task-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="fw-semibold">{{ $task->name }}</div>
                                <span class="pill pill-secondary text-capitalize">{{ str_replace('_',' ', $task->status) }}</span>
                            </div>
                            <div class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($task->description, 110) }}</div>
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <span class="pill pill-info">Priority: {{ $task->priority ?? 'Normal' }}</span>
                                <span class="pill pill-warning">Due: {{ $due }}</span>
                            </div>
                            <div class="progress mb-2" style="height:8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Leader: {{ $task->teamLeader->name ?? 'Unassigned' }}</div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="openReportForm({{ $task->activities->first()->id ?? 'null' }})">Log Progress</button>
                                    @if($isManager)
                                        <button class="btn btn-outline-secondary" onclick="document.getElementById('taskName').value='{{ addslashes($task->name) }}'">Clone</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">No tasks yet. Start with "Create Task".</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Activities & Quick Actions Section - Independent Component -->
    <div class="section-card">
        <div class="section-card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="section-card-title">
                        <i class="bx bx-task me-2"></i>
                        Activities & Quick Actions
                    </h3>
                    <p class="section-card-subtitle">Manage all activities and track assignments</p>
                </div>
                <div style="min-width: 250px;">
                    <input type="text" id="activitySearch" class="form-control form-control-sm" placeholder="Search activity or task...">
                </div>
            </div>
        </div>
        <div class="section-card-body p-0">
            <div class="table-responsive">
            <table class="table align-middle mb-0" id="activitiesTable">
                <thead class="table-light">
                    <tr>
                        <th>Activity</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Due</th>
                        <th>Assignees</th>
                        <th>Reports</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mainTasks as $task)
                        @foreach($task->activities as $activity)
                            @php
                                $activityReports = $activity->reports ?? collect();
                                $latestReport = $activityReports->sortByDesc('created_at')->first();
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $activity->name }}</td>
                                <td>{{ $task->name }}</td>
                                <td><span class="pill pill-secondary">{{ $activity->status ?? 'Not Started' }}</span></td>
                                <td>{{ $activity->end_date ? \Illuminate\Support\Carbon::parse($activity->end_date)->format('M d, Y') : '—' }}</td>
                                <td>
                                    @foreach($activity->assignedUsers ?? [] as $assignee)
                                        <span class="avatar-chip">{{ $assignee->name }}</span>
                                    @endforeach
                                </td>
                                <td class="small text-muted">
                                    {{ $activityReports->count() }} report(s)
                                    @if($latestReport)
                                        <div>Last: {{ \Illuminate\Support\Carbon::parse($latestReport->created_at)->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" onclick="openReportForm({{ $activity->id }})">
                                        Log Progress
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>

    @php
        $recentReports = collect($mainTasks)
            ->flatMap(function($task) {
                return $task->activities->flatMap(function($act) {
                    return $act->reports;
                });
            })
            ->sortByDesc('created_at')
            ->take(8);
    @endphp

    <!-- Latest Reports Section - Independent Component -->
    <div class="section-card">
        <div class="section-card-header">
            <div>
                <h3 class="section-card-title">
                    <i class="bx bx-file me-2"></i>
                    Latest Reports
                </h3>
                <p class="section-card-subtitle">Recent progress reports with attachments and blocker information</p>
            </div>
        </div>
        <div class="section-card-body p-0">
            <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Activity</th>
                        <th>Task</th>
                        <th>Reporter</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReports as $report)
                        @php
                            $taskName = $report->activity->mainTask->name ?? 'Task';
                            $activityName = $report->activity->name ?? 'Activity';
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $activityName }}</td>
                            <td>{{ $taskName }}</td>
                            <td>{{ $report->user->name ?? '—' }}</td>
                            <td><span class="pill pill-secondary">{{ $report->status }}</span></td>
                            <td>{{ \Illuminate\Support\Carbon::parse($report->created_at)->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($report->attachment_path)
                                    <a href="{{ Storage::url($report->attachment_path) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="bx bx-paperclip"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No reports yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    const actionUrl = '{{ route('modules.tasks.action') }}';
    const csrfToken = '{{ csrf_token() }}';

    document.getElementById('completionStatus').addEventListener('change', function () {
        document.getElementById('delayReasonWrap').style.display = ['Delayed','Behind Schedule'].includes(this.value) ? 'block' : 'none';
    });

    // Quick search on activities table
    const searchInput = document.getElementById('activitySearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#activitiesTable tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    function handleResponse(res) {
        if (res.success) {
            Swal.fire('Done', res.message || 'Saved', 'success').then(() => window.location.reload());
        } else {
            Swal.fire('Error', res.message || 'Something went wrong', 'error');
        }
    }

    // Create task
    const createTaskForm = document.getElementById('createTaskForm');
    if (createTaskForm) {
        createTaskForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const fd = new FormData(this);
            fd.append('action', 'task_create_main');

            const initActivity = document.getElementById('initialActivity').value.trim();
            if (initActivity) {
                fd.append('activities[0][name]', initActivity);
                const selectedUsers = Array.from(document.getElementById('initialActivityUsers').selectedOptions).map(o => o.value);
                selectedUsers.forEach((id, idx) => fd.append(`activities[0][users][${idx}]`, id));
            }

            fetch(actionUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: fd
            }).then(r => r.json()).then(handleResponse).catch(() => Swal.fire('Error', 'Unable to save task', 'error'));
        });
    }

    // Progress submission
    document.getElementById('progressForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'task_submit_report');

        fetch(actionUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: fd
        }).then(r => r.json()).then(handleResponse).catch(() => Swal.fire('Error', 'Unable to submit report', 'error'));
    });

    // Approve / reject
    window.approveReport = function (id) {
        Swal.fire({
            title: 'Approve report?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Approve'
        }).then(result => {
            if (!result.isConfirmed) return;
            const fd = new FormData();
            fd.append('_token', csrfToken);
            fd.append('action', 'task_approve_report');
            fd.append('report_id', id);
            fetch(actionUrl, { method: 'POST', body: fd }).then(r => r.json()).then(handleResponse);
        });
    }

    window.rejectReport = function (id) {
        Swal.fire({
            title: 'Reject report',
            input: 'textarea',
            inputPlaceholder: 'Reason',
            showCancelButton: true,
            confirmButtonText: 'Reject',
            confirmButtonColor: '#dc3545'
        }).then(result => {
            if (!result.isConfirmed || !result.value) return;
            const fd = new FormData();
            fd.append('_token', csrfToken);
            fd.append('action', 'task_reject_report');
            fd.append('report_id', id);
            fd.append('comments', result.value);
            fetch(actionUrl, { method: 'POST', body: fd }).then(r => r.json()).then(handleResponse);
        });
    }

    window.openReportForm = function (activityId) {
        if (activityId) {
            const select = document.getElementById('progressActivitySelect');
            if (select) select.value = activityId;
        }
        document.getElementById('progressForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>
@endpush


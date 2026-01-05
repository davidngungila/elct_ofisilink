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
                    <a href="{{ route('modules.tasks.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> New Task
                    </a>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">
<style>
    :root {
        --task-primary: #2563eb;
        --task-success: #16a34a;
        --task-warning: #d97706;
        --task-danger: #dc2626;
        --task-info: #4f46e5;
        --task-gray: #6b7280;
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

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--task-shadow-lg);
        border-color: var(--task-primary);
    }

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

    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--task-shadow-lg);
        border-color: var(--task-primary);
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

<!-- View Task Details Modal -->
<div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" id="view-task-content">
            <div class="modal-header">
                <h5 class="modal-title">Loading Task...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Loading task details...</p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" id="edit-task-content">
            <div class="modal-header">
                <h5 class="modal-title">Loading Task...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Loading task details...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
            setTimeout(() => {
                if (typeof initializeCalendar === 'function') {
                    initializeCalendar();
                }
            }, 100);
        } else if (target === '#analytics-view') {
            setTimeout(() => {
                if (typeof initializeAnalytics === 'function') {
                    initializeAnalytics();
                }
            }, 100);
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
@include('modules.tasks.partials.scripts', [
    'users' => $users,
    'categories' => $categories
])
@endpush


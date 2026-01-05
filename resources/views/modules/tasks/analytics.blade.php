@extends('layouts.app')

@section('title', 'Task Analytics - OfisiLink')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
    .stat-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .flatpickr-calendar {
        display: none;
    }
    .flatpickr-calendar.open {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-info" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-3 mb-md-0">
                            <h3 class="mb-2 text-white fw-bold">
                                <i class="bx bx-bar-chart-alt-2 me-2"></i>Task Analytics & Insights
                            </h3>
                            <p class="mb-0 text-white-50 fs-6">
                                Comprehensive task statistics, performance metrics, and detailed insights
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('modules.tasks.index') }}" class="btn btn-light btn-lg shadow-sm me-2">
                                <i class="bx bx-arrow-back me-2"></i>Back to Tasks
                            </a>
                            <a href="{{ route('modules.tasks.analytics.pdf') }}?{{ http_build_query(request()->query()) }}" class="btn btn-light btn-lg shadow-sm" target="_blank">
                                <i class="bx bx-file-pdf me-2"></i>Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('modules.tasks.analytics') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input type="text" name="date_from" id="dateFrom" class="form-control" value="{{ request('date_from') }}" placeholder="Select start date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input type="text" name="date_to" id="dateTo" class="form-control" value="{{ request('date_to') }}" placeholder="Select end date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">All Priorities</option>
                                <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Normal" {{ request('priority') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Critical" {{ request('priority') == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('modules.tasks.analytics') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-refresh me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card border-primary" style="border-left: 4px solid var(--bs-primary) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3 bg-primary">
                            <i class="bx bx-task fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Total Tasks</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #3b82f6 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <i class="bx bx-loader-circle fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">In Progress</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $stats['in_progress'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="bx bx-check-circle fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Completed</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $stats['completed'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <i class="bx bx-error-circle fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Overdue</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ $stats['overdue'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                            <i class="bx bx-list-check fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Total Activities</h6>
                            <h3 class="mb-0 fw-bold" style="color: #8b5cf6;">{{ $stats['total_activities'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="bx bx-file-blank fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Total Reports</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $stats['total_reports'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #06b6d4 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                            <i class="bx bx-time fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Pending Reports</h6>
                            <h3 class="mb-0 fw-bold" style="color: #06b6d4;">{{ $stats['pending_reports'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left: 4px solid #14b8a6 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                            <i class="bx bx-check-double fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Avg. Progress</h6>
                            <h3 class="mb-0 fw-bold" style="color: #14b8a6;">{{ $stats['avg_progress'] ?? 0 }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-pie-chart me-2"></i>Tasks by Priority</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-bar-chart me-2"></i>Tasks by Category</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-line-chart me-2"></i>Tasks by Status</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-trending-up me-2"></i>Progress Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-user me-2"></i>Top Team Leaders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Team Leader</th>
                                    <th>Tasks</th>
                                    <th>Completed</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topLeaders ?? [] as $leader)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-primary">{{ substr($leader['name'], 0, 1) }}</span>
                                                </div>
                                                <strong>{{ $leader['name'] }}</strong>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary">{{ $leader['total_tasks'] }}</span></td>
                                        <td><span class="badge bg-success">{{ $leader['completed_tasks'] }}</span></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $leader['avg_progress'] }}%">
                                                    {{ $leader['avg_progress'] }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-calendar me-2"></i>Recent Activity Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Task</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivity ?? [] as $activity)
                                    <tr>
                                        <td><small>{{ $activity['date'] ?? 'N/A' }}</small></td>
                                        <td><strong>{{ $activity['task'] ?? 'N/A' }}</strong></td>
                                        <td>{{ $activity['activity'] ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $activity['status_badge'] ?? 'secondary' }}">
                                                {{ $activity['status'] ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No recent activity</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bx bx-tachometer me-2"></i>Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-primary">{{ $stats['completion_rate'] ?? 0 }}%</h4>
                                <small class="text-muted">Completion Rate</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-info">{{ $stats['on_time_rate'] ?? 0 }}%</h4>
                                <small class="text-muted">On-Time Rate</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-warning">{{ $stats['avg_duration'] ?? 0 }}</h4>
                                <small class="text-muted">Avg. Duration (Days)</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1 text-success">{{ $stats['active_users'] ?? 0 }}</h4>
                                <small class="text-muted">Active Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize date pickers
    flatpickr("#dateFrom", {
        dateFormat: "Y-m-d",
        allowInput: true
    });
    
    flatpickr("#dateTo", {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    const priorityData = @json($byPriority ?? []);
    const categoryData = @json($byCategory ?? []);
    const statusData = @json($byStatus ?? []);
    const progressData = @json($byProgress ?? []);

    // Priority Chart
    if (document.getElementById('priorityChart')) {
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        new Chart(priorityCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(priorityData),
                datasets: [{
                    data: Object.values(priorityData),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Category Chart
    if (document.getElementById('categoryChart')) {
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    label: 'Tasks',
                    data: Object.values(categoryData),
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Status Chart
    if (document.getElementById('statusChart')) {
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Progress Chart
    if (document.getElementById('progressChart')) {
        const progressCtx = document.getElementById('progressChart').getContext('2d');
        new Chart(progressCtx, {
            type: 'line',
            data: {
                labels: Object.keys(progressData),
                datasets: [{
                    label: 'Progress %',
                    data: Object.values(progressData),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
});
</script>
@endpush

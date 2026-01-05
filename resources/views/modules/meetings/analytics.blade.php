@extends('layouts.app')

@section('title', 'Meeting Analytics - OfisiLink')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* Hide flatpickr calendar if not attached to input */
.flatpickr-calendar:not(.open) {
    display: none !important;
}
.flatpickr-calendar.open {
    z-index: 9999;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title text-white mb-1">
                                <i class="bx bx-bar-chart me-2"></i>Meeting Analytics
                            </h4>
                            <p class="card-text text-white-50 mb-0">View comprehensive meeting statistics and insights</p>
                        </div>
                        <div>
                            <a href="{{ route('modules.meetings.index') }}" class="btn btn-light">
                                <i class="bx bx-arrow-back me-1"></i>Back to Meetings
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
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Branch</label>
                            <select class="form-select" id="filter-branch">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }} ({{ $branch->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="filter-date" placeholder="Select date range">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="apply-filters">
                                <i class="bx bx-filter"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Meetings</h6>
                            <h3 class="mb-0 text-primary" id="stat-total">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded"><i class="bx bx-calendar"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Upcoming</h6>
                            <h3 class="mb-0 text-success" id="stat-upcoming">{{ $stats['upcoming'] ?? 0 }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded"><i class="bx bx-calendar-check"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Pending Approval</h6>
                            <h3 class="mb-0 text-warning" id="stat-pending">{{ $stats['pending_approval'] ?? 0 }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded"><i class="bx bx-time-five"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Completed</h6>
                            <h3 class="mb-0 text-info" id="stat-completed">{{ $stats['completed'] ?? 0 }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded"><i class="bx bx-check-double"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Meetings by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Meetings by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const csrfToken = '{{ csrf_token() }}';
const ajaxUrl = '{{ route("modules.meetings.ajax") }}';

// Initialize date picker only if element exists and is visible
$(document).ready(function() {
    const dateInput = document.getElementById('filter-date');
    if (dateInput) {
        flatpickr(dateInput, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            appendTo: document.body,
            static: false,
            clickOpens: true
        });
    }
});

// Load analytics data
function loadAnalytics() {
    const branchId = $('#filter-branch').val();
    const dateRange = $('#filter-date').val();

    $.ajax({
        url: ajaxUrl,
        method: 'POST',
        data: {
            _token: csrfToken,
            action: 'get_dashboard_stats',
            branch_id: branchId,
            date_range: dateRange
        },
        success: function(response) {
            if (response.success) {
                updateCharts(response.stats);
            }
        }
    });
}

// Update charts
function updateCharts(stats) {
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Pending', 'Approved', 'Completed', 'Rejected'],
            datasets: [{
                data: [
                    stats.draft || 0,
                    stats.pending_approval || 0,
                    stats.approved || 0,
                    stats.completed || 0,
                    stats.rejected || 0
                ],
                backgroundColor: [
                    '#6c757d',
                    '#ffc107',
                    '#28a745',
                    '#17a2b8',
                    '#dc3545'
                ]
            }]
        }
    });

    // Category Chart (placeholder - would need category data)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: ['Category 1', 'Category 2', 'Category 3'],
            datasets: [{
                label: 'Meetings',
                data: [10, 20, 15],
                backgroundColor: '#007bff'
            }]
        }
    });
}

// Event listeners
$('#apply-filters').on('click', loadAnalytics);

// Initial load
loadAnalytics();
</script>
@endpush


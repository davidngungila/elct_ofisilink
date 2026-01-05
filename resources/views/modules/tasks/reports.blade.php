@extends('layouts.app')

@section('title', 'Task Reports - OfisiLink')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    .report-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
    }
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .report-card.pending { border-left-color: #f59e0b; }
    .report-card.approved { border-left-color: #10b981; }
    .report-card.rejected { border-left-color: #ef4444; }
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
                                <i class="bx bx-file-blank me-2"></i>Progress Reports
                            </h3>
                            <p class="mb-0 text-white-50 fs-6">
                                View and track all activity progress reports and their approval status
                            </p>
                        </div>
                        <div>
                            @if($isManager)
                                <a href="{{ route('modules.tasks.reports.pending-approval') }}" class="btn btn-light btn-lg shadow-sm me-2">
                                    <i class="bx bx-time me-2"></i>Pending Approval
                                </a>
                            @endif
                            <a href="{{ route('modules.tasks.index') }}" class="btn btn-light btn-lg shadow-sm">
                                <i class="bx bx-arrow-back me-2"></i>Back to Tasks
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-primary" style="border-left: 4px solid var(--bs-primary) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3 bg-primary">
                            <i class="bx bx-file fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Total Reports</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="bx bx-time fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Pending</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $stats['pending'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="bx bx-check-circle fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Approved</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $stats['approved'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <i class="bx bx-x-circle fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Rejected</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ $stats['rejected'] ?? 0 }}</h3>
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
                    <form method="GET" action="{{ route('modules.tasks.reports') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ $statusFilter == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Approved" {{ $statusFilter == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Rejected" {{ $statusFilter == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Completion Status</label>
                            <select name="completion_status" class="form-select">
                                <option value="">All</option>
                                <option value="In Progress" {{ $completionStatusFilter == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ $completionStatusFilter == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Delayed" {{ $completionStatusFilter == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $searchFilter }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('modules.tasks.reports') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-refresh me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>All Reports ({{ $reports->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($reports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Report Date</th>
                                        <th>Reporter</th>
                                        <th>Activity</th>
                                        <th>Task</th>
                                        <th>Completion Status</th>
                                        <th>Approval Status</th>
                                        <th>Approved By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                    <tr class="report-card {{ strtolower($report->status) }}">
                                        <td>
                                            <strong>{{ $report->report_date->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
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
                                            <strong>{{ $report->activity->name ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('modules.tasks.show', $report->activity->mainTask->id ?? '#') }}" class="text-primary">
                                                {{ $report->activity->mainTask->name ?? 'N/A' }}
                                            </a>
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
                                            <a href="{{ route('modules.tasks.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bx bx-show"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $reports->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-file-blank fs-1 text-muted mb-3"></i>
                            <p class="text-muted">No reports found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


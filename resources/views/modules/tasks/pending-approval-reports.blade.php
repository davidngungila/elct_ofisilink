@extends('layouts.app')

@section('title', 'Pending Approval Reports - OfisiLink')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
<style>
    .report-card {
        transition: all 0.3s ease;
        border-left: 4px solid #f59e0b;
    }
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .priority-high { border-left-color: #ef4444; }
    .priority-medium { border-left-color: #f59e0b; }
    .priority-low { border-left-color: #10b981; }
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
                                <i class="bx bx-time me-2"></i>Pending Approval Reports
                            </h3>
                            <p class="mb-0 text-white-50 fs-6">
                                Review and approve progress reports submitted by assigned staff
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('modules.tasks.reports') }}" class="btn btn-light btn-lg shadow-sm me-2">
                                <i class="bx bx-file-blank me-2"></i>All Reports
                            </a>
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
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-warning" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="bx bx-time fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Total Pending</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $stats['total_pending'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #3b82f6 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <i class="bx bx-calendar fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">Pending Today</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $stats['pending_today'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                            <i class="bx bx-calendar-week fs-2 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1 small">This Week</h6>
                            <h3 class="mb-0 fw-bold text-purple">{{ $stats['pending_this_week'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('modules.tasks.reports.pending-approval') }}" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Search by activity, task, reporter name..." value="{{ $searchFilter }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-search me-1"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Reports List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>Reports Awaiting Approval ({{ $reports->total() }})</h5>
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
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                    <tr class="report-card">
                                        <td>
                                            <strong>{{ $report->report_date->format('M d, Y') }}</strong><br>
                                            <small class="text-muted">{{ $report->report_date->format('l') }}</small>
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
                                                <strong>{{ $report->activity->mainTask->name ?? 'N/A' }}</strong>
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
                                            <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small><br>
                                            <small class="text-muted">{{ $report->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('modules.tasks.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="bx bx-show"></i> View
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="approveReport({{ $report->id }})" title="Approve">
                                                    <i class="bx bx-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="rejectReport({{ $report->id }})" title="Reject">
                                                    <i class="bx bx-x"></i>
                                                </button>
                                            </div>
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
                            <i class="bx bx-check-circle fs-1 text-success mb-3"></i>
                            <p class="text-muted">No pending reports. All reports have been reviewed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve/Reject Modal -->
<div class="modal fade" id="approveRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveRejectModalTitle">Approve Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveRejectForm">
                <div class="modal-body">
                    <input type="hidden" id="reportIdForAction" name="report_id">
                    <input type="hidden" id="actionType" name="action">
                    <div class="mb-3">
                        <label class="form-label">Comments (Optional)</label>
                        <textarea name="comments" id="approverComments" class="form-control" rows="3" placeholder="Add any comments or feedback..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="submitActionBtn">
                        <span id="submitActionText">Approve</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
const csrfToken = '{{ csrf_token() }}';
const ajaxUrl = '{{ route("modules.tasks.action") }}';

function approveReport(reportId) {
    $('#reportIdForAction').val(reportId);
    $('#actionType').val('approve');
    $('#approveRejectModalTitle').text('Approve Report');
    $('#submitActionBtn').removeClass('btn-danger').addClass('btn-success');
    $('#submitActionText').text('Approve Report');
    $('#approverComments').val('');
    $('#approveRejectModal').modal('show');
}

function rejectReport(reportId) {
    $('#reportIdForAction').val(reportId);
    $('#actionType').val('reject');
    $('#approveRejectModalTitle').text('Reject Report');
    $('#submitActionBtn').removeClass('btn-success').addClass('btn-danger');
    $('#submitActionText').text('Reject Report');
    $('#approverComments').val('');
    $('#approveRejectModal').modal('show');
}

$('#approveRejectForm').on('submit', function(e) {
    e.preventDefault();
    const reportId = $('#reportIdForAction').val();
    const action = $('#actionType').val();
    const comments = $('#approverComments').val();

    $.ajax({
        url: ajaxUrl,
        method: 'POST',
        data: {
            _token: csrfToken,
            action: action === 'approve' ? 'task_approve_report' : 'task_reject_report',
            report_id: reportId,
            comments: comments
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: action === 'approve' ? 'Report Approved' : 'Report Rejected',
                    text: response.message || 'Action completed successfully',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#approveRejectModal').modal('hide');
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', response.message || 'Failed to process request', 'error');
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Failed to process request';
            Swal.fire('Error', message, 'error');
        }
    });
});
</script>
@endpush


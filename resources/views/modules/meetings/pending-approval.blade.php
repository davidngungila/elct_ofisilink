@extends('layouts.app')

@section('title', 'Pending Approval Meetings - OfisiLink')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.css') }}">
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
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title text-white mb-1">
                                <i class="bx bx-time me-2"></i>Pending Approval Meetings
                            </h4>
                            <p class="card-text text-white-50 mb-0">Review and approve pending meeting requests</p>
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="filter-search" placeholder="Search meetings...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <input type="text" class="form-control" id="filter-date" placeholder="Select date range">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="apply-filters">
                                <i class="bx bx-filter"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Meetings List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-list-ul me-2"></i>Pending Meetings</h5>
                </div>
                <div class="card-body">
                    <div id="pending-meetings-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
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
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
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

// Load pending meetings
function loadPendingMeetings() {
    const branchId = $('#filter-branch').val();
    const search = $('#filter-search').val();
    const dateRange = $('#filter-date').val();

    $.ajax({
        url: ajaxUrl,
        method: 'POST',
        data: {
            _token: csrfToken,
            action: 'get_meetings',
            status: 'pending_approval',
            branch_id: branchId,
            search: search,
            date_range: dateRange
        },
        success: function(response) {
            if (response.success && response.meetings) {
                renderPendingMeetings(response.meetings);
            } else {
                $('#pending-meetings-container').html('<div class="text-center py-5 text-muted"><i class="bx bx-calendar-x" style="font-size: 3rem;"></i><p>No pending meetings found</p></div>');
            }
        },
        error: function() {
            $('#pending-meetings-container').html('<div class="alert alert-danger">Failed to load pending meetings. Please try again.</div>');
        }
    });
}

// Render pending meetings
function renderPendingMeetings(meetings) {
    if (meetings.length === 0) {
        $('#pending-meetings-container').html('<div class="text-center py-5 text-muted"><i class="bx bx-calendar-x" style="font-size: 3rem;"></i><p>No pending meetings found</p></div>');
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Title</th><th>Date & Time</th><th>Venue</th><th>Branch</th><th>Created By</th><th>Actions</th></tr></thead><tbody>';
    
    meetings.forEach(meeting => {
        html += `
            <tr>
                <td><strong>${escapeHtml(meeting.title)}</strong></td>
                <td>${meeting.meeting_date}<br><small class="text-muted">${meeting.start_time} - ${meeting.end_time}</small></td>
                <td>${escapeHtml(meeting.venue || 'TBD')}</td>
                <td>${escapeHtml(meeting.branch_name || 'N/A')}</td>
                <td>${escapeHtml(meeting.creator_name || 'N/A')}</td>
                <td>
                    <a href="/modules/meetings/${meeting.id}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-show"></i> View
                    </a>
                    @if(isset($canApproveMeetings) && $canApproveMeetings)
                    <button class="btn btn-sm btn-outline-success approve-meeting-btn" data-id="${meeting.id}" title="Approve">
                        <i class="bx bx-check"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger reject-meeting-btn" data-id="${meeting.id}" title="Reject">
                        <i class="bx bx-x"></i>
                    </button>
                    @endif
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    $('#pending-meetings-container').html(html);
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
}

// Approve Meeting
$(document).on('click', '.approve-meeting-btn', function() {
    const meetingId = $(this).data('id');
    Swal.fire({
        title: 'Approve Meeting?',
        html: 'This will approve the meeting and send SMS notifications to all participants.<br><br><textarea id="approval-message" class="form-control" rows="3" placeholder="Custom message for SMS (optional)"></textarea>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Approve & Send SMS',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            const message = $('#approval-message').val();
            $.ajax({
                url: ajaxUrl,
                method: 'POST',
                data: {
                    _token: csrfToken,
                    action: 'approve_meeting',
                    meeting_id: meetingId,
                    custom_message: message
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Approved!', 'Meeting approved and SMS sent to participants', 'success');
                        loadPendingMeetings();
                    } else {
                        Swal.fire('Error', response.message || 'Failed to approve meeting', 'error');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Failed to approve meeting. Please try again.';
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    });
});

// Reject Meeting
$(document).on('click', '.reject-meeting-btn', function() {
    const meetingId = $(this).data('id');
    Swal.fire({
        title: 'Reject Meeting?',
        input: 'textarea',
        inputPlaceholder: 'Reason for rejection...',
        inputAttributes: { required: true },
        showCancelButton: true,
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            $.ajax({
                url: ajaxUrl,
                method: 'POST',
                data: {
                    _token: csrfToken,
                    action: 'reject_meeting',
                    meeting_id: meetingId,
                    reason: result.value
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Rejected', 'Meeting has been rejected', 'info');
                        loadPendingMeetings();
                    } else {
                        Swal.fire('Error', response.message || 'Failed to reject meeting', 'error');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Failed to reject meeting. Please try again.';
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    });
});

// Event listeners
$('#apply-filters').on('click', loadPendingMeetings);
$('#filter-branch, #filter-search').on('change keyup', loadPendingMeetings);

// Initial load
loadPendingMeetings();
</script>
@endpush


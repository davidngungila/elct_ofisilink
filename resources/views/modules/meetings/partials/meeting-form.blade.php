@php
    $isEdit = isset($meeting) && $meeting;
    $users = $users ?? collect();
@endphp

<form id="meetingForm" method="POST" action="{{ $isEdit ? route('modules.meetings.update', $meeting->id) : route('modules.meetings.store') }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="meeting_id" id="meeting_id" value="{{ $isEdit ? $meeting->id : '' }}">

    <!-- Basic Information -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>Basic Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Meeting Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg" required 
                           value="{{ $isEdit ? $meeting->title : '' }}" 
                           placeholder="Enter meeting title">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select form-select-lg" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ ($isEdit && $meeting->category_id == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Meeting Date <span class="text-danger">*</span></label>
                    <input type="date" name="meeting_date" class="form-control" required 
                           value="{{ $isEdit ? $meeting->meeting_date : '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" name="start_time" class="form-control" required 
                           value="{{ $isEdit ? $meeting->start_time : '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" name="end_time" class="form-control" required 
                           value="{{ $isEdit ? $meeting->end_time : '' }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Meeting Type <span class="text-danger">*</span></label>
                    <select name="meeting_type" id="meeting_type" class="form-select" required>
                        @php
                            $currentType = '';
                            if ($isEdit) {
                                // Check meeting_type first (newer schema)
                                if (isset($meeting->meeting_type)) {
                                    $currentType = $meeting->meeting_type;
                                } 
                                // Fallback to meeting_mode (older schema) and map values
                                elseif (isset($meeting->meeting_mode)) {
                                    $mode = $meeting->meeting_mode;
                                    if ($mode == 'in_person') {
                                        $currentType = 'physical';
                                    } else {
                                        $currentType = $mode; // virtual or hybrid
                                    }
                                }
                            }
                        @endphp
                        <option value="physical" {{ ($isEdit && $currentType == 'physical') ? 'selected' : '' }}>Physical (In-Person)</option>
                        <option value="virtual" {{ ($isEdit && $currentType == 'virtual') ? 'selected' : '' }}>Virtual (Online)</option>
                        <option value="hybrid" {{ ($isEdit && $currentType == 'hybrid') ? 'selected' : '' }}>Hybrid</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">Select Branch (Optional)</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ ($isEdit && $meeting->branch_id == $branch->id) ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Venue/Location <span class="text-danger">*</span></label>
                    <input type="text" name="venue" id="venue_location" class="form-control" required 
                           value="{{ $isEdit ? ($meeting->venue ?? $meeting->location) : '' }}" 
                           placeholder="Enter venue or meeting link">
                    <small class="text-muted" id="venue_help">Physical location or virtual meeting link</small>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description / Objectives</label>
                <textarea name="description" class="form-control" rows="4" 
                          placeholder="Enter meeting description, objectives, or agenda overview">{{ $isEdit ? ($meeting->description ?? $meeting->agenda_overview) : '' }}</textarea>
            </div>
        </div>
    </div>

    <!-- Approval Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="card-title mb-0"><i class="bx bx-check-circle me-2"></i>Approval Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Select Approver <small class="text-muted">(Required when submitting for approval)</small></label>
                    <select name="approver_id" id="approver_id" class="form-select form-select-lg">
                        <option value="">Select Approver</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ ($isEdit && (isset($meeting->approver_id) && $meeting->approver_id == $u->id)) ? 'selected' : '' }}>
                                {{ $u->name }}
                                @if($u->primaryDepartment)
                                    - {{ $u->primaryDepartment->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">
                        <i class="bx bx-info-circle"></i> Select who will approve this meeting when you submit it for approval. Choose someone with approval permissions (System Admin, General Manager, HOD, or HR Officer).
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0"><i class="bx bx-user-plus me-2"></i>Participants</h5>
        </div>
        <div class="card-body">
            <!-- Internal Staff Participants -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-3">
                    <i class="bx bx-user me-2"></i>Internal Staff Participants
                </label>
                <select name="staff_participants[]" id="staff_participants" class="form-select select2" multiple="multiple" 
                        data-placeholder="Select staff members to invite" style="width: 100%;">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ ($isEdit && isset($meeting->staff_participants) && in_array($u->id, $meeting->staff_participants ?? [])) ? 'selected' : '' }}>
                            {{ $u->name }}
                            @if($u->primaryDepartment)
                                - {{ $u->primaryDepartment->name }}
                            @endif
                            @if($u->employee_id)
                                ({{ $u->employee_id }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <small class="text-muted d-block mt-2">
                    <i class="bx bx-info-circle"></i> Select multiple staff members from your organization
                </small>
                <!-- Selected Staff Display -->
                <div id="selected-staff-display" class="mt-3">
                    <h6 class="mb-2 fw-semibold">Selected Participants:</h6>
                    <div id="selected-staff-list" class="d-flex flex-wrap gap-2">
                        @if($isEdit && isset($meeting->staff_participants) && count($meeting->staff_participants) > 0)
                            @foreach($meeting->staff_participants as $staffId)
                                @php
                                    $staffUser = $users->firstWhere('id', $staffId);
                                @endphp
                                @if($staffUser)
                                    <span class="badge bg-primary p-2 mb-2" data-user-id="{{ $staffId }}">
                                        <i class="bx bx-user me-1"></i>
                                        {{ $staffUser->name }}
                                        @if($staffUser->primaryDepartment)
                                            - {{ $staffUser->primaryDepartment->name }}
                                        @endif
                                    </span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- External Participants -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-label fw-semibold mb-0">
                        <i class="bx bx-user-voice me-2"></i>External Participants
                    </label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-external-participant-btn">
                        <i class="bx bx-plus"></i> Add External Participant
                    </button>
                </div>
                <small class="text-muted d-block mb-3">
                    <i class="bx bx-info-circle"></i> Add external participants (guests, clients, partners, etc.)
                </small>
                <div id="external-participants-container">
                    @if($isEdit && isset($externalParticipants) && $externalParticipants->count() > 0)
                        @foreach($externalParticipants as $index => $extParticipant)
                            <div class="external-participant-item card mb-3" data-index="{{ $index }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">External Participant #<span class="participant-number">{{ $index + 1 }}</span></h6>
                                        <button type="button" class="btn btn-sm btn-danger remove-external-participant">
                                            <i class="bx bx-trash"></i> Remove
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="external_name[]" class="form-control" required placeholder="Enter full name" value="{{ $extParticipant->name ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="external_email[]" class="form-control" placeholder="Enter email address" value="{{ $extParticipant->email ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="external_phone[]" class="form-control" placeholder="Enter phone number" value="{{ $extParticipant->phone ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Institution/Organization</label>
                                            <input type="text" name="external_institution[]" class="form-control" placeholder="Enter institution or organization" value="{{ $extParticipant->institution ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <!-- External participants will be added here dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Agenda Items Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0"><i class="bx bx-list-check me-2"></i>Agenda Items</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i> Add agenda items to structure your meeting
                </small>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-agenda-item-btn">
                    <i class="bx bx-plus"></i> Add Agenda Item
                </button>
            </div>
            <div id="agenda-items-container">
                <!-- Agenda items will be added here dynamically -->
            </div>
        </div>
    </div>

    <!-- Meeting Resolutions Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0"><i class="bx bx-file-blank me-2"></i>Meeting Resolutions</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-muted">
                    <i class="bx bx-info-circle"></i> Prepare resolutions that will be discussed and voted on during the meeting
                </small>
                <button type="button" class="btn btn-sm btn-outline-warning" id="add-resolution-btn">
                    <i class="bx bx-plus"></i> Add Resolution
                </button>
            </div>
            <div id="resolutions-container">
                <!-- Resolutions will be added here dynamically -->
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ $isEdit ? route('modules.meetings.show', $meeting->id) : route('modules.meetings.index') }}" class="btn btn-secondary btn-lg">
                    <i class="bx bx-x me-1"></i>Cancel
                </a>
                <div>
                    <button type="submit" class="btn btn-primary btn-lg me-2" name="action" value="save">
                        <i class="bx bx-save me-1"></i>{{ $isEdit ? 'Update' : 'Save' }} Meeting
                    </button>
                    @if(!$isEdit)
                        <button type="submit" class="btn btn-success btn-lg" name="action" value="submit">
                            <i class="bx bx-check me-1"></i>Save & Submit for Approval
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

<!-- External Participant Template -->
<template id="external-participant-template">
    <div class="external-participant-item card mb-3" data-index="">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">External Participant #<span class="participant-number"></span></h6>
                <button type="button" class="btn btn-sm btn-danger remove-external-participant">
                    <i class="bx bx-trash"></i> Remove
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="external_name[]" class="form-control" required placeholder="Enter full name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="external_email[]" class="form-control" placeholder="Enter email address">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="external_phone[]" class="form-control" placeholder="Enter phone number">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Institution/Organization</label>
                    <input type="text" name="external_institution[]" class="form-control" placeholder="Enter institution or organization">
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Resolution Template -->
<template id="resolution-template">
    <div class="resolution-item card mb-3" data-index="" style="border-left: 3px solid #ffc107;">
        <div class="card-body" style="background-color: #fffbf0;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Resolution #<span class="resolution-number"></span></h6>
                <button type="button" class="btn btn-sm btn-danger remove-resolution">
                    <i class="bx bx-trash"></i> Remove
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Resolution Number</label>
                    <input type="text" name="resolution_number[]" class="form-control" placeholder="e.g., RES-001 (auto-generated if empty)">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="resolution_status[]" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="proposed">Proposed</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="deferred">Deferred</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Resolution Title <span class="text-danger">*</span></label>
                <input type="text" name="resolution_title[]" class="form-control" required placeholder="Enter resolution title">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="resolution_description[]" class="form-control" rows="2" placeholder="Enter background/context for this resolution"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Resolution Text <span class="text-danger">*</span></label>
                <textarea name="resolution_text[]" class="form-control" rows="4" required placeholder="Enter the actual resolution statement that will be voted on"></textarea>
                <small class="text-muted">This is the formal resolution statement that will appear in the resolutions document</small>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Proposed By</label>
                    <select name="resolution_proposed_by[]" class="form-select select2-resolution-proposer">
                        <option value="">Select Person</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Seconded By</label>
                    <select name="resolution_seconded_by[]" class="form-select select2-resolution-seconder">
                        <option value="">Select Person</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Agenda Item Template -->
<template id="agenda-item-template">
    <div class="agenda-item card mb-3" data-index="">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Agenda Item #<span class="agenda-number"></span></h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary move-agenda-up" title="Move Up">
                        <i class="bx bx-up-arrow"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary move-agenda-down" title="Move Down">
                        <i class="bx bx-down-arrow"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger remove-agenda-item">
                        <i class="bx bx-trash"></i> Remove
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Agenda Title <span class="text-danger">*</span></label>
                    <input type="text" name="agenda_title[]" class="form-control" required placeholder="Enter agenda item title">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Presenter</label>
                    <select name="agenda_presenter[]" class="form-select select2-agenda-presenter">
                        <option value="">Select Presenter</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration</label>
                    <input type="text" name="agenda_duration[]" class="form-control" placeholder="e.g., 15 mins, 30 minutes, 1 hour">
                    <small class="text-muted">Enter duration (e.g., "15 mins", "30 minutes", "1 hour")</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Supporting Documents</label>
                    <input type="file" name="agenda_documents[]" class="form-control agenda-document-upload" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png">
                    <small class="text-muted">Upload supporting documents (PDF, Word, Excel, PowerPoint, Images)</small>
                    <div class="uploaded-documents mt-2" style="display: none;">
                        <small class="text-success"><i class="bx bx-check-circle"></i> <span class="document-count">0</span> file(s) selected</small>
                        <ul class="list-unstyled mt-1 mb-0 uploaded-file-list"></ul>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="agenda_description[]" class="form-control" rows="2" placeholder="Enter detailed description of the agenda item"></textarea>
            </div>
        </div>
    </div>
</template>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
    .external-participant-item, .agenda-item {
        border-left: 3px solid #007bff;
    }
    .external-participant-item .card-body, .agenda-item .card-body {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for staff participants
    $('#staff_participants').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select staff members to invite',
        allowClear: true,
        width: '100%'
    });

    // Display selected staff participants
    function updateSelectedStaffDisplay() {
        const selectedIds = $('#staff_participants').val() || [];
        const $list = $('#selected-staff-list');
        
        // Get existing badges (pre-loaded from edit mode)
        const existingBadges = $list.find('.badge').map(function() {
            return $(this).attr('data-user-id');
        }).get();
        
        // Remove badges for unselected users (but keep pre-loaded ones if they're still selected)
        $list.find('.badge').each(function() {
            const userId = $(this).attr('data-user-id');
            if (userId && selectedIds.indexOf(userId.toString()) === -1) {
                $(this).remove();
            }
        });
        
        // Add badges for newly selected users
        selectedIds.forEach(function(userId) {
            // Check if badge already exists
            if ($list.find('.badge[data-user-id="' + userId + '"]').length === 0) {
                const $option = $('#staff_participants option[value="' + userId + '"]');
                if ($option.length) {
                    const text = $option.text();
                    const $badge = $('<span class="badge bg-primary p-2 mb-2" data-user-id="' + userId + '">' + 
                        '<i class="bx bx-user me-1"></i>' + text + 
                        '</span>');
                    $list.append($badge);
                }
            }
        });
    }

    // Update display when selection changes
    $('#staff_participants').on('change', function() {
        updateSelectedStaffDisplay();
    });

    // Initial display update - ensure existing participants are shown
    setTimeout(function() {
        updateSelectedStaffDisplay();
    }, 500);

    // Initialize Select2 for agenda presenters (will be initialized when agenda items are added)
    
    // Handle meeting type change to show/hide virtual link
    $('#meeting_type').on('change', function() {
        const meetingType = $(this).val();
        const venueInput = $('#venue_location');
        const venueHelp = $('#venue_help');
        
        if (meetingType === 'virtual') {
            venueInput.attr('placeholder', 'Enter virtual meeting link (e.g., Zoom, Teams, Google Meet URL)');
            venueHelp.text('Enter the virtual meeting link/URL');
        } else if (meetingType === 'hybrid') {
            venueInput.attr('placeholder', 'Enter physical location and virtual link');
            venueHelp.text('Enter both physical location and virtual meeting link');
        } else {
            venueInput.attr('placeholder', 'Enter venue or meeting link');
            venueHelp.text('Physical location or virtual meeting link');
        }
    });

    // External Participants Management
    let externalParticipantCount = {{ $isEdit && isset($externalParticipants) ? $externalParticipants->count() : 0 }};
    
    $('#add-external-participant-btn').on('click', function() {
        const template = $('#external-participant-template').html();
        const $newItem = $(template);
        $newItem.attr('data-index', externalParticipantCount);
        $newItem.find('.participant-number').text(externalParticipantCount + 1);
        // Keep array notation as [] for proper PHP array handling
        $('#external-participants-container').append($newItem);
        externalParticipantCount++;
    });

    $(document).on('click', '.remove-external-participant', function() {
        $(this).closest('.external-participant-item').fadeOut(300, function() {
            $(this).remove();
            updateExternalParticipantNumbers();
        });
    });

    function updateExternalParticipantNumbers() {
        $('#external-participants-container .external-participant-item').each(function(index) {
            $(this).find('.participant-number').text(index + 1);
        });
    }

    // Agenda Items Management
    let agendaItemCount = 0;
    
    $('#add-agenda-item-btn').on('click', function() {
        const template = $('#agenda-item-template').html();
        const $newItem = $(template);
        $newItem.attr('data-index', agendaItemCount);
        $newItem.find('.agenda-number').text(agendaItemCount + 1);
        // Keep array notation as [] for proper PHP array handling
        // File inputs need special handling for nested arrays
        $newItem.find('input[type="file"]').attr('name', 'agenda_documents[' + agendaItemCount + '][]');
        
        // Initialize Select2 for presenter select in this agenda item
        $newItem.find('.select2-agenda-presenter').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select Presenter',
            allowClear: true,
            width: '100%'
        });
        
        // Initialize file upload handler for this agenda item
        $newItem.find('.agenda-document-upload').on('change', function() {
            handleAgendaFileUpload($(this));
        });
        
        $('#agenda-items-container').append($newItem);
        agendaItemCount++;
    });
    
    function handleAgendaFileUpload($input) {
        const files = $input[0].files;
        const $container = $input.closest('.col-md-6');
        const $uploadedDiv = $container.find('.uploaded-documents');
        const $fileList = $container.find('.uploaded-file-list');
        
        if (files.length > 0) {
            $uploadedDiv.show();
            $container.find('.document-count').text(files.length);
            $fileList.empty();
            
            Array.from(files).forEach(function(file) {
                const $li = $('<li class="small mb-1"><i class="bx bx-file text-primary"></i> ' + file.name + ' <span class="text-muted">(' + formatFileSize(file.size) + ')</span></li>');
                $fileList.append($li);
            });
        } else {
            $uploadedDiv.hide();
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    $(document).on('click', '.remove-agenda-item', function() {
        $(this).closest('.agenda-item').fadeOut(300, function() {
            $(this).remove();
            updateAgendaItemNumbers();
        });
    });

    $(document).on('click', '.move-agenda-up', function() {
        const $item = $(this).closest('.agenda-item');
        const $prev = $item.prev('.agenda-item');
        if ($prev.length) {
            $item.fadeOut(200, function() {
                $item.insertBefore($prev);
                $item.fadeIn(200);
                updateAgendaItemNumbers();
            });
        }
    });

    $(document).on('click', '.move-agenda-down', function() {
        const $item = $(this).closest('.agenda-item');
        const $next = $item.next('.agenda-item');
        if ($next.length) {
            $item.fadeOut(200, function() {
                $item.insertAfter($next);
                $item.fadeIn(200);
                updateAgendaItemNumbers();
            });
        }
    });

    function updateAgendaItemNumbers() {
        $('#agenda-items-container .agenda-item').each(function(index) {
            $(this).find('.agenda-number').text(index + 1);
        });
    }

    // Resolutions Management
    let resolutionCount = 0;
    
    $('#add-resolution-btn').on('click', function() {
        const template = $('#resolution-template').html();
        const $newItem = $(template);
        $newItem.attr('data-index', resolutionCount);
        $newItem.find('.resolution-number').text(resolutionCount + 1);
        
        // Auto-generate resolution number if empty
        const resolutionNumber = 'RES-' + String(resolutionCount + 1).padStart(3, '0');
        $newItem.find('input[name="resolution_number[]"]').val(resolutionNumber);
        
        $('#resolutions-container').append($newItem);
        
        // Initialize Select2 for the new resolution proposer/seconder fields
        $newItem.find('.select2-resolution-proposer, .select2-resolution-seconder').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select person',
            allowClear: true,
            width: '100%'
        });
        
        resolutionCount++;
        updateResolutionNumbers();
    });

    $(document).on('click', '.remove-resolution', function() {
        $(this).closest('.resolution-item').fadeOut(300, function() {
            $(this).remove();
            updateResolutionNumbers();
        });
    });

    function updateResolutionNumbers() {
        $('#resolutions-container .resolution-item').each(function(index) {
            $(this).find('.resolution-number').text(index + 1);
            // Update resolution number if it matches the pattern
            const $numberInput = $(this).find('input[name="resolution_number[]"]');
            const currentVal = $numberInput.val();
            if (!currentVal || currentVal.match(/^RES-\d+$/)) {
                const newNumber = 'RES-' + String(index + 1).padStart(3, '0');
                $numberInput.val(newNumber);
            }
        });
    }

    // Initialize Flatpickr for date and time inputs
    if (typeof flatpickr !== 'undefined') {
        // Date picker
        $('input[name="meeting_date"]').flatpickr({
            dateFormat: 'Y-m-d',
            minDate: 'today',
            altInput: true,
            altFormat: 'F j, Y',
            defaultDate: 'today'
        });
        
        // Time pickers
        $('input[name="start_time"]').flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true
        });
        
        $('input[name="end_time"]').flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true
        });
    }

    // Form validation before submission
    $('#meetingForm').on('submit', function(e) {
        // Validate required fields
        let isValid = true;
        const title = $('input[name="title"]').val();
        const meetingDate = $('input[name="meeting_date"]').val();
        const startTime = $('input[name="start_time"]').val();
        const endTime = $('input[name="end_time"]').val();
        const venue = $('input[name="venue"]').val();
        
        if (!title || !meetingDate || !startTime || !endTime || !venue) {
            isValid = false;
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields (Title, Date, Start Time, End Time, and Venue).'
            });
        }
        
        // Validate time range
        if (isValid && startTime && endTime) {
            const start = new Date('2000-01-01 ' + startTime);
            const end = new Date('2000-01-01 ' + endTime);
            if (end <= start) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'End time must be after start time.'
                });
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading indicator
        const submitBtn = $(this).find('button[type="submit"]:focus, button[type="submit"]:last');
        if (submitBtn.length) {
            submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Processing...');
        }
    });
});
</script>
@endpush

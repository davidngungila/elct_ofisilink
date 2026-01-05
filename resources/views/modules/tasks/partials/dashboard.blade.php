<!-- Dashboard Section - Independent Component -->
<div class="section-card need-action-section">
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

<!-- Task Creation & Progress Report Section -->
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
                            <label class="form-label">Activity Name</label>
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

    <div class="{{ $isManager ? 'col-xl-7' : 'col-xl-12' }}">
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
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" id="reportAttachments" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.xls,.xlsx,.txt">
                            <div class="input-hint">Upload documents, images, or other files (multiple files allowed).</div>
                            <div id="attachmentPreview" class="mt-2"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Work Done *</label>
                            <textarea name="work_description" rows="3" class="form-control" placeholder="Be concise but detailed. What did you complete?" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Next Activities</label>
                            <textarea name="next_activities" rows="2" class="form-control" placeholder="What's next? Who needs to act?"></textarea>
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

<!-- Task Board Section -->
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


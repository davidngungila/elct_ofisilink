<!-- List View Section - Independent Component -->
<div class="section-card">
    <div class="section-card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="section-card-title">
                    <i class="bx bx-list-ul me-2"></i>
                    All Tasks
                </h3>
                <p class="section-card-subtitle">Complete list of tasks with filters and search functionality</p>
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="taskSearchInput" class="form-control form-control-sm" placeholder="Search tasks..." style="min-width: 200px;">
                <select class="form-select form-select-sm" id="filterStatus" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
                <select class="form-select form-select-sm" id="filterPriority" style="width: auto;">
                    <option value="">All Priorities</option>
                    <option value="Normal">Normal</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
                </select>
            </div>
        </div>
    </div>
    <div class="section-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tasksListTable">
                <thead class="table-light">
                    <tr>
                        <th>Task Name</th>
                        <th>Leader</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Due Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mainTasks as $task)
                        @php
                            $totalActivities = $task->activities->count();
                            $done = $task->activities->where('status', 'Completed')->count();
                            $progress = $totalActivities > 0 ? round(($done / $totalActivities) * 100) : ($task->status === 'completed' ? 100 : 0);
                            $dueDate = $task->end_date ? \Illuminate\Support\Carbon::parse($task->end_date) : null;
                            $isOverdue = $dueDate && $dueDate->isPast() && $task->status !== 'completed';
                        @endphp
                        <tr data-status="{{ $task->status }}" data-priority="{{ $task->priority ?? 'Normal' }}" data-task-name="{{ strtolower($task->name) }}">
                            <td>
                                <div class="fw-semibold">{{ $task->name }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($task->description, 60) }}</small>
                            </td>
                            <td>
                                <span class="avatar-chip">{{ $task->teamLeader->name ?? 'Unassigned' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $task->category ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @php
                                    $priorityClass = $task->priority === 'Critical' ? 'danger' : ($task->priority === 'High' ? 'warning' : 'info');
                                @endphp
                                <span class="badge bg-{{ $priorityClass }}">{{ $task->priority ?? 'Normal' }}</span>
                            </td>
                            <td>
                                <span class="pill pill-secondary text-capitalize">{{ str_replace('_',' ', $task->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width: 80px; height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $progress }}%</small>
                                </div>
                            </td>
                            <td>
                                @if($dueDate)
                                    <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                                        {{ $dueDate->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewTaskDetails({{ $task->id }})">
                                        <i class="bx bx-show"></i> View
                                    </button>
                                    @if($isManager)
                                        <button class="btn btn-outline-secondary" onclick="editTask({{ $task->id }})">
                                            <i class="bx bx-edit"></i> Edit
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Activities Table Section -->
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


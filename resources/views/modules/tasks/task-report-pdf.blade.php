<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Task Progress Report - {{ $task->name }}</title>
    <style>
        @page { margin: 20px 30px 60px 30px; size: A4 portrait; }
        body { font-family: "Helvetica", Arial, sans-serif; font-size: 9pt; color: #333; line-height: 1.4; }
        h1, h2, h3 { color: #500000; margin: 0 0 10px 0; font-weight: bold; }
        h1 { font-size: 20pt; border-bottom: 2px solid #940000; padding-bottom: 8px; margin-bottom: 20px; text-align: center; }
        h2 { font-size: 14pt; background-color: #fceeee; padding: 8px; margin-top: 15px; border-left: 4px solid #940000; }
        h3 { font-size: 11pt; margin-top: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 8pt; }
        th, td { padding: 6px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f9f9f9; font-weight: bold; color: #500000; }
        .stats-grid { display: table; width: 100%; margin-bottom: 15px; }
        .stat-box { display: table-cell; width: 20%; padding: 10px; border: 1px solid #ddd; text-align: center; background-color: #f9f9f9; }
        .stat-box h4 { margin: 0 0 5px 0; font-size: 10pt; color: #6c757d; }
        .stat-box .value { font-size: 18pt; font-weight: bold; color: #500000; }
        .priority-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 8pt; font-weight: bold; }
        .priority-low { background-color: #0dcaf0; color: #000; }
        .priority-normal { background-color: #6c757d; color: #fff; }
        .priority-high { background-color: #ffc107; color: #000; }
        .priority-critical { background-color: #dc3545; color: #fff; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 8pt; font-weight: bold; }
        .status-planning, .status-not_started, .status-not-started { background-color: #ffc107; color: #000; }
        .status-in_progress, .status-in-progress { background-color: #0dcaf0; color: #000; }
        .status-completed { background-color: #198754; color: #fff; }
        .status-delayed { background-color: #dc3545; color: #fff; }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-approved { background-color: #198754; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
        .completion-completed { background-color: #198754; color: #fff; }
        .completion-delayed { background-color: #dc3545; color: #fff; }
        .completion-in_progress, .completion-in-progress { background-color: #0dcaf0; color: #000; }
        .progress-bar { width: 100%; background-color: #e9ecef; border-radius: 3px; height: 18px; position: relative; }
        .progress-fill { height: 100%; background-color: #940000; border-radius: 3px; text-align: center; color: white; line-height: 18px; font-size: 7pt; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-2 { margin-bottom: 8px; }
        .page-break { page-break-after: always; }
        .info-section { margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #940000; }
        .activity-section { margin-bottom: 15px; padding: 10px; border: 1px solid #dee2e6; border-radius: 3px; background-color: #f9f9f9; }
        .report-item { margin: 10px 0; padding: 8px; background-color: #ffffff; border-left: 3px solid #007bff; border-radius: 2px; }
    </style>
</head>
<body>
    @php
        $orgSettings = \App\Models\OrganizationSetting::getSettings();
        $timezone = $orgSettings->timezone ?? config('app.timezone', 'Africa/Dar_es_Salaam');
        $documentDate = now()->setTimezone($timezone)->format($orgSettings->date_format ?? 'd M Y');
        $documentRef = 'TASK-REPORT-' . $task->id . '-' . now()->setTimezone($timezone)->format('YmdHis');
    @endphp
    
    @include('components.pdf-header', [
        'documentTitle' => 'TASK PROGRESS REPORT',
        'documentRef' => $documentRef,
        'documentDate' => $documentDate
    ])

    <main>
        <h1>Task Progress Report</h1>
        
        <!-- Task Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-box">
                <h4>Overall Progress</h4>
                <div class="value" style="color: #940000;">{{ $task->progress_percentage ?? 0 }}%</div>
            </div>
            <div class="stat-box">
                <h4>Total Activities</h4>
                <div class="value">{{ $task->activities->count() }}</div>
            </div>
            <div class="stat-box">
                <h4>Completed</h4>
                <div class="value" style="color: #198754;">{{ $task->activities->where('status', 'Completed')->count() }}</div>
            </div>
            <div class="stat-box">
                <h4>In Progress</h4>
                <div class="value" style="color: #0dcaf0;">{{ $task->activities->where('status', 'In Progress')->count() }}</div>
            </div>
            <div class="stat-box">
                <h4>Total Reports</h4>
                <div class="value">{{ $task->activities->sum(function($activity) { return $activity->reports->count(); }) }}</div>
            </div>
        </div>

        <!-- Task Information -->
        <h2>Task Information</h2>
        <div class="info-section">
            <table>
                <tr>
                    <th style="width: 30%;">Task Name</th>
                    <td><strong>{{ $task->name }}</strong></td>
                </tr>
                @if($task->description)
                <tr>
                    <th>Description</th>
                    <td>{{ $task->description }}</td>
                </tr>
                @endif
                <tr>
                    <th>Team Leader</th>
                    <td>{{ $task->teamLeader->name ?? 'Unassigned' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="status-badge status-{{ str_replace(' ', '_', strtolower($task->status)) }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Priority</th>
                    <td>
                        <span class="priority-badge priority-{{ strtolower($task->priority ?? 'normal') }}">
                            {{ $task->priority ?? 'Normal' }}
                        </span>
                    </td>
                </tr>
                @if($task->category)
                <tr>
                    <th>Category</th>
                    <td>{{ $task->category }}</td>
                </tr>
                @endif
                <tr>
                    <th>Start Date</th>
                    <td>{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d M Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td>{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d M Y') : 'N/A' }}</td>
                </tr>
                @if($task->timeframe)
                <tr>
                    <th>Timeframe</th>
                    <td>{{ $task->timeframe }}</td>
                </tr>
                @endif
                <tr>
                    <th>Overall Progress</th>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $task->progress_percentage ?? 0 }}%;">{{ $task->progress_percentage ?? 0 }}%</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Activities and Reports -->
        <h2>Activities & Progress Reports</h2>
        
        @if($task->activities->count() > 0)
            @foreach($task->activities as $activity)
                <div class="activity-section">
                    <h3 style="margin-top: 0; margin-bottom: 10px; color: #940000; font-size: 12pt;">{{ $activity->name }}</h3>
                    
                    <table style="margin-bottom: 10px;">
                        <tr>
                            <th style="width: 30%;">Status</th>
                            <td>
                                <span class="status-badge status-{{ str_replace(' ', '_', strtolower($activity->status)) }}">
                                    {{ $activity->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Assigned To</th>
                            <td>
                                @if($activity->assignedUsers->count() > 0)
                                    {{ $activity->assignedUsers->pluck('name')->join(', ') }}
                                @else
                                    Unassigned
                                @endif
                            </td>
                        </tr>
                        @if($activity->priority)
                        <tr>
                            <th>Priority</th>
                            <td>
                                <span class="priority-badge priority-{{ strtolower($activity->priority) }}">
                                    {{ $activity->priority }}
                                </span>
                            </td>
                        </tr>
                        @endif
                        @if($activity->timeframe)
                        <tr>
                            <th>Timeframe</th>
                            <td>{{ $activity->timeframe }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Due Date</th>
                            <td>{{ $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </table>

                    @if($activity->reports->count() > 0)
                        <h4 style="font-size: 10pt; margin-top: 15px; margin-bottom: 8px; color: #666;">
                            Progress Reports ({{ $activity->reports->count() }})
                        </h4>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 12%;">Report Date</th>
                                    <th style="width: 15%;">Reporter</th>
                                    <th style="width: 12%;">Completion</th>
                                    <th style="width: 12%;">Approval</th>
                                    <th style="width: 15%;">Approved By</th>
                                    <th style="width: 8%;" class="text-center">Attachments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activity->reports as $report)
                                <tr>
                                    <td>{{ $report->report_date->format('d M Y') }}</td>
                                    <td>{{ $report->user->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="status-badge completion-{{ str_replace(' ', '_', strtolower($report->completion_status)) }}">
                                            {{ $report->completion_status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-{{ strtolower($report->status) }}">
                                            {{ $report->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($report->approver)
                                            {{ $report->approver->name }}<br>
                                            <small style="font-size: 7pt;">{{ $report->approved_at ? $report->approved_at->format('d M Y H:i') : '' }}</small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $report->attachments->count() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Detailed Report Information -->
                        @foreach($activity->reports as $report)
                            <div class="report-item">
                                <table style="margin-bottom: 5px;">
                                    <tr>
                                        <th style="width: 20%;">Reporter</th>
                                        <td>{{ $report->user->name ?? 'N/A' }} @if($report->user->email)({{ $report->user->email }})@endif</td>
                                    </tr>
                                    <tr>
                                        <th>Report Date</th>
                                        <td>{{ $report->report_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Work Description</th>
                                        <td>{{ $report->work_description }}</td>
                                    </tr>
                                    @if($report->next_activities)
                                    <tr>
                                        <th>Next Activities</th>
                                        <td>{{ $report->next_activities }}</td>
                                    </tr>
                                    @endif
                                    @if($report->reason_if_delayed)
                                    <tr>
                                        <th>Reason for Delay</th>
                                        <td style="color: #dc3545;">{{ $report->reason_if_delayed }}</td>
                                    </tr>
                                    @endif
                                    @if($report->attachments->count() > 0)
                                    <tr>
                                        <th>Attachments</th>
                                        <td>
                                            @foreach($report->attachments as $attachment)
                                                • {{ $attachment->file_name }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                    @if($report->approver_comments)
                                    <tr>
                                        <th>Approver Comments</th>
                                        <td>{{ $report->approver_comments }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        @endforeach
                    @else
                        <p style="color: #666; font-style: italic; margin: 10px 0;">No reports submitted yet.</p>
                    @endif
                </div>
            @endforeach
        @else
            <p style="color: #666; font-style: italic;">No activities defined for this task.</p>
        @endif

        <!-- Summary Statistics -->
        <div class="page-break"></div>
        <h2>Summary Statistics</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th class="text-right">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Activities</td>
                    <td class="text-right">{{ $task->activities->count() }}</td>
                </tr>
                <tr>
                    <td>Completed Activities</td>
                    <td class="text-right">{{ $task->activities->where('status', 'Completed')->count() }}</td>
                </tr>
                <tr>
                    <td>In Progress Activities</td>
                    <td class="text-right">{{ $task->activities->where('status', 'In Progress')->count() }}</td>
                </tr>
                <tr>
                    <td>Not Started Activities</td>
                    <td class="text-right">{{ $task->activities->where('status', 'Not Started')->count() }}</td>
                </tr>
                <tr>
                    <td>Delayed Activities</td>
                    <td class="text-right">{{ $task->activities->where('status', 'Delayed')->count() }}</td>
                </tr>
                <tr>
                    <td>Total Reports</td>
                    <td class="text-right">{{ $task->activities->sum(function($activity) { return $activity->reports->count(); }) }}</td>
                </tr>
                <tr>
                    <td>Approved Reports</td>
                    <td class="text-right">{{ $task->activities->sum(function($activity) { return $activity->reports->where('status', 'Approved')->count(); }) }}</td>
                </tr>
                <tr>
                    <td>Pending Reports</td>
                    <td class="text-right">{{ $task->activities->sum(function($activity) { return $activity->reports->where('status', 'Pending')->count(); }) }}</td>
                </tr>
                <tr>
                    <td>Rejected Reports</td>
                    <td class="text-right">{{ $task->activities->sum(function($activity) { return $activity->reports->where('status', 'Rejected')->count(); }) }}</td>
                </tr>
                <tr>
                    <td><strong>Overall Progress</strong></td>
                    <td class="text-right"><strong>{{ $task->progress_percentage ?? 0 }}%</strong></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 8pt; color: #6c757d; text-align: center;">
            <p>Report generated on: {{ $generated_at }}</p>
            <p>Generated by: {{ $generated_by }}</p>
        </div>
    </main>

    @include('components.pdf-footer')
</body>
</html>

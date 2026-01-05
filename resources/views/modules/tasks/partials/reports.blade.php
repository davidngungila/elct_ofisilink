<!-- Reports View Section - Independent Component -->
<div class="section-card">
    <div class="section-card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="section-card-title">
                    <i class="bx bx-file-blank me-2"></i>
                    All Reports
                </h3>
                <p class="section-card-subtitle">Complete history of all progress reports and submissions</p>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="filterReportStatus" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>
    <div class="section-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="reportsTable">
                <thead class="table-light">
                    <tr>
                        <th>Activity</th>
                        <th>Task</th>
                        <th>Reporter</th>
                        <th>Status</th>
                        <th>Completion Status</th>
                        <th>Submitted</th>
                        <th>Attachment</th>
                        @if($isManager)
                        <th class="text-end">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $recentReports = collect($pendingReports ?? []);
                        // Get more reports if we have any pending reports
                        if ($recentReports->isNotEmpty() && $recentReports->first()->activity) {
                            $mainTask = $recentReports->first()->activity->mainTask ?? null;
                            if ($mainTask) {
                                foreach($mainTask->activities ?? [] as $activity) {
                                    foreach($activity->reports ?? [] as $report) {
                                        if (!$recentReports->contains('id', $report->id)) {
                                            $recentReports->push($report);
                                        }
                                    }
                                }
                            }
                        }
                        $recentReports = $recentReports->sortByDesc('created_at')->take(50);
                    @endphp
                    @forelse($recentReports as $report)
                        @php
                            $taskName = $report->activity->mainTask->name ?? 'Task';
                            $activityName = $report->activity->name ?? 'Activity';
                        @endphp
                        <tr data-status="{{ $report->status }}">
                            <td class="fw-semibold">{{ $activityName }}</td>
                            <td>{{ $taskName }}</td>
                            <td>{{ $report->user->name ?? '—' }}</td>
                            <td>
                                @if($report->status === 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($report->status === 'Rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <span class="pill pill-info">{{ $report->completion_status ?? 'N/A' }}</span>
                            </td>
                            <td>{{ \Illuminate\Support\Carbon::parse($report->created_at)->format('M d, Y h:i A') }}</td>
                            <td>
                                @if($report->attachment_path)
                                    <a href="{{ Storage::url($report->attachment_path) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="bx bx-paperclip"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @if($isManager)
                            <td class="text-end">
                                @if($report->status === 'Pending')
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-success" onclick="approveReport({{ $report->id }})">
                                        <i class="bx bx-check"></i> Approve
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="rejectReport({{ $report->id }})">
                                        <i class="bx bx-x"></i> Reject
                                    </button>
                                </div>
                                @else
                                    <span class="text-muted small">Processed</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isManager ? '8' : '7' }}" class="text-center text-muted py-4">
                                No reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


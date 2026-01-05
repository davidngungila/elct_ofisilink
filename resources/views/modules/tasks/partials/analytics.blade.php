<!-- Analytics View Section - Independent Component -->
<div class="row g-4">
    <!-- Analytics Overview Cards -->
    <div class="col-md-6 col-lg-3">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="bx bx-trending-up"></i>
            </div>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">Completion Rate</h6>
                    @php
                        $total = $dashboardStats['total'] ?? 1;
                        $completed = $dashboardStats['completed'] ?? 0;
                        $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
                    @endphp
                    <h3 class="mb-0 text-primary">{{ $rate }}%</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="bx bx-time"></i>
            </div>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">Avg. Duration</h6>
                    <h3 class="mb-0 text-warning">—</h3>
                    <small class="text-muted">Calculating...</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="bx bx-check-double"></i>
            </div>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">On Time</h6>
                    @php
                        $onTime = ($dashboardStats['total'] ?? 0) - ($dashboardStats['overdue'] ?? 0) - ($dashboardStats['completed'] ?? 0);
                    @endphp
                    <h3 class="mb-0 text-success">{{ max(0, $onTime) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="bx bx-user"></i>
            </div>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">Active Team Members</h6>
                    @php
                        $uniqueUsers = $mainTasks->pluck('team_leader_id')
                            ->merge($mainTasks->flatMap->activities->flatMap->assignedUsers->pluck('id'))
                            ->unique()
                            ->count();
                    @endphp
                    <h3 class="mb-0 text-info">{{ $uniqueUsers }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="section-card">
            <div class="section-card-header">
                <h3 class="section-card-title">
                    <i class="bx bx-pie-chart-alt-2 me-2"></i>
                    Tasks by Status
                </h3>
            </div>
            <div class="section-card-body">
                <canvas id="statusChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="section-card">
            <div class="section-card-header">
                <h3 class="section-card-title">
                    <i class="bx bx-bar-chart-alt-2 me-2"></i>
                    Tasks by Priority
                </h3>
            </div>
            <div class="section-card-body">
                <canvas id="priorityChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function initializeAnalytics() {
    // Status Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx && typeof Chart !== 'undefined') {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending', 'Overdue'],
                datasets: [{
                    data: [
                        {{ $dashboardStats['completed'] ?? 0 }},
                        {{ $dashboardStats['in_progress'] ?? 0 }},
                        {{ ($dashboardStats['total'] ?? 0) - ($dashboardStats['completed'] ?? 0) - ($dashboardStats['in_progress'] ?? 0) - ($dashboardStats['overdue'] ?? 0) }},
                        {{ $dashboardStats['overdue'] ?? 0 }}
                    ],
                    backgroundColor: ['#16a34a', '#d97706', '#2563eb', '#dc2626']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    // Priority Chart
    const priorityCtx = document.getElementById('priorityChart');
    if (priorityCtx && typeof Chart !== 'undefined') {
        @php
            $normalCount = $mainTasks->where('priority', 'Normal')->count();
            $highCount = $mainTasks->where('priority', 'High')->count();
            $criticalCount = $mainTasks->where('priority', 'Critical')->count();
        @endphp
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['Normal', 'High', 'Critical'],
                datasets: [{
                    label: 'Tasks',
                    data: [{{ $normalCount }}, {{ $highCount }}, {{ $criticalCount }}],
                    backgroundColor: ['#2563eb', '#d97706', '#dc2626']
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
}

// Initialize when tab is shown
$('#analytics-tab').on('shown.bs.tab', function() {
    setTimeout(initializeAnalytics, 100);
});
</script>


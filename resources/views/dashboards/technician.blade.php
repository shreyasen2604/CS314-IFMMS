@extends('layouts.app')

@section('title', 'Technician Dashboard - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">Technician Dashboard</h1>
    <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}</p>
</div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        @php
            $assignedIncidents = \App\Models\Incident::where('assigned_to_user_id', auth()->id())->where('status', '!=', 'Closed')->count();

            try {
                $assignedWork = \App\Models\MaintenanceSchedule::where('technician_id', auth()->id())->where('status', '!=', 'completed')->count();
                $availableWork = \App\Models\MaintenanceSchedule::whereNull('technician_id')->where('status', 'scheduled')->count();
                $todayWork = \App\Models\MaintenanceSchedule::where('technician_id', auth()->id())->whereDate('scheduled_date', today())->where('status', '!=', 'completed')->count();
            } catch (\Exception $e) {
                $assignedWork = 0;
                $availableWork = 0;
                $todayWork = 0;
            }
        @endphp

        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $assignedIncidents }}</h4>
                            <p class="mb-0">Assigned Incidents</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $assignedWork }}</h4>
                            <p class="mb-0">Maintenance Tasks</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $availableWork }}</h4>
                            <p class="mb-0">Available Tasks</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $todayWork }}</h4>
                            <p class="mb-0">Due Today</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Area -->
        <div class="col-xl-8">
            <!-- Incident Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Incident Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('technician.incidents.index') }}" class="card text-decoration-none h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-list-alt fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Incident Queue</h6>
                                    <p class="card-text small text-muted">Unassigned + assigned to me</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('technician.incidents.export') }}" class="card text-decoration-none h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-download fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">Export Reports</h6>
                                    <p class="card-text small text-muted">Generate incident reports</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Maintenance Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.technician.work-queue') }}" class="card text-decoration-none h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-tasks fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Work Queue</h6>
                                    <p class="card-text small text-muted">My assigned maintenance tasks</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.schedule') }}" class="card text-decoration-none h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                                    <h6 class="card-title">Scheduler</h6>
                                    <p class="card-text small text-muted">Schedule maintenance tasks</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.records') }}" class="card text-decoration-none h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x text-secondary mb-3"></i>
                                    <h6 class="card-title">Records</h6>
                                    <p class="card-text small text-muted">Maintenance history</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics & Tools -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Analytics & Tools</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('maintenance.analytics') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Fleet Analytics</h6>
                                    <p class="card-text small text-muted">Performance and health data</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('maintenance.alerts') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-bell fa-3x text-danger mb-3"></i>
                                    <h6 class="card-title">Alert Management</h6>
                                    <p class="card-text small text-muted">Acknowledge and resolve alerts</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Charts (Placeholder) -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Work Performance</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-placeholder" style="height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-chart-line fa-3x mb-2"></i>
                                    <p>Completion Rate Trend</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-placeholder" style="height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-chart-pie fa-3x mb-2"></i>
                                    <p>Work Type Distribution</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Today's Schedule -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Today's Schedule</h5>
                </div>
                <div class="card-body">
                    @php
                        try {
                            $todaySchedule = \App\Models\MaintenanceSchedule::with('vehicle')
                                ->where('technician_id', auth()->id())
                                ->whereDate('scheduled_date', today())
                                ->where('status', '!=', 'completed')
                                ->orderBy('scheduled_date')
                                ->get();
                        } catch (\Exception $e) {
                            $todaySchedule = collect();
                        }
                    @endphp
                    @forelse($todaySchedule as $schedule)
                        <div class="schedule-item d-flex justify-content-between align-items-start mb-3 p-2 border rounded">
                            <div>
                                <p class="mb-1"><strong>{{ $schedule->vehicle->vehicle_number }}</strong></p>
                                <small class="text-muted">
                                    {{ ucfirst(str_replace('_', ' ', $schedule->maintenance_type)) }} -
                                    {{ $schedule->scheduled_date->format('H:i') }}
                                </small>
                            </div>
                            <span class="badge bg-{{ $schedule->priority === 'critical' ? 'danger' : ($schedule->priority === 'high' ? 'warning' : 'info') }}">
                                {{ ucfirst($schedule->priority) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted">No scheduled work for today</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @php
                        try {
                            $recentWork = \App\Models\MaintenanceRecord::with('vehicle')
                                ->where('technician_id', auth()->id())
                                ->latest()
                                ->take(5)
                                ->get();
                        } catch (\Exception $e) {
                            $recentWork = collect();
                        }
                    @endphp
                    @forelse($recentWork as $work)
                        <div class="activity-item d-flex mb-3">
                            <div class="activity-icon me-3">
                                <i class="fas fa-wrench text-success"></i>
                            </div>
                            <div class="activity-content">
                                <p class="mb-1"><strong>{{ $work->vehicle->vehicle_number }}</strong></p>
                                <small class="text-muted">
                                    {{ ucfirst(str_replace('_', ' ', $work->maintenance_type)) }} •
                                    {{ $work->service_date->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No recent activity</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('maintenance.technician.work-queue') }}" class="btn btn-primary">
                            <i class="fas fa-tasks me-2"></i>View Work Queue
                        </a>
                        <a href="{{ route('maintenance.schedule') }}" class="btn btn-outline-info">
                            <i class="fas fa-calendar-plus me-2"></i>Schedule Maintenance
                        </a>
                        <a href="{{ route('technician.incidents.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-list me-2"></i>Check Incidents
                        </a>
                        <button class="btn btn-outline-success" onclick="completeQuickTask()">
                            <i class="fas fa-check me-2"></i>Quick Complete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Work Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Work Statistics</h5>
                </div>
                <div class="card-body">
                    @php
                        try {
                            $completedThisWeek = \App\Models\MaintenanceRecord::where('technician_id', auth()->id())
                                ->where('status', 'completed')
                                ->whereBetween('completion_date', [now()->startOfWeek(), now()->endOfWeek()])
                                ->count();
                            $completedThisMonth = \App\Models\MaintenanceRecord::where('technician_id', auth()->id())
                                ->where('status', 'completed')
                                ->whereMonth('completion_date', now()->month)
                                ->count();
                        } catch (\Exception $e) {
                            $completedThisWeek = 0;
                            $completedThisMonth = 0;
                        }
                        $avgCompletionTime = 2.5; // This would be calculated from actual data
                    @endphp
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span>This Week</span>
                        <span class="badge bg-success">{{ $completedThisWeek }} completed</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span>This Month</span>
                        <span class="badge bg-info">{{ $completedThisMonth }} completed</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span>Avg. Time</span>
                        <span class="badge bg-warning">{{ $avgCompletionTime }}h</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span>Efficiency</span>
                        <span class="badge bg-primary">95%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function completeQuickTask() {
    // This would open a modal for quick task completion
    alert('Quick task completion feature coming soon!');
}

// Auto-refresh work queue every 30 seconds
setInterval(function() {
    // This would typically make AJAX calls to update the work queue
    console.log('Work queue refresh would happen here');
}, 30000);

// Notification for overdue tasks
document.addEventListener('DOMContentLoaded', function() {
    @if($todayWork > 0)
        // Show notification for tasks due today
        console.log('You have {{ $todayWork }} task(s) due today');
    @endif
});
</script>
@endsection

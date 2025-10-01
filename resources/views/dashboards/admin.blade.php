@extends('layouts.app')

@section('title', 'Admin Dashboard - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admin Dashboard</h1>
    <p class="page-subtitle">Complete fleet management and system administration</p>
</div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="totalUsers">{{ \App\Models\User::count() }}</h4>
                            <p class="mb-0">Total Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
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
                            @php
                                try {
                                    $totalVehicles = \App\Models\Vehicle::count();
                                } catch (\Exception $e) {
                                    $totalVehicles = 0;
                                }
                            @endphp
                            <h4 class="mb-0" id="totalVehicles">{{ $totalVehicles }}</h4>
                            <p class="mb-0">Fleet Vehicles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-truck fa-2x"></i>
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
                            <h4 class="mb-0" id="activeIncidents">{{ \App\Models\Incident::where('status', '!=', 'Closed')->count() }}</h4>
                            <p class="mb-0">Active Incidents</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            @php
                                try {
                                    $maintenanceAlerts = \App\Models\MaintenanceAlert::where('status', 'active')->count();
                                } catch (\Exception $e) {
                                    $maintenanceAlerts = 0;
                                }
                            @endphp
                            <h4 class="mb-0" id="maintenanceAlerts">{{ $maintenanceAlerts }}</h4>
                            <p class="mb-0">Maintenance Alerts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Area -->
        <div class="col-xl-8">
            <!-- System Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">System Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('admin.users.index') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">User Management</h6>
                                    <p class="card-text small text-muted">Add, edit, and manage system users</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.incidents.index') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Incident Management</h6>
                                    <p class="card-text small text-muted">Track and resolve service incidents</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.incidents.export') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-download fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">Export Reports</h6>
                                    <p class="card-text small text-muted">Generate system reports</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Support & Incident Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Service Support & Incident Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('support.service-requests.index') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-headset fa-3x text-info mb-3"></i>
                                    <h6 class="card-title">Service Requests</h6>
                                    <p class="card-text small text-muted">Manage support tickets</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('support.incidents.index') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                                    <h6 class="card-title">Incident Reports</h6>
                                    <p class="card-text small text-muted">Fleet incident tracking</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('support.statistics') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-3x text-purple mb-3"></i>
                                    <h6 class="card-title">Support Analytics</h6>
                                    <p class="card-text small text-muted">Service performance metrics</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Fleet Maintenance Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.dashboard') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                                    <h6 class="card-title">Maintenance Dashboard</h6>
                                    <p class="card-text small text-muted">Fleet overview and analytics</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.vehicles.index') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-car fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Vehicle Management</h6>
                                    <p class="card-text small text-muted">Add and manage fleet vehicles</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.schedule') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-alt fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">Maintenance Scheduler</h6>
                                    <p class="card-text small text-muted">Schedule maintenance tasks</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.analytics') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Predictive Analytics</h6>
                                    <p class="card-text small text-muted">Fleet health and forecasting</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.records') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x text-secondary mb-3"></i>
                                    <h6 class="card-title">Maintenance Records</h6>
                                    <p class="card-text small text-muted">Complete maintenance history</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('maintenance.alerts') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-bell fa-3x text-danger mb-3"></i>
                                    <h6 class="card-title">Alert Management</h6>
                                    <p class="card-text small text-muted">Configure and manage alerts</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics Section (Placeholder) -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">System Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-placeholder" style="height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-chart-pie fa-3x mb-2"></i>
                                    <p>User Distribution Chart</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-placeholder" style="height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-chart-line fa-3x mb-2"></i>
                                    <p>Incident Trends Chart</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        @php
                            $recentIncidents = \App\Models\Incident::with('reporter')->latest()->take(5)->get();
                        @endphp
                        @forelse($recentIncidents as $incident)
                            <div class="activity-item d-flex mb-3">
                                <div class="activity-icon me-3">
                                    <i class="fas fa-exclamation-circle text-warning"></i>
                                </div>
                                <div class="activity-content">
                                    <p class="mb-1"><strong>{{ $incident->title }}</strong></p>
                                    <small class="text-muted">
                                        by {{ $incident->reporter->name ?? 'Unknown' }} • {{ $incident->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">System Status</h5>
                </div>
                <div class="card-body">
                    <div class="status-item d-flex justify-content-between align-items-center mb-3">
                        <span>Database</span>
                        <span class="badge bg-success">Online</span>
                    </div>
                    <div class="status-item d-flex justify-content-between align-items-center mb-3">
                        <span>Maintenance Module</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="status-item d-flex justify-content-between align-items-center mb-3">
                        <span>Alert System</span>
                        <span class="badge bg-success">Running</span>
                    </div>
                    <div class="status-item d-flex justify-content-between align-items-center">
                        <span>Backup Status</span>
                        <span class="badge bg-warning">Scheduled</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Add New User
                        </a>
                        <a href="{{ route('maintenance.vehicles.create') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-2"></i>Add New Vehicle
                        </a>
                        <button class="btn btn-outline-info" onclick="generateReport()">
                            <i class="fas fa-file-alt me-2"></i>Generate Report
                        </button>
                        <button class="btn btn-outline-warning" onclick="configureAlerts()">
                            <i class="fas fa-cog me-2"></i>Configure Alerts
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport() {
    alert('Report generation feature coming soon!');
}

function configureAlerts() {
    window.location.href = '{{ route("maintenance.alerts") }}';
}

// Auto-refresh stats every 30 seconds
setInterval(function() {
    // This would typically make AJAX calls to update the stats
    console.log('Stats refresh would happen here');
}, 30000);
</script>
@endsection
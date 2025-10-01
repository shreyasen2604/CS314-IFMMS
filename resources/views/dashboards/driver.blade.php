@extends('layouts.app')

@section('title', 'Driver Dashboard - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">Driver Dashboard</h1>
    <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}</p>
</div>

    <!-- Quick Stats Row -->
    <div class="row mb-4">
        @php
            $myIncidents = \App\Models\Incident::where('reported_by_user_id', auth()->id())->count();
            $activeIncidents = \App\Models\Incident::where('reported_by_user_id', auth()->id())->where('status', '!=', 'Closed')->count();

            try {
                $myVehicle = \App\Models\Vehicle::where('driver_id', auth()->id())->first();
                $vehicleAlerts = $myVehicle ? \App\Models\MaintenanceAlert::where('vehicle_id', $myVehicle->id)->where('status', 'active')->count() : 0;
            } catch (\Exception $e) {
                $myVehicle = null;
                $vehicleAlerts = 0;
            }
        @endphp

        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $myIncidents }}</h4>
                            <p class="mb-0">My Incidents</p>
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
                            <h4 class="mb-0">{{ $activeIncidents }}</h4>
                            <p class="mb-0">Active Issues</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4 class="mb-0">{{ $myVehicle ? number_format($myVehicle->health_score, 1) . '%' : 'N/A' }}</h4>
                            <p class="mb-0">Vehicle Health</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-heart fa-2x"></i>
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
                            <h4 class="mb-0">{{ $vehicleAlerts }}</h4>
                            <p class="mb-0">Vehicle Alerts</p>
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
            <!-- Incident Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Incident Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('driver.incidents.create') }}" class="card text-decoration-none h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Report New Incident</h6>
                                    <p class="card-text small text-muted">Create a service/support ticket</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('driver.incidents.index') }}" class="card text-decoration-none h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-list fa-3x text-info mb-3"></i>
                                    <h6 class="card-title">My Incidents</h6>
                                    <p class="card-text small text-muted">View status and updates</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle & Maintenance -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Vehicle & Maintenance</h5>
                </div>
                <div class="card-body">
                    @if($myVehicle)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('maintenance.driver.vehicle') }}" class="card text-decoration-none h-100 border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-truck fa-3x text-success mb-3"></i>
                                        <h6 class="card-title">My Vehicle</h6>
                                        <p class="card-text small text-muted">{{ $myVehicle->vehicle_number }} - {{ $myVehicle->make }} {{ $myVehicle->model }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('maintenance.records', ['vehicle_id' => $myVehicle->id]) }}" class="card text-decoration-none h-100 border-warning">
                                    <div class="card-body text-center">
                                        <i class="fas fa-history fa-3x text-warning mb-3"></i>
                                        <h6 class="card-title">Maintenance History</h6>
                                        <p class="card-text small text-muted">View service records</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No vehicle assigned. Please contact your administrator.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Analytics & Reports -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Analytics & Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('maintenance.analytics') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-secondary mb-3"></i>
                                    <h6 class="card-title">Fleet Analytics</h6>
                                    <p class="card-text small text-muted">View fleet performance data</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('maintenance.alerts') }}" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-bell fa-3x text-danger mb-3"></i>
                                    <h6 class="card-title">System Alerts</h6>
                                    <p class="card-text small text-muted">View maintenance alerts</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Charts (Placeholder) -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Performance Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-placeholder" style="height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-chart-line fa-3x mb-2"></i>
                                    <p>Vehicle Health Trend</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-placeholder" style="height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-chart-pie fa-3x mb-2"></i>
                                    <p>Incident Categories</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Vehicle Status -->
            @if($myVehicle)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Vehicle Status</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-{{ $myVehicle->health_score >= 80 ? 'success' : ($myVehicle->health_score >= 60 ? 'info' : ($myVehicle->health_score >= 40 ? 'warning' : 'danger')) }}">
                            {{ number_format($myVehicle->health_score, 1) }}%
                        </h3>
                        <p class="text-muted">Health Score</p>
                    </div>
                    <div class="vehicle-info">
                        <p><strong>Vehicle:</strong> {{ $myVehicle->vehicle_number }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ $myVehicle->status === 'active' ? 'success' : ($myVehicle->status === 'maintenance' ? 'warning' : 'danger') }}">
                                {{ ucfirst($myVehicle->status) }}
                            </span>
                        </p>
                        <p><strong>Mileage:</strong> {{ number_format($myVehicle->mileage) }} miles</p>
                        @if($myVehicle->next_maintenance_due)
                            <p><strong>Next Service:</strong>
                                <span class="text-{{ $myVehicle->next_maintenance_due->isPast() ? 'danger' : ($myVehicle->next_maintenance_due->diffInDays() <= 7 ? 'warning' : 'success') }}">
                                    {{ $myVehicle->next_maintenance_due->format('M d, Y') }}
                                </span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Incidents -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Recent Incidents</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentIncidents = \App\Models\Incident::where('reported_by_user_id', auth()->id())->latest()->take(5)->get();
                    @endphp
                    @forelse($recentIncidents as $incident)
                        <div class="incident-item d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="mb-1"><strong>{{ Str::limit($incident->title, 30) }}</strong></p>
                                <small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                            </div>
                            <span class="badge bg-{{ $incident->status === 'Closed' ? 'success' : ($incident->status === 'In Progress' ? 'warning' : 'secondary') }}">
                                {{ $incident->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted">No recent incidents</p>
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
                        <a href="{{ route('driver.incidents.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Report Issue
                        </a>
                        @if($myVehicle)
                            <button class="btn btn-outline-warning" onclick="requestMaintenance()">
                                <i class="fas fa-wrench me-2"></i>Request Maintenance
                            </button>
                        @endif
                        <a href="{{ route('driver.incidents.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>View My Incidents
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Notifications</h5>
                </div>
                <div class="card-body">
                    @if($vehicleAlerts > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You have {{ $vehicleAlerts }} active vehicle alert{{ $vehicleAlerts > 1 ? 's' : '' }}.
                            <a href="{{ route('maintenance.alerts') }}" class="alert-link">View Details</a>
                        </div>
                    @endif

                    @if($myVehicle && $myVehicle->next_maintenance_due && $myVehicle->next_maintenance_due->diffInDays() <= 7)
                        <div class="alert alert-info">
                            <i class="fas fa-calendar me-2"></i>
                            Vehicle maintenance due {{ $myVehicle->next_maintenance_due->diffForHumans() }}.
                        </div>
                    @endif

                    @if($vehicleAlerts == 0 && (!$myVehicle || !$myVehicle->next_maintenance_due || $myVehicle->next_maintenance_due->diffInDays() > 7))
                        <p class="text-muted">No new notifications</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function requestMaintenance() {
    @if($myVehicle)
        window.location.href = '{{ route("maintenance.driver.vehicle") }}';
    @else
        alert('No vehicle assigned. Please contact your administrator.');
    @endif
}

// Auto-refresh notifications every 60 seconds
setInterval(function() {
    // This would typically make AJAX calls to update notifications
    console.log('Notifications refresh would happen here');
}, 60000);
</script>
@endsection

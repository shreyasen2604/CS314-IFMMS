@extends('layouts.app')

@section('title', 'Maintenance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Maintenance Dashboard</h1>
                <p class="page-subtitle">Fleet overview and maintenance insights</p>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $fleetStats['total_vehicles'] }}</h4>
                            <p class="mb-0">Total Vehicles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $fleetStats['active_vehicles'] }}</h4>
                            <p class="mb-0">Active Vehicles</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $fleetStats['overdue_maintenance'] }}</h4>
                            <p class="mb-0">Overdue Maintenance</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $fleetStats['critical_alerts'] }}</h4>
                            <p class="mb-0">Critical Alerts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ $fleetStats['due_soon'] }}</h5>
                            <small class="text-muted">Due This Week</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ $fleetStats['scheduled_today'] }}</h5>
                            <small class="text-muted">Scheduled Today</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ number_format($fleetStats['avg_health_score'], 1) }}%</h5>
                            <small class="text-muted">Avg Health Score</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-heart text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">${{ number_format($fleetStats['monthly_cost'], 2) }}</h5>
                            <small class="text-muted">Monthly Cost</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Fleet Health Distribution -->
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Fleet Health Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="healthChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Alerts -->
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title">Recent Alerts</h5>
                    <a href="{{ route('maintenance.alerts') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($criticalAlerts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($criticalAlerts as $alert)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $alert->title }}</div>
                                        <small class="text-muted">{{ $alert->vehicle->vehicle_number }} - {{ $alert->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : 'info') }} rounded-pill">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No recent alerts</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Maintenance -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title">Upcoming Maintenance</h5>
                    <a href="{{ route('maintenance.schedule') }}" class="btn btn-sm btn-outline-primary">View Schedule</a>
                </div>
                <div class="card-body">
                    @if($upcomingMaintenance->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Scheduled Date</th>
                                        <th>Technician</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingMaintenance as $schedule)
                                        <tr>
                                            <td>
                                                <strong>{{ $schedule->vehicle->vehicle_number }}</strong><br>
                                                <small class="text-muted">{{ $schedule->vehicle->make }} {{ $schedule->vehicle->model }}</small>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $schedule->maintenance_type)) }}</td>
                                            <td>{{ $schedule->scheduled_date->format('M d, Y H:i') }}</td>
                                            <td>{{ $schedule->technician->name ?? 'Unassigned' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->priority === 'critical' ? 'danger' : ($schedule->priority === 'high' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($schedule->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($schedule->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No upcoming maintenance scheduled</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Health Distribution Chart
    const ctx = document.getElementById('healthChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Excellent (80-100%)', 'Good (60-79%)', 'Fair (40-59%)', 'Poor (<40%)'],
            datasets: [{
                data: [
                    {{ $healthDistribution['excellent'] }},
                    {{ $healthDistribution['good'] }},
                    {{ $healthDistribution['fair'] }},
                    {{ $healthDistribution['poor'] }}
                ],
                backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
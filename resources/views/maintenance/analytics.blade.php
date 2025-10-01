@extends('layouts.app')

@section('title', 'Predictive Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Predictive Analytics</h1>
                <p class="page-subtitle">Vehicle health scoring, cost forecasting, and performance trends</p>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $vehicleHealthData->avg('health_score') ? number_format($vehicleHealthData->avg('health_score'), 1) : 0 }}%</h4>
                            <p class="mb-0">Fleet Avg Health</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-heart fa-2x"></i>
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
                            <h4 class="mb-0">{{ $breakdownRisk->count() }}</h4>
                            <p class="mb-0">High Risk Vehicles</p>
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
                            <h4 class="mb-0">${{ number_format($monthlyCosts->sum('total_cost'), 0) }}</h4>
                            <p class="mb-0">YTD Maintenance Cost</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                            <h4 class="mb-0">{{ $performanceTrends->avg('service_count') ? number_format($performanceTrends->avg('service_count'), 1) : 0 }}</h4>
                            <p class="mb-0">Avg Services/Day</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Vehicle Health Scoring -->
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Vehicle Health Scoring</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="healthTable">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Make/Model</th>
                                    <th>Health Score</th>
                                    <th>Status</th>
                                    <th>Risk Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicleHealthData as $vehicle)
                                    <tr>
                                        <td><strong>{{ $vehicle->vehicle_number }}</strong></td>
                                        <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $vehicle->health_score >= 80 ? 'success' : ($vehicle->health_score >= 60 ? 'info' : ($vehicle->health_score >= 40 ? 'warning' : 'danger')) }}" 
                                                         style="width: {{ $vehicle->health_score }}%"></div>
                                                </div>
                                                <span class="text-{{ $vehicle->health_score >= 80 ? 'success' : ($vehicle->health_score >= 60 ? 'info' : ($vehicle->health_score >= 40 ? 'warning' : 'danger')) }}">
                                                    {{ $vehicle->health_score }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $vehicle->health_score >= 80 ? 'success' : ($vehicle->health_score >= 60 ? 'info' : ($vehicle->health_score >= 40 ? 'warning' : 'danger')) }}">
                                                {{ $vehicle->health_score >= 80 ? 'Excellent' : ($vehicle->health_score >= 60 ? 'Good' : ($vehicle->health_score >= 40 ? 'Fair' : 'Poor')) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($vehicle->health_score < 40)
                                                <span class="badge bg-danger">Critical</span>
                                            @elseif($vehicle->health_score < 60)
                                                <span class="badge bg-warning">High</span>
                                            @elseif($vehicle->health_score < 80)
                                                <span class="badge bg-info">Medium</span>
                                            @else
                                                <span class="badge bg-success">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('maintenance.vehicle.profile', $vehicle->id) }}" class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Type Breakdown -->
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Maintenance Type Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="maintenanceTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Forecasting -->
    <div class="row mt-4">
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Maintenance Cost Forecasting</h5>
                </div>
                <div class="card-body">
                    <canvas id="costForecastChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Breakdown Risk Assessment -->
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Breakdown Risk Assessment</h5>
                </div>
                <div class="card-body">
                    @if($breakdownRisk->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($breakdownRisk->take(5) as $vehicle)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $vehicle->vehicle_number }}</div>
                                        <small class="text-muted">
                                            Health: {{ $vehicle->health_score }}% | 
                                            Mileage: {{ number_format($vehicle->mileage) }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $vehicle->health_score < 40 ? 'danger' : 'warning' }} rounded-pill">
                                        {{ $vehicle->health_score < 40 ? 'Critical' : 'High' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        @if($breakdownRisk->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">And {{ $breakdownRisk->count() - 5 }} more vehicles at risk</small>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">No vehicles currently at high risk</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Trends -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Performance Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Maintenance Type Chart
    const typeCtx = document.getElementById('maintenanceTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($maintenanceTypes as $type)
                    '{{ ucfirst(str_replace("_", " ", $type->maintenance_type)) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($maintenanceTypes as $type)
                        {{ $type->count }},
                    @endforeach
                ],
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', 
                    '#6f42c1', '#fd7e14', '#20c997', '#6c757d'
                ]
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

    // Cost Forecast Chart
    const costCtx = document.getElementById('costForecastChart').getContext('2d');
    new Chart(costCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($monthlyCosts as $cost)
                    '{{ date("M Y", mktime(0, 0, 0, $cost->month, 1, $cost->year)) }}',
                @endforeach
            ],
            datasets: [{
                label: 'Monthly Maintenance Cost',
                data: [
                    @foreach($monthlyCosts as $cost)
                        {{ $cost->total_cost }},
                    @endforeach
                ],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Cost: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Performance Trend Chart
    const perfCtx = document.getElementById('performanceTrendChart').getContext('2d');
    new Chart(perfCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($performanceTrends as $trend)
                    '{{ date("M d", strtotime($trend->date)) }}',
                @endforeach
            ],
            datasets: [{
                label: 'Services Completed',
                data: [
                    @foreach($performanceTrends as $trend)
                        {{ $trend->service_count }},
                    @endforeach
                ],
                backgroundColor: '#28a745',
                yAxisID: 'y'
            }, {
                label: 'Average Cost',
                data: [
                    @foreach($performanceTrends as $trend)
                        {{ $trend->avg_cost }},
                    @endforeach
                ],
                type: 'line',
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(0);
                        }
                    }
                }
            }
        }
    });

    // Initialize DataTable for health scoring
    $('#healthTable').DataTable({
        order: [[2, 'asc']], // Sort by health score ascending (worst first)
        pageLength: 10,
        responsive: true
    });
});
</script>
@endsection
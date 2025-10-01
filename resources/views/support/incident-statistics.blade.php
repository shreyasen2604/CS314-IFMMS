@extends('layouts.app')

@section('title', 'Incident Statistics - IFMMS')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-danger bg-opacity-10 me-3">
                                    <i class="fas fa-chart-line text-danger fs-4"></i>
                                </div>
                                <div>
                                    <h1 class="h3 mb-1 fw-bold">Incident Statistics</h1>
                                    <p class="text-muted mb-0">Comprehensive incident analysis and trends</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="{{ route('support.incidents.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Incidents
                            </a>
                            <a href="{{ route('support.incidents.export') }}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Export Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Damage Cost</h6>
                            <h3 class="mb-0">${{ number_format($stats['total_damage_cost'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="icon-shape bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-dollar-sign text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Avg Resolution Time</h6>
                            <h3 class="mb-0">{{ round($stats['avg_resolution_time'] ?? 0) }} days</h3>
                        </div>
                        <div class="icon-shape bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Critical Incidents</h6>
                            <h3 class="mb-0">{{ $stats['by_severity']->where('severity', 'critical')->first()->count ?? 0 }}</h3>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Resolved Rate</h6>
                            @php
                                $total = $stats['by_status']->sum('count');
                                $resolved = $stats['by_status']->whereIn('status', ['resolved', 'closed'])->sum('count');
                                $rate = $total > 0 ? ($resolved / $total) * 100 : 0;
                            @endphp
                            <h3 class="mb-0">{{ round($rate) }}%</h3>
                        </div>
                        <div class="icon-shape bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Incidents by Type -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Incidents by Type</h5>
                </div>
                <div class="card-body">
                    <canvas id="incidentsByTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Incidents by Severity -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Incidents by Severity</h5>
                </div>
                <div class="card-body">
                    <canvas id="incidentsBySeverityChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Trend -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Monthly Incident Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Current Status Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicles with Most Incidents -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Vehicles with Most Incidents</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Registration</th>
                                    <th>Total Incidents</th>
                                    <th>Critical</th>
                                    <th>Major</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['vehicles_most_incidents'] ?? [] as $vehicle)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                                        <small class="text-muted">{{ $vehicle->year }}</small>
                                    </td>
                                    <td>{{ $vehicle->registration_number }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $vehicle->incident_reports_count }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $critical = $vehicle->incidentReports->where('severity', 'critical')->count();
                                        @endphp
                                        @if($critical > 0)
                                            <span class="badge bg-danger">{{ $critical }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $major = $vehicle->incidentReports->where('severity', 'major')->count();
                                        @endphp
                                        @if($major > 0)
                                            <span class="badge bg-warning">{{ $major }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $vehicle->status == 'operational' ? 'success' : 'warning' }}">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('maintenance.vehicles.index') }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No vehicle incident data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.icon-shape {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

@push('scripts')
<script>
// Prepare data for charts
const byType = @json($stats['by_type'] ?? []);
const bySeverity = @json($stats['by_severity'] ?? []);
const byStatus = @json($stats['by_status'] ?? []);
const monthlyTrend = @json($stats['monthly_trend'] ?? []);

// Incidents by Type Chart
const typeCtx = document.getElementById('incidentsByTypeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'bar',
    data: {
        labels: byType.map(item => item.type.replace('_', ' ').charAt(0).toUpperCase() + item.type.slice(1)),
        datasets: [{
            label: 'Number of Incidents',
            data: byType.map(item => item.count),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Incidents by Severity Chart
const severityCtx = document.getElementById('incidentsBySeverityChart').getContext('2d');
new Chart(severityCtx, {
    type: 'doughnut',
    data: {
        labels: bySeverity.map(item => item.severity.charAt(0).toUpperCase() + item.severity.slice(1)),
        datasets: [{
            data: bySeverity.map(item => item.count),
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
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

// Monthly Trend Chart
const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: monthlyTrend.map(item => {
            const [year, month] = item.month.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }).reverse(),
        datasets: [{
            label: 'Incidents',
            data: monthlyTrend.map(item => item.count).reverse(),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: byStatus.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
        datasets: [{
            data: byStatus.map(item => item.count),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
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
</script>
@endpush
@endsection
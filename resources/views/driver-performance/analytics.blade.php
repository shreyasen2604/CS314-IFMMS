@extends('layouts.app')

@section('title', 'Driver Performance Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Driver Performance Analytics</h3>
                    <div>
                        <a href="{{ route('maintenance.driver-performance.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Back to Overview
                        </a>
                        <a href="{{ route('maintenance.driver-performance.rankings') }}" class="btn btn-info">
                            <i class="fas fa-trophy"></i> Rankings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Period Filter -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('maintenance.driver-performance.analytics') }}">
                                <label for="period" class="form-label">Analysis Period</label>
                                <select name="period" class="form-select" id="period" onchange="this.form.submit()">
                                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                                    <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Fleet Performance Overview -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Fleet Performance Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-primary">{{ $analytics['fleet_performance']['total_assignments'] ?? 0 }}</h3>
                                                <p class="text-muted mb-0">Total Assignments</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ $analytics['fleet_performance']['completed_assignments'] ?? 0 }}</h3>
                                                <p class="text-muted mb-0">Completed</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-info">{{ number_format($analytics['fleet_performance']['avg_completion_rate'] ?? 0, 1) }}%</h3>
                                                <p class="text-muted mb-0">Avg Completion Rate</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-warning">{{ number_format($analytics['fleet_performance']['total_distance'] ?? 0, 0) }} km</h3>
                                                <p class="text-muted mb-0">Total Distance</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Performance Distribution</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $distribution = $analytics['performance_distribution'] ?? ['excellent' => 0, 'good' => 0, 'average' => 0, 'poor' => 0];
                                        $total = array_sum($distribution);
                                    @endphp
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>Excellent (90-100%)</span>
                                            <span class="badge bg-success">{{ $distribution['excellent'] }}</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ $total > 0 ? ($distribution['excellent'] / $total) * 100 : 0 }}%">
                                                {{ $total > 0 ? number_format(($distribution['excellent'] / $total) * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>Good (80-89%)</span>
                                            <span class="badge bg-info">{{ $distribution['good'] }}</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-info" 
                                                 style="width: {{ $total > 0 ? ($distribution['good'] / $total) * 100 : 0 }}%">
                                                {{ $total > 0 ? number_format(($distribution['good'] / $total) * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>Average (70-79%)</span>
                                            <span class="badge bg-warning">{{ $distribution['average'] }}</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-warning" 
                                                 style="width: {{ $total > 0 ? ($distribution['average'] / $total) * 100 : 0 }}%">
                                                {{ $total > 0 ? number_format(($distribution['average'] / $total) * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>Poor (<70%)</span>
                                            <span class="badge bg-danger">{{ $distribution['poor'] }}</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-danger" 
                                                 style="width: {{ $total > 0 ? ($distribution['poor'] / $total) * 100 : 0 }}%">
                                                {{ $total > 0 ? number_format(($distribution['poor'] / $total) * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Top Performers</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($analytics['top_performers']) && count($analytics['top_performers']) > 0)
                                        @foreach(array_slice($analytics['top_performers'], 0, 5) as $index => $performer)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="me-3">
                                                <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') }} fs-6">
                                                    #{{ $index + 1 }}
                                                </span>
                                            </div>
                                            <div class="avatar-sm me-3">
                                                @if($performer['driver']->profile_picture)
                                                    <img src="{{ asset('storage/' . $performer['driver']->profile_picture) }}" 
                                                         class="rounded-circle" width="35" height="35" alt="Avatar">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 35px; height: 35px;">
                                                        <span class="text-white fw-bold small">{{ substr($performer['driver']->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">{{ $performer['driver']->name }}</div>
                                                <small class="text-muted">Score: {{ number_format($performer['score'], 1) }}%</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                            <p>No performance data available for this period.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics Charts -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Fuel Efficiency Trends</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="fuelEfficiencyChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">On-Time Performance Trends</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="onTimeChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Improvement Areas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Areas for Improvement</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card border-warning">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                                    <h5>Late Arrivals</h5>
                                                    <p class="text-muted">Focus on punctuality training and route optimization</p>
                                                    <span class="badge bg-warning">Priority: Medium</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-danger">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-gas-pump fa-3x text-danger mb-3"></i>
                                                    <h5>Fuel Efficiency</h5>
                                                    <p class="text-muted">Implement eco-driving training programs</p>
                                                    <span class="badge bg-danger">Priority: High</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-shield-alt fa-3x text-info mb-3"></i>
                                                    <h5>Safety Incidents</h5>
                                                    <p class="text-muted">Enhance safety protocols and regular training</p>
                                                    <span class="badge bg-info">Priority: High</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Fuel Efficiency Chart
    const fuelCtx = document.getElementById('fuelEfficiencyChart').getContext('2d');
    new Chart(fuelCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Fleet Average (km/L)',
                data: [12.5, 13.2, 12.8, 13.5],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Fuel Efficiency (km/L)'
                    }
                }
            }
        }
    });

    // On-Time Performance Chart
    const onTimeCtx = document.getElementById('onTimeChart').getContext('2d');
    new Chart(onTimeCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'On-Time Percentage',
                data: [85, 88, 92, 89],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                }
            }
        }
    });
});
</script>
@endpush
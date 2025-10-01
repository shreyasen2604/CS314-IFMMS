@extends('layouts.app')

@section('title', 'Driver Performance Overview')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Driver Performance Overview</h3>
                    <div>
                        <a href="{{ route('maintenance.driver-performance.rankings') }}" class="btn btn-info me-2">
                            <i class="fas fa-trophy"></i> Rankings
                        </a>
                        <a href="{{ route('maintenance.driver-performance.analytics') }}" class="btn btn-success">
                            <i class="fas fa-chart-line"></i> Analytics
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['total_drivers'] }}</h4>
                                            <p class="mb-0">Total Drivers</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['active_drivers'] }}</h4>
                                            <p class="mb-0">Active This Month</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ number_format($stats['avg_performance_score'], 1) }}%</h4>
                                            <p class="mb-0">Avg Performance</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-tachometer-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['safety_incidents'] }}</h4>
                                            <p class="mb-0">Safety Incidents</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" action="{{ route('maintenance.driver-performance.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search drivers..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="performance_grade" class="form-select">
                                        <option value="">All Performance Grades</option>
                                        <option value="excellent" {{ request('performance_grade') == 'excellent' ? 'selected' : '' }}>Excellent (90-100%)</option>
                                        <option value="good" {{ request('performance_grade') == 'good' ? 'selected' : '' }}>Good (80-89%)</option>
                                        <option value="average" {{ request('performance_grade') == 'average' ? 'selected' : '' }}>Average (70-79%)</option>
                                        <option value="poor" {{ request('performance_grade') == 'poor' ? 'selected' : '' }}>Poor (<70%)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('maintenance.driver-performance.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                                        <i class="fas fa-file-export"></i> Export
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Drivers Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Driver</th>
                                    <th>Current Performance</th>
                                    <th>Routes This Month</th>
                                    <th>On-Time Rate</th>
                                    <th>Fuel Efficiency</th>
                                    <th>Safety Score</th>
                                    <th>Last Activity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($drivers as $driver)
                                @php
                                    $latestMetric = $driver->driverPerformanceMetrics->first();
                                    $onTimeRate = $latestMetric ? $latestMetric->on_time_percentage : 0;
                                    $fuelEfficiency = $latestMetric ? $latestMetric->fuel_efficiency : 0;
                                    $safetyScore = $latestMetric ? (100 - ($latestMetric->safety_incidents * 10)) : 100;
                                    $routesCompleted = $latestMetric ? $latestMetric->routes_completed : 0;
                                    $routesAssigned = $latestMetric ? $latestMetric->routes_assigned : 0;
                                    
                                    // Calculate overall performance grade
                                    $overallScore = ($onTimeRate + min($safetyScore, 100)) / 2;
                                    if ($overallScore >= 90) $gradeClass = 'success';
                                    elseif ($overallScore >= 80) $gradeClass = 'info';
                                    elseif ($overallScore >= 70) $gradeClass = 'warning';
                                    else $gradeClass = 'danger';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                @if($driver->profile_picture)
                                                    <img src="{{ asset('storage/' . $driver->profile_picture) }}" 
                                                         class="rounded-circle" width="40" height="40" alt="Avatar">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <span class="text-white fw-bold">{{ substr($driver->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>{{ $driver->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $driver->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 20px;">
                                                <div class="progress-bar bg-{{ $gradeClass }}" 
                                                     style="width: {{ $overallScore }}%"></div>
                                            </div>
                                            <span class="badge bg-{{ $gradeClass }}">{{ number_format($overallScore, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $routesCompleted }}</span> / {{ $routesAssigned }}
                                        @if($routesAssigned > 0)
                                            <br>
                                            <small class="text-muted">
                                                {{ number_format(($routesCompleted / $routesAssigned) * 100, 1) }}% completion
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $onTimeRate >= 90 ? 'success' : ($onTimeRate >= 80 ? 'warning' : 'danger') }}">
                                            {{ number_format($onTimeRate, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($fuelEfficiency > 0)
                                            {{ number_format($fuelEfficiency, 2) }} km/L
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $safetyScore >= 90 ? 'success' : ($safetyScore >= 80 ? 'warning' : 'danger') }}">
                                            {{ number_format($safetyScore, 0) }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($latestMetric)
                                            {{ $latestMetric->metric_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">No data</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('maintenance.driver-performance.show', $driver) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning update-metrics-btn" 
                                                    data-driver-id="{{ $driver->id }}" data-driver-name="{{ $driver->name }}"
                                                    title="Update Metrics">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <h5>No drivers found</h5>
                                            <p>No drivers match your current filters.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($drivers->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $drivers->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Metrics Modal -->
<div class="modal fade" id="updateMetricsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Driver Metrics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateMetricsForm">
                <div class="modal-body">
                    <input type="hidden" id="driver_id" name="driver_id">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="safety_incidents" class="form-label">Safety Incidents</label>
                                <input type="number" class="form-control" id="safety_incidents" name="safety_incidents" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="traffic_violations" class="form-label">Traffic Violations</label>
                                <input type="number" class="form-control" id="traffic_violations" name="traffic_violations" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="customer_rating" class="form-label">Customer Rating (1-5)</label>
                        <input type="number" class="form-control" id="customer_rating" name="customer_rating" min="1" max="5" step="0.1">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Metrics</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Performance Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('maintenance.driver-performance.generate-report') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Drivers</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select_all_drivers">
                            <label class="form-check-label" for="select_all_drivers">
                                <strong>Select All</strong>
                            </label>
                        </div>
                        <hr>
                        <div style="max-height: 200px; overflow-y: auto;">
                            @foreach($drivers as $driver)
                            <div class="form-check">
                                <input class="form-check-input driver-checkbox" type="checkbox" 
                                       name="driver_ids[]" value="{{ $driver->id }}" id="driver_{{ $driver->id }}">
                                <label class="form-check-label" for="driver_{{ $driver->id }}">
                                    {{ $driver->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" required 
                                       value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" required 
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="format" class="form-label">Format</label>
                        <select class="form-select" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update metrics modal
    $('.update-metrics-btn').click(function() {
        const driverId = $(this).data('driver-id');
        const driverName = $(this).data('driver-name');
        
        $('#driver_id').val(driverId);
        $('.modal-title').text('Update Metrics for ' + driverName);
        $('#updateMetricsModal').modal('show');
    });

    // Handle metrics form submission
    $('#updateMetricsForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("maintenance.driver-performance.update-metrics") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#updateMetricsModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating metrics: ' + xhr.responseJSON.message);
            }
        });
    });

    // Select all drivers checkbox
    $('#select_all_drivers').change(function() {
        $('.driver-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Auto-submit form on filter change
    $('select[name="performance_grade"]').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
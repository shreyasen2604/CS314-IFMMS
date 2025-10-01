@extends('layouts.app')

@section('title', 'My Vehicle - Maintenance')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">My Vehicle Maintenance</h1>
                <p class="page-subtitle">{{ $vehicle->vehicle_number }} - {{ $vehicle->make }} {{ $vehicle->model }}</p>
            </div>
        </div>
    </div>

    <!-- Vehicle Status Overview -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Vehicle Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Vehicle Number:</strong></td>
                                    <td>{{ $vehicle->vehicle_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Make/Model:</strong></td>
                                    <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Year:</strong></td>
                                    <td>{{ $vehicle->year }}</td>
                                </tr>
                                <tr>
                                    <td><strong>License Plate:</strong></td>
                                    <td>{{ $vehicle->license_plate }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Current Mileage:</strong></td>
                                    <td>{{ number_format($vehicle->mileage) }} miles</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $vehicle->status === 'active' ? 'success' : ($vehicle->status === 'maintenance' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Last Maintenance:</strong></td>
                                    <td>{{ $vehicle->last_maintenance_date ? $vehicle->last_maintenance_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Next Maintenance Due:</strong></td>
                                    <td>
                                        @if($vehicle->next_maintenance_due)
                                            <span class="text-{{ $vehicle->isOverdue() ? 'danger' : ($vehicle->isDueSoon() ? 'warning' : 'success') }}">
                                                {{ $vehicle->next_maintenance_due->format('M d, Y') }}
                                                @if($vehicle->isOverdue())
                                                    (Overdue)
                                                @elseif($vehicle->isDueSoon())
                                                    (Due Soon)
                                                @endif
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Health Score</h5>
                </div>
                <div class="card-body text-center">
                    <div class="health-score-circle mb-3">
                        <canvas id="healthScoreChart" width="120" height="120"></canvas>
                    </div>
                    <h3 class="text-{{ $vehicle->health_status === 'excellent' ? 'success' : ($vehicle->health_status === 'good' ? 'info' : ($vehicle->health_status === 'fair' ? 'warning' : 'danger')) }}">
                        {{ $vehicle->health_score }}%
                    </h3>
                    <p class="text-muted">{{ ucfirst($vehicle->health_status) }} Condition</p>
                    
                    <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#requestMaintenanceModal">
                        <i class="fas fa-wrench"></i> Request Maintenance
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Alerts -->
    @if($activeAlerts->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Active Alerts
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($activeAlerts as $alert)
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : 'info') }} mb-0">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $alert->title }}</strong><br>
                                            <small>{{ $alert->message }}</small>
                                        </div>
                                        <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : 'info') }}">
                                            {{ ucfirst($alert->severity) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Upcoming Scheduled Maintenance -->
    @if($upcomingSchedules->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Upcoming Scheduled Maintenance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Scheduled Date</th>
                                    <th>Technician</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingSchedules as $schedule)
                                    <tr>
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
                                        <td>{{ $schedule->estimated_duration }} min</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Maintenance History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Maintenance History</h5>
                </div>
                <div class="card-body">
                    @if($recentRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Technician</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th>Mileage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRecords as $record)
                                        <tr>
                                            <td>{{ $record->service_date->format('M d, Y') }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $record->maintenance_type)) }}</td>
                                            <td>{{ Str::limit($record->description, 50) }}</td>
                                            <td>{{ $record->technician->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($record->cost, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $record->status === 'completed' ? 'success' : ($record->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($record->mileage_at_service) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('maintenance.records', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-outline-primary">
                                View Complete History
                            </a>
                        </div>
                    @else
                        <p class="text-muted text-center">No maintenance history available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Maintenance Modal -->
<div class="modal fade" id="requestMaintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Maintenance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="requestMaintenanceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Maintenance Type</label>
                        <select class="form-select" name="maintenance_type" required>
                            <option value="">Select Type</option>
                            <option value="routine">Routine Inspection</option>
                            <option value="corrective">Repair Issue</option>
                            <option value="emergency">Emergency Repair</option>
                            <option value="oil_change">Oil Change</option>
                            <option value="tire_rotation">Tire Service</option>
                            <option value="brake_service">Brake Service</option>
                            <option value="engine_service">Engine Service</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority" required>
                            <option value="low">Low - Can wait</option>
                            <option value="medium" selected>Medium - Normal priority</option>
                            <option value="high">High - Urgent</option>
                            <option value="critical">Critical - Safety issue</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Please describe the issue or maintenance needed..." required></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Your maintenance request will be reviewed by the maintenance team and scheduled accordingly.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Health Score Gauge Chart
    const ctx = document.getElementById('healthScoreChart').getContext('2d');
    const healthScore = {{ $vehicle->health_score }};
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [healthScore, 100 - healthScore],
                backgroundColor: [
                    healthScore >= 80 ? '#28a745' : 
                    healthScore >= 60 ? '#17a2b8' : 
                    healthScore >= 40 ? '#ffc107' : '#dc3545',
                    '#e9ecef'
                ],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            }
        }
    });
    
    // Request maintenance form
    document.getElementById('requestMaintenanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/maintenance/request-maintenance', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Maintenance request submitted successfully!');
                bootstrap.Modal.getInstance(document.getElementById('requestMaintenanceModal')).hide();
                this.reset();
            } else {
                alert('Error submitting request: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error submitting request');
        });
    });
});
</script>
@endsection
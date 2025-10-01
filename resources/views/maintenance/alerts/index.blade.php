@extends('layouts.app')

@section('title', 'Alert Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Alert Management</h1>
                <p class="page-subtitle">Configure thresholds, manage notifications, and track alert history</p>
            </div>
        </div>
    </div>

    <!-- Alert Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $alertStats['total'] }}</h4>
                            <p class="mb-0">Total Alerts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x"></i>
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
                            <h4 class="mb-0">{{ $alertStats['active'] }}</h4>
                            <p class="mb-0">Active Alerts</p>
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
                            <h4 class="mb-0">{{ $alertStats['critical'] }}</h4>
                            <p class="mb-0">Critical Alerts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-fire fa-2x"></i>
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
                            <h4 class="mb-0">{{ $alertStats['acknowledged'] }}</h4>
                            <p class="mb-0">Acknowledged</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div>
                    @if(auth()->user()->role === 'Admin')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#configureModal">
                        <i class="fas fa-cog"></i> Configure Thresholds
                    </button>
                    <button class="btn btn-outline-info" onclick="testAlert()">
                        <i class="fas fa-vial"></i> Test Alert
                    </button>
                    @endif
                    
                    @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
                    <button class="btn btn-outline-secondary" onclick="bulkAcknowledge()">
                        <i class="fas fa-check-double"></i> Bulk Acknowledge
                    </button>
                    @endif
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="alertFilter" id="allAlerts" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="allAlerts">All</label>

                        <input type="radio" class="btn-check" name="alertFilter" id="activeAlerts" autocomplete="off">
                        <label class="btn btn-outline-warning" for="activeAlerts">Active</label>

                        <input type="radio" class="btn-check" name="alertFilter" id="criticalAlerts" autocomplete="off">
                        <label class="btn btn-outline-danger" for="criticalAlerts">Critical</label>

                        <input type="radio" class="btn-check" name="alertFilter" id="acknowledgedAlerts" autocomplete="off">
                        <label class="btn btn-outline-success" for="acknowledgedAlerts">Acknowledged</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Maintenance Alerts</h5>
                </div>
                <div class="card-body">
                    @if($alerts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="alertsTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Severity</th>
                                        <th>Vehicle</th>
                                        <th>Alert Type</th>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alerts as $alert)
                                        <tr class="alert-row" 
                                            data-status="{{ $alert->status }}" 
                                            data-severity="{{ $alert->severity }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input alert-checkbox" value="{{ $alert->id }}">
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'high' ? 'warning' : ($alert->severity === 'medium' ? 'info' : 'secondary')) }}">
                                                    <i class="fas fa-{{ $alert->severity === 'critical' ? 'fire' : ($alert->severity === 'high' ? 'exclamation-triangle' : ($alert->severity === 'medium' ? 'info-circle' : 'circle')) }}"></i>
                                                    {{ ucfirst($alert->severity) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $alert->vehicle->vehicle_number }}</strong><br>
                                                <small class="text-muted">{{ $alert->vehicle->make }} {{ $alert->vehicle->model }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $alert->title }}</strong>
                                                @if($alert->threshold_value && $alert->current_value)
                                                    <br><small class="text-muted">
                                                        Current: {{ $alert->current_value }} | Threshold: {{ $alert->threshold_value }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span data-bs-toggle="tooltip" title="{{ $alert->message }}">
                                                    {{ Str::limit($alert->message, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $alert->created_at->format('M d, Y H:i') }}<br>
                                                <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($alert->status === 'active')
                                                    <span class="badge bg-warning">Active</span>
                                                @elseif($alert->status === 'acknowledged')
                                                    <span class="badge bg-info">Acknowledged</span>
                                                    <br><small class="text-muted">by {{ $alert->acknowledgedBy->name }}</small>
                                                @else
                                                    <span class="badge bg-success">Resolved</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($alert->status === 'active')
                                                        <button class="btn btn-outline-warning" onclick="acknowledgeAlert({{ $alert->id }})" data-bs-toggle="tooltip" title="Acknowledge">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    @if($alert->status !== 'resolved')
                                                        <button class="btn btn-outline-success" onclick="resolveAlert({{ $alert->id }})" data-bs-toggle="tooltip" title="Resolve">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-outline-primary" onclick="viewAlert({{ $alert->id }})" data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info" onclick="createMaintenanceFromAlert({{ $alert->id }})" data-bs-toggle="tooltip" title="Schedule Maintenance">
                                                        <i class="fas fa-calendar-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $alerts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No alerts found</h5>
                            <p class="text-muted">Your fleet is running smoothly!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configure Thresholds Modal -->
<div class="modal fade" id="configureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configure Alert Thresholds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="thresholdForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Health Score Thresholds</h6>
                            <div class="mb-3">
                                <label class="form-label">Critical Health Score (%)</label>
                                <input type="number" class="form-control" name="critical_health_score" value="40" min="0" max="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Warning Health Score (%)</label>
                                <input type="number" class="form-control" name="warning_health_score" value="60" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Mileage Thresholds</h6>
                            <div class="mb-3">
                                <label class="form-label">Maintenance Due (miles)</label>
                                <input type="number" class="form-control" name="maintenance_due_mileage" value="5000" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Overdue Warning (miles)</label>
                                <input type="number" class="form-control" name="overdue_warning_mileage" value="1000" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Time-based Thresholds</h6>
                            <div class="mb-3">
                                <label class="form-label">Maintenance Due (days)</label>
                                <input type="number" class="form-control" name="maintenance_due_days" value="90" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Overdue Warning (days)</label>
                                <input type="number" class="form-control" name="overdue_warning_days" value="7" min="0">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Cost Thresholds</h6>
                            <div class="mb-3">
                                <label class="form-label">Monthly Cost Limit ($)</label>
                                <input type="number" class="form-control" name="monthly_cost_limit" value="10000" min="0" step="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Single Service Limit ($)</label>
                                <input type="number" class="form-control" name="service_cost_limit" value="2000" min="0" step="50">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6>Notification Preferences</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                <label class="form-check-label" for="emailNotifications">
                                    Email Notifications
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="smsNotifications">
                                <label class="form-check-label" for="smsNotifications">
                                    SMS Notifications
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dashboardNotifications" checked>
                                <label class="form-check-label" for="dashboardNotifications">
                                    Dashboard Notifications
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Alert Details Modal -->
<div class="modal fade" id="alertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alert Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="alertDetails">
                <!-- Alert details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Alert filtering
    document.querySelectorAll('input[name="alertFilter"]').forEach(radio => {
        radio.addEventListener('change', function() {
            filterAlerts(this.id);
        });
    });
    
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.alert-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

function filterAlerts(filter) {
    const rows = document.querySelectorAll('.alert-row');
    
    rows.forEach(row => {
        let show = true;
        
        switch(filter) {
            case 'activeAlerts':
                show = row.dataset.status === 'active';
                break;
            case 'criticalAlerts':
                show = row.dataset.severity === 'critical';
                break;
            case 'acknowledgedAlerts':
                show = row.dataset.status === 'acknowledged';
                break;
            default:
                show = true;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function acknowledgeAlert(id) {
    fetch(`/maintenance/alerts/${id}/acknowledge`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error acknowledging alert');
        }
    });
}

function resolveAlert(id) {
    if (confirm('Are you sure you want to resolve this alert?')) {
        fetch(`/maintenance/alerts/${id}/resolve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error resolving alert');
            }
        });
    }
}

function viewAlert(id) {
    fetch(`/maintenance/alerts/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('alertDetails').innerHTML = `
                <div class="row">
                    <div class="col-12">
                        <h6>Vehicle: ${data.vehicle.vehicle_number}</h6>
                        <p><strong>Type:</strong> ${data.alert_type.replace('_', ' ')}</p>
                        <p><strong>Severity:</strong> <span class="badge bg-${data.severity === 'critical' ? 'danger' : (data.severity === 'high' ? 'warning' : 'info')}">${data.severity}</span></p>
                        <p><strong>Title:</strong> ${data.title}</p>
                        <p><strong>Message:</strong> ${data.message}</p>
                        ${data.threshold_value ? `<p><strong>Threshold:</strong> ${data.threshold_value}</p>` : ''}
                        ${data.current_value ? `<p><strong>Current Value:</strong> ${data.current_value}</p>` : ''}
                        <p><strong>Created:</strong> ${new Date(data.created_at).toLocaleString()}</p>
                        ${data.acknowledged_at ? `<p><strong>Acknowledged:</strong> ${new Date(data.acknowledged_at).toLocaleString()} by ${data.acknowledged_by.name}</p>` : ''}
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('alertModal')).show();
        });
}

function createMaintenanceFromAlert(id) {
    // Redirect to scheduler with alert context
    window.location.href = `/maintenance/schedule?alert_id=${id}`;
}

function bulkAcknowledge() {
    const selectedAlerts = Array.from(document.querySelectorAll('.alert-checkbox:checked')).map(cb => cb.value);
    
    if (selectedAlerts.length === 0) {
        alert('Please select alerts to acknowledge');
        return;
    }
    
    if (confirm(`Acknowledge ${selectedAlerts.length} selected alerts?`)) {
        fetch('/maintenance/alerts/bulk-acknowledge', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ alert_ids: selectedAlerts })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error acknowledging alerts');
            }
        });
    }
}

function testAlert() {
    fetch('/maintenance/alerts/test', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test alert created successfully');
            location.reload();
        } else {
            alert('Error creating test alert');
        }
    });
}

// Threshold form submission
document.getElementById('thresholdForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/maintenance/alerts/configure', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Configuration saved successfully');
            bootstrap.Modal.getInstance(document.getElementById('configureModal')).hide();
        } else {
            alert('Error saving configuration');
        }
    });
});
</script>
@endsection
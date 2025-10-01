@extends('layouts.app')

@section('title', 'Technician Work Queue')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Work Queue</h1>
                <p class="page-subtitle">Manage your assigned maintenance tasks</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $assignedWork->count() }}</h4>
                            <p class="mb-0">Assigned Tasks</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tasks fa-2x"></i>
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
                            <h4 class="mb-0">{{ $assignedWork->where('status', 'in_progress')->count() }}</h4>
                            <p class="mb-0">In Progress</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cog fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $availableWork->count() }}</h4>
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
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $assignedWork->where('scheduled_date', '>=', today())->where('scheduled_date', '<=', today())->count() }}</h4>
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
        <!-- Assigned Work -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">My Assigned Tasks</h5>
                </div>
                <div class="card-body">
                    @if($assignedWork->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Scheduled</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedWork as $work)
                                        <tr class="{{ $work->scheduled_date->isPast() && $work->status !== 'completed' ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $work->vehicle->vehicle_number }}</strong><br>
                                                <small class="text-muted">{{ $work->vehicle->make }} {{ $work->vehicle->model }}</small>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $work->maintenance_type)) }}</td>
                                            <td>
                                                {{ $work->scheduled_date->format('M d, Y H:i') }}
                                                @if($work->scheduled_date->isPast() && $work->status !== 'completed')
                                                    <br><small class="text-danger">Overdue</small>
                                                @elseif($work->scheduled_date->isToday())
                                                    <br><small class="text-warning">Due Today</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $work->priority === 'critical' ? 'danger' : ($work->priority === 'high' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($work->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $work->status === 'in_progress' ? 'warning' : ($work->status === 'completed' ? 'success' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $work->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $work->estimated_duration }} min</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($work->status === 'scheduled' || $work->status === 'confirmed')
                                                        <button class="btn btn-success" onclick="startWork({{ $work->id }})" data-bs-toggle="tooltip" title="Start Work">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    @elseif($work->status === 'in_progress')
                                                        <button class="btn btn-info" onclick="updateProgress({{ $work->id }})" data-bs-toggle="tooltip" title="Update Progress">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-primary" onclick="completeWork({{ $work->id }})" data-bs-toggle="tooltip" title="Complete Work">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-outline-primary" onclick="viewDetails({{ $work->id }})" data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No assigned tasks</h5>
                            <p class="text-muted">Check the available tasks to claim new work.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Work -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Available Tasks</h5>
                </div>
                <div class="card-body">
                    @if($availableWork->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($availableWork->take(10) as $work)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $work->vehicle->vehicle_number }}</div>
                                        <small class="text-muted">
                                            {{ ucfirst(str_replace('_', ' ', $work->maintenance_type)) }} - 
                                            {{ $work->scheduled_date->format('M d, H:i') }}
                                        </small>
                                    </div>
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="badge bg-{{ $work->priority === 'critical' ? 'danger' : ($work->priority === 'high' ? 'warning' : 'info') }} mb-1">
                                            {{ ucfirst($work->priority) }}
                                        </span>
                                        <button class="btn btn-outline-primary btn-sm" onclick="claimWork({{ $work->id }})">
                                            Claim
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($availableWork->count() > 10)
                            <div class="text-center mt-3">
                                <small class="text-muted">And {{ $availableWork->count() - 10 }} more tasks available</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">No available tasks</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="progressForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Progress Notes</label>
                        <textarea class="form-control" name="progress_notes" rows="4" placeholder="Describe the work completed so far..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Progress</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Work Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Maintenance Work</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Parts Cost ($)</label>
                                <input type="number" class="form-control" name="parts_cost" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Labor Cost ($)</label>
                                <input type="number" class="form-control" name="labor_cost" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Work Completed Notes</label>
                        <textarea class="form-control" name="notes" rows="4" placeholder="Describe the work completed, parts replaced, etc..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Next Service Date (Optional)</label>
                        <input type="date" class="form-control" name="next_service_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Work</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Work Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Work Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="workDetails">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentWorkId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function startWork(id) {
    if (confirm('Are you ready to start this maintenance work?')) {
        fetch(`/maintenance/work/${id}/start`, {
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
                alert('Error starting work: ' + data.message);
            }
        });
    }
}

function updateProgress(id) {
    currentWorkId = id;
    new bootstrap.Modal(document.getElementById('progressModal')).show();
}

function completeWork(id) {
    currentWorkId = id;
    new bootstrap.Modal(document.getElementById('completeModal')).show();
}

function claimWork(id) {
    if (confirm('Do you want to claim this maintenance task?')) {
        fetch(`/maintenance/work/${id}/start`, {
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
                alert('Error claiming work: ' + data.message);
            }
        });
    }
}

function viewDetails(id) {
    // Load work details
    fetch(`/maintenance/schedule/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('workDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Vehicle Information</h6>
                        <p><strong>Vehicle:</strong> ${data.vehicle.vehicle_number}</p>
                        <p><strong>Make/Model:</strong> ${data.vehicle.make} ${data.vehicle.model}</p>
                        <p><strong>Current Mileage:</strong> ${data.vehicle.mileage.toLocaleString()}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Work Details</h6>
                        <p><strong>Type:</strong> ${data.maintenance_type.replace('_', ' ')}</p>
                        <p><strong>Priority:</strong> <span class="badge bg-${data.priority === 'critical' ? 'danger' : (data.priority === 'high' ? 'warning' : 'info')}">${data.priority}</span></p>
                        <p><strong>Estimated Duration:</strong> ${data.estimated_duration} minutes</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p>${data.description || 'No description provided'}</p>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('detailsModal')).show();
        });
}

// Progress form submission
document.getElementById('progressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/maintenance/work/${currentWorkId}/update`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Progress updated successfully');
            bootstrap.Modal.getInstance(document.getElementById('progressModal')).hide();
            this.reset();
        } else {
            alert('Error updating progress: ' + data.message);
        }
    });
});

// Complete form submission
document.getElementById('completeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/maintenance/work/${currentWorkId}/complete`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Work completed successfully');
            bootstrap.Modal.getInstance(document.getElementById('completeModal')).hide();
            location.reload();
        } else {
            alert('Error completing work: ' + data.message);
        }
    });
});
</script>
@endsection
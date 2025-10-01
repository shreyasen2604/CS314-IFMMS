@extends('layouts.app')

@section('title', 'Maintenance Scheduler')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Maintenance Scheduler</h1>
                <p class="page-subtitle">Schedule and manage maintenance appointments</p>
            </div>
        </div>
    </div>

    <!-- Debug Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <strong>Debug Info:</strong> 
                Total Schedules: {{ $schedules->count() }} | 
                Total Vehicles: {{ $vehicles->count() }} | 
                Total Technicians: {{ $technicians->count() }}
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                <i class="fas fa-plus"></i> Schedule Maintenance
            </button>
        </div>
    </div>

    <!-- Schedules Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Scheduled Maintenance</h5>
                </div>
                <div class="card-body">
                    @if($schedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Scheduled Date</th>
                                        <th>Technician</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->id }}</td>
                                            <td>
                                                @if($schedule->vehicle)
                                                    <strong>{{ $schedule->vehicle->vehicle_number }}</strong><br>
                                                    <small class="text-muted">{{ $schedule->vehicle->make }} {{ $schedule->vehicle->model }}</small>
                                                @else
                                                    <span class="text-muted">No vehicle</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $schedule->maintenance_type)) }}</td>
                                            <td>
                                                @if($schedule->scheduled_date)
                                                    {{ \Carbon\Carbon::parse($schedule->scheduled_date)->format('M d, Y H:i') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($schedule->technician)
                                                    {{ $schedule->technician->name }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->priority === 'critical' ? 'danger' : ($schedule->priority === 'high' ? 'warning' : ($schedule->priority === 'medium' ? 'info' : 'success')) }}">
                                                    {{ ucfirst($schedule->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->status === 'completed' ? 'success' : ($schedule->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $schedule->estimated_duration }} min</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule({{ $schedule->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No maintenance schedules found. Click "Schedule Maintenance" to add one.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Maintenance Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Maintenance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleForm" method="POST" action="{{ route('maintenance.schedule.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vehicle <span class="text-danger">*</span></label>
                                <select class="form-select" name="vehicle_id" required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->vehicle_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="maintenance_type" required>
                                    <option value="">Select Type</option>
                                    <option value="routine">Routine</option>
                                    <option value="preventive">Preventive</option>
                                    <option value="corrective">Corrective</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="oil_change">Oil Change</option>
                                    <option value="tire_rotation">Tire Rotation</option>
                                    <option value="brake_service">Brake Service</option>
                                    <option value="engine_service">Engine Service</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="scheduled_date" 
                                       value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimated Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="estimated_duration" 
                                       min="15" step="15" value="60" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Technician</label>
                                <select class="form-select" name="technician_id">
                                    <option value="">Assign Later</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" 
                                  placeholder="Enter maintenance details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Maintenance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Simple form submission
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    fetch('{{ route("maintenance.schedule.store") }}', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Maintenance scheduled successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to schedule maintenance'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error scheduling maintenance');
    });
});

function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this scheduled maintenance?')) {
        fetch(`/maintenance/schedule/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Schedule deleted successfully');
                location.reload();
            } else {
                alert('Error deleting schedule');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting schedule');
        });
    }
}
</script>
@endsection
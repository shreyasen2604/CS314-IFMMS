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

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                        <i class="fas fa-plus"></i> Schedule Maintenance
                    </button>
                    <button class="btn btn-outline-secondary" id="bulkScheduleBtn">
                        <i class="fas fa-calendar-plus"></i> Bulk Schedule
                    </button>
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="calendarView">
                            <i class="fas fa-calendar"></i> Calendar
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="listView">
                            <i class="fas fa-list"></i> List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="row" id="calendarContainer">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div class="row" id="listContainer" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Scheduled Maintenance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="scheduleTable">
                            <thead>
                                <tr>
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
                                            <span class="badge bg-{{ $schedule->status === 'completed' ? 'success' : ($schedule->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($schedule->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $schedule->estimated_duration }} min</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="editSchedule({{ $schedule->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteSchedule({{ $schedule->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
            <form id="scheduleForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vehicle</label>
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
                                <label class="form-label">Maintenance Type</label>
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
                                <label class="form-label">Scheduled Date</label>
                                <input type="datetime-local" class="form-control" name="scheduled_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimated Duration (minutes)</label>
                                <input type="number" class="form-control" name="estimated_duration" min="15" step="15" value="60" required>
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
                                <label class="form-label">Priority</label>
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
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="recurring" id="recurringCheck">
                                <label class="form-check-label" for="recurringCheck">
                                    Recurring Maintenance
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="recurringOptions" style="display: none;">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Recurring Type</label>
                                    <select class="form-select" name="recurring_type">
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Interval</label>
                                    <input type="number" class="form-control" name="recurring_interval" min="1" value="1">
                                </div>
                            </div>
                        </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            @foreach($schedules as $schedule)
            {
                id: '{{ $schedule->id }}',
                title: '{{ $schedule->vehicle->vehicle_number }} - {{ ucfirst(str_replace("_", " ", $schedule->maintenance_type)) }}',
                start: '{{ $schedule->scheduled_date->toISOString() }}',
                end: '{{ $schedule->scheduled_date->addMinutes($schedule->estimated_duration)->toISOString() }}',
                backgroundColor: '{{ $schedule->priority === "critical" ? "#dc3545" : ($schedule->priority === "high" ? "#ffc107" : "#007bff") }}',
                borderColor: '{{ $schedule->priority === "critical" ? "#dc3545" : ($schedule->priority === "high" ? "#ffc107" : "#007bff") }}'
            },
            @endforeach
        ],
        eventClick: function(info) {
            // Handle event click
            editSchedule(info.event.id);
        },
        editable: true,
        droppable: true,
        eventDrop: function(info) {
            // Handle event drag and drop
            updateScheduleDate(info.event.id, info.event.start);
        }
    });
    
    calendar.render();
    
    // View toggle
    document.getElementById('calendarView').addEventListener('click', function() {
        document.getElementById('calendarContainer').style.display = 'block';
        document.getElementById('listContainer').style.display = 'none';
        this.classList.add('active');
        document.getElementById('listView').classList.remove('active');
    });
    
    document.getElementById('listView').addEventListener('click', function() {
        document.getElementById('calendarContainer').style.display = 'none';
        document.getElementById('listContainer').style.display = 'block';
        this.classList.add('active');
        document.getElementById('calendarView').classList.remove('active');
    });
    
    // Recurring checkbox toggle
    document.getElementById('recurringCheck').addEventListener('change', function() {
        const recurringOptions = document.getElementById('recurringOptions');
        recurringOptions.style.display = this.checked ? 'block' : 'none';
    });
    
    // Schedule form submission
    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const data = {};
        formData.forEach((value, key) => {
            if (key === 'recurring') {
                data[key] = document.getElementById('recurringCheck').checked;
            } else {
                data[key] = value;
            }
        });
        
        // Send as JSON
        fetch('/maintenance/schedule', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleModal'));
                modal.hide();
                
                // Show success message
                alert('Maintenance scheduled successfully!');
                
                // Reload page to show new schedule
                location.reload();
            } else {
                alert(data.message || 'Error scheduling maintenance');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error scheduling maintenance. Please check all required fields.');
        });
    });
});

function editSchedule(id) {
    // Implement edit functionality
    console.log('Edit schedule:', id);
}

function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this scheduled maintenance?')) {
        fetch(`/maintenance/schedule/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting schedule');
            }
        });
    }
}

function updateScheduleDate(id, newDate) {
    fetch(`/maintenance/schedule/${id}/update-date`, {
        method: 'POST',
        body: JSON.stringify({
            scheduled_date: newDate.toISOString()
        }),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Error updating schedule date');
            location.reload();
        }
    });
}
</script>
@endsection
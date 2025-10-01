@extends('layouts.app')

@section('title', 'Vehicle Assignments - IFMMS-ZAR')
@section('page-title', 'Vehicle Assignments')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-3 me-3" style="background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%) !important;">
                        <i class="fas fa-user-tag text-white fs-4"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-1 fw-bold">Vehicle Assignments</h1>
                        <p class="text-muted mb-0">Assign drivers to vehicles and manage fleet assignments</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('maintenance.vehicles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Vehicles
                    </a>
                    <button onclick="exportAssignments()" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Export Assignments
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Vehicles</p>
                            <h3 class="fw-bold mb-0">{{ $vehicles->count() }}</h3>
                        </div>
                        <div class="bg-primary rounded-circle p-3" style="background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%) !important;">
                            <i class="fas fa-car text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Assigned</p>
                            <h3 class="fw-bold mb-0 text-success">{{ $vehicles->whereNotNull('driver_id')->count() }}</h3>
                        </div>
                        <div class="bg-success rounded-circle p-3">
                            <i class="fas fa-user-check text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Unassigned</p>
                            <h3 class="fw-bold mb-0 text-warning">{{ $vehicles->whereNull('driver_id')->count() }}</h3>
                        </div>
                        <div class="bg-warning rounded-circle p-3">
                            <i class="fas fa-user-times text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Available Drivers</p>
                            <h3 class="fw-bold mb-0 text-info">{{ $users->count() }}</h3>
                        </div>
                        <div class="bg-info rounded-circle p-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Form -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Vehicle Assignment Management
                </h5>
                <div class="d-flex gap-2">
                    <button onclick="bulkAssign()" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-users me-1"></i>Bulk Assign
                    </button>
                    <button onclick="clearAllAssignments()" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-user-times me-1"></i>Clear All
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('maintenance.vehicles.assignments.update') }}" method="POST" id="assignmentForm">
                @csrf
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Vehicle</th>
                                <th>Details</th>
                                <th>Current Status</th>
                                <th>Current Assignment</th>
                                <th>Assign Driver</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input vehicle-checkbox" value="{{ $vehicle->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle p-2 me-3" style="background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%) !important;">
                                                <i class="fas fa-car text-white small"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $vehicle->vehicle_number }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $vehicle->license_plate }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                                            <small class="text-muted">{{ $vehicle->year }} • {{ ucfirst($vehicle->fuel_type) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($vehicle->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($vehicle->status === 'maintenance')
                                            <span class="badge bg-warning">Maintenance</span>
                                        @elseif($vehicle->status === 'retired')
                                            <span class="badge bg-danger">Retired</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($vehicle->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vehicle->assignedUser)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle p-1 me-2">
                                                    <i class="fas fa-user text-white small"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $vehicle->assignedUser->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $vehicle->assignedUser->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center text-muted">
                                                <div class="bg-secondary rounded-circle p-1 me-2">
                                                    <i class="fas fa-user-slash text-white small"></i>
                                                </div>
                                                <em>Unassigned</em>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="assignments[{{ $vehicle->id }}]" class="form-select form-select-sm assignment-select" data-vehicle-id="{{ $vehicle->id }}">
                                            <option value="">-- Select Driver --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" 
                                                        {{ $vehicle->driver_id == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" onclick="viewVehicle({{ $vehicle->id }})" class="btn btn-outline-primary" title="View Vehicle">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" onclick="clearAssignment({{ $vehicle->id }})" class="btn btn-outline-warning" title="Clear Assignment">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-car fa-3x mb-3"></i>
                                        <p>No vehicles found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($vehicles->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sendNotifications">
                            <label class="form-check-label" for="sendNotifications">
                                Send notification emails to assigned drivers
                            </label>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="resetForm()" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i>Reset Changes
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Assignments
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Bulk Assignment Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkAssignModalLabel">
                    <i class="fas fa-users me-2"></i>Bulk Assignment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Driver</label>
                    <select class="form-select" id="bulkDriverSelect">
                        <option value="">-- Select Driver --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Apply to:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bulkTarget" id="selectedVehicles" value="selected" checked>
                        <label class="form-check-label" for="selectedVehicles">
                            Selected vehicles only (<span id="selectedCount">0</span> selected)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bulkTarget" id="unassignedVehicles" value="unassigned">
                        <label class="form-check-label" for="unassignedVehicles">
                            All unassigned vehicles ({{ $vehicles->whereNull('driver_id')->count() }} vehicles)
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="applyBulkAssignment()" class="btn btn-primary">Apply Assignment</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Select All functionality
    $('#selectAll').change(function() {
        $('.vehicle-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });

    $('.vehicle-checkbox').change(function() {
        updateSelectedCount();
        const totalCheckboxes = $('.vehicle-checkbox').length;
        const checkedCheckboxes = $('.vehicle-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    function updateSelectedCount() {
        const count = $('.vehicle-checkbox:checked').length;
        $('#selectedCount').text(count);
    }

    // Clear individual assignment
    function clearAssignment(vehicleId) {
        $(`select[name="assignments[${vehicleId}]"]`).val('');
    }

    // Clear all assignments
    function clearAllAssignments() {
        if (confirm('Are you sure you want to clear all vehicle assignments?')) {
            $('.assignment-select').val('');
        }
    }

    // Reset form
    function resetForm() {
        if (confirm('Are you sure you want to reset all changes?')) {
            location.reload();
        }
    }

    // View vehicle
    function viewVehicle(vehicleId) {
        window.location.href = `/maintenance/vehicles/${vehicleId}`;
    }

    // Bulk assignment
    function bulkAssign() {
        updateSelectedCount();
        const modal = new bootstrap.Modal(document.getElementById('bulkAssignModal'));
        modal.show();
    }

    function applyBulkAssignment() {
        const driverId = $('#bulkDriverSelect').val();
        const target = $('input[name="bulkTarget"]:checked').val();

        if (!driverId) {
            alert('Please select a driver');
            return;
        }

        if (target === 'selected') {
            $('.vehicle-checkbox:checked').each(function() {
                const vehicleId = $(this).val();
                $(`select[name="assignments[${vehicleId}]"]`).val(driverId);
            });
        } else if (target === 'unassigned') {
            $('.assignment-select').each(function() {
                if ($(this).val() === '') {
                    $(this).val(driverId);
                }
            });
        }

        bootstrap.Modal.getInstance(document.getElementById('bulkAssignModal')).hide();
        showAlert('Bulk assignment applied. Click "Update Assignments" to save changes.', 'info');
    }

    // Export assignments
    function exportAssignments() {
        window.location.href = '/maintenance/vehicles/assignments/export';
    }

    // Show alert
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : (type === 'info' ? 'alert-info' : 'alert-danger');
        const alertDiv = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
    }

    // Initialize
    $(document).ready(function() {
        updateSelectedCount();
        
        // Highlight changed assignments
        $('.assignment-select').change(function() {
            $(this).addClass('border-warning');
        });
    });
</script>
@endpush
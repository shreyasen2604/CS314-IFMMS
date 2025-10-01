@extends('layouts.app')

@section('title', 'Maintenance Records')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Maintenance Records</h1>
                <p class="page-subtitle">Complete maintenance history and cost tracking</p>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Vehicle</label>
                            <select class="form-select" name="vehicle_id">
                                <option value="">All Vehicles</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vehicle_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Maintenance Type</label>
                            <select class="form-select" name="maintenance_type">
                                <option value="">All Types</option>
                                <option value="routine" {{ request('maintenance_type') == 'routine' ? 'selected' : '' }}>Routine</option>
                                <option value="preventive" {{ request('maintenance_type') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                                <option value="corrective" {{ request('maintenance_type') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                                <option value="emergency" {{ request('maintenance_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                <option value="oil_change" {{ request('maintenance_type') == 'oil_change' ? 'selected' : '' }}>Oil Change</option>
                                <option value="tire_rotation" {{ request('maintenance_type') == 'tire_rotation' ? 'selected' : '' }}>Tire Rotation</option>
                                <option value="brake_service" {{ request('maintenance_type') == 'brake_service' ? 'selected' : '' }}>Brake Service</option>
                                <option value="engine_service" {{ request('maintenance_type') == 'engine_service' ? 'selected' : '' }}>Engine Service</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
                                <button class="btn btn-success" onclick="exportRecords()">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                                <button class="btn btn-info" onclick="exportPDF()">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                                @endif
                                <a href="{{ route('maintenance.records') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="alert alert-info mb-0 d-inline-block">
                                <strong>Total Cost: ${{ number_format($totalCost, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Records Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Maintenance Records ({{ $records->total() }} records)</h5>
                </div>
                <div class="card-body">
                    @if($records->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="recordsTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Technician</th>
                                        <th>Parts Cost</th>
                                        <th>Labor Cost</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
                                        <th>Mileage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $record)
                                        <tr>
                                            <td>{{ $record->service_date->format('M d, Y') }}</td>
                                            <td>
                                                <strong>{{ $record->vehicle->vehicle_number }}</strong><br>
                                                <small class="text-muted">{{ $record->vehicle->make }} {{ $record->vehicle->model }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $record->maintenance_type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span data-bs-toggle="tooltip" title="{{ $record->description }}">
                                                    {{ Str::limit($record->description, 40) }}
                                                </span>
                                            </td>
                                            <td>{{ $record->technician->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($record->parts_cost, 2) }}</td>
                                            <td>${{ number_format($record->labor_cost, 2) }}</td>
                                            <td>
                                                <strong>${{ number_format($record->total_cost, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $record->status === 'completed' ? 'success' : ($record->status === 'in_progress' ? 'warning' : ($record->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($record->mileage_at_service) }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="viewRecord({{ $record->id }})" data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($record->status !== 'completed')
                                                        <button class="btn btn-outline-warning" onclick="editRecord({{ $record->id }})" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-outline-info" onclick="printRecord({{ $record->id }})" data-bs-toggle="tooltip" title="Print">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $records->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No maintenance records found</h5>
                            <p class="text-muted">Try adjusting your filters or check back later.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    @if($records->count() > 0)
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $records->where('status', 'completed')->count() }}</h4>
                            <p class="mb-0">Completed Services</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="mb-0">{{ $records->where('status', 'in_progress')->count() }}</h4>
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
                            <h4 class="mb-0">${{ number_format($records->avg('cost'), 2) }}</h4>
                            <p class="mb-0">Average Cost</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calculator fa-2x"></i>
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
                            <h4 class="mb-0">{{ $records->where('maintenance_type', 'preventive')->count() }}</h4>
                            <p class="mb-0">Preventive Services</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Record Details Modal -->
<div class="modal fade" id="recordModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Maintenance Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="recordDetails">
                <!-- Record details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printCurrentRecord()">Print</button>
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
});

function viewRecord(id) {
    fetch(`/maintenance/records/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('recordDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Vehicle Information</h6>
                        <table class="table table-borderless table-sm">
                            <tr><td><strong>Vehicle:</strong></td><td>${data.vehicle.vehicle_number}</td></tr>
                            <tr><td><strong>Make/Model:</strong></td><td>${data.vehicle.make} ${data.vehicle.model}</td></tr>
                            <tr><td><strong>Mileage:</strong></td><td>${data.mileage_at_service.toLocaleString()}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Service Information</h6>
                        <table class="table table-borderless table-sm">
                            <tr><td><strong>Type:</strong></td><td>${data.maintenance_type.replace('_', ' ')}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>${new Date(data.service_date).toLocaleDateString()}</td></tr>
                            <tr><td><strong>Technician:</strong></td><td>${data.technician ? data.technician.name : 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p>${data.description}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h6>Parts Cost</h6>
                        <h4 class="text-info">$${data.parts_cost}</h4>
                    </div>
                    <div class="col-md-4">
                        <h6>Labor Cost</h6>
                        <h4 class="text-warning">$${data.labor_cost}</h4>
                    </div>
                    <div class="col-md-4">
                        <h6>Total Cost</h6>
                        <h4 class="text-success">${data.total_cost || data.cost}</h4>
                    </div>
                </div>
                ${data.notes ? `<div class="row"><div class="col-12"><h6>Notes</h6><p>${data.notes}</p></div></div>` : ''}
            `;
            new bootstrap.Modal(document.getElementById('recordModal')).show();
        });
}

function editRecord(id) {
    // Implement edit functionality
    window.location.href = `/maintenance/records/${id}/edit`;
}

function printRecord(id) {
    window.open(`/maintenance/records/${id}/print`, '_blank');
}

function printCurrentRecord() {
    window.print();
}

function exportRecords() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = `/maintenance/records/export?${params.toString()}`;
}

function exportPDF() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'pdf');
    window.location.href = `/maintenance/records/export?${params.toString()}`;
}
</script>
@endsection
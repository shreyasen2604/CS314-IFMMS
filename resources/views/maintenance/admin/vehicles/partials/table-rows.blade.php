@forelse($vehicles as $vehicle)
<tr>
    <td class="column-select d-none">
        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $vehicle->id }}">
    </td>
    <td class="column-id">
        {{ $vehicle->id }}
    </td>
    <td class="column-vehicle_number fw-medium">
        <a href="{{ route('maintenance.vehicle.profile', ['id' => $vehicle->id]) }}" class="text-primary text-decoration-none">
            {{ $vehicle->vehicle_number }}
        </a>
    </td>
    <td class="column-details">
        <div>
            <div class="fw-medium">{{ $vehicle->make }} {{ $vehicle->model }}</div>
            <div class="text-muted small">{{ $vehicle->year }}</div>
        </div>
    </td>
    <td class="column-vin text-muted">
        <code class="small">{{ $vehicle->vin }}</code>
    </td>
    <td class="column-license_plate">
        <span class="badge bg-light text-dark">{{ $vehicle->license_plate }}</span>
    </td>
    <td class="column-driver">
        @if($vehicle->driver)
            <div class="d-flex align-items-center">
                <i class="fas fa-user-circle text-muted me-2"></i>
                {{ $vehicle->driver->name }}
            </div>
        @else
            <span class="text-muted fst-italic">Unassigned</span>
        @endif
    </td>
    <td class="column-status">
        @if($vehicle->status == 'active')
            <span class="badge bg-success">Active</span>
        @elseif($vehicle->status == 'maintenance')
            <span class="badge bg-warning">Maintenance</span>
        @elseif($vehicle->status == 'retired')
            <span class="badge bg-danger">Retired</span>
        @else
            <span class="badge bg-secondary">{{ ucfirst($vehicle->status) }}</span>
        @endif
    </td>
    <td class="column-fuel_type">
        <div class="d-flex align-items-center">
            @if($vehicle->fuel_type == 'electric')
                <i class="fas fa-bolt text-success me-1"></i>
            @elseif($vehicle->fuel_type == 'diesel')
                <i class="fas fa-gas-pump text-secondary me-1"></i>
            @elseif($vehicle->fuel_type == 'hybrid')
                <i class="fas fa-leaf text-success me-1"></i>
            @else
                <i class="fas fa-gas-pump text-primary me-1"></i>
            @endif
            {{ ucfirst($vehicle->fuel_type) }}
        </div>
    </td>
    <td class="column-mileage">
        {{ number_format($vehicle->mileage) }} km
    </td>
    <td class="column-health_score">
        <div class="d-flex align-items-center">
            <div class="progress me-2" style="width: 60px; height: 8px;">
                <div class="progress-bar {{ $vehicle->health_score >= 80 ? 'bg-success' : ($vehicle->health_score >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                     style="width: {{ $vehicle->health_score }}%"></div>
            </div>
            <span class="small fw-medium">{{ $vehicle->health_score }}%</span>
        </div>
    </td>
    <td class="column-last_maintenance">
        {{ $vehicle->last_maintenance_date }}
    </td>
    <td class="column-next_maintenance">
        <span class="text-primary fw-medium">{{ $vehicle->next_maintenance_due }}</span>
    </td>
    <td class="column-location">
        <div class="d-flex align-items-center">
            <i class="fas fa-map-marker-alt text-muted me-1"></i>
            {{ $vehicle->location ?? 'Unknown' }}
        </div>
    </td>
    <td class="column-department">
        <span class="badge bg-info">{{ $vehicle->department ?? 'N/A' }}</span>
    </td>
    <td class="column-actions text-end">
        <div class="btn-group btn-group-sm" role="group">
            <button onclick="viewVehicle({{ $vehicle->id }})" class="btn btn-outline-primary btn-sm" title="View">
                <i class="fas fa-eye"></i>
            </button>
            <a href="{{ route('maintenance.vehicles.edit', $vehicle->id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button onclick="showMaintenanceModal({{ $vehicle->id }})" class="btn btn-outline-success btn-sm" title="Schedule Maintenance">
                <i class="fas fa-wrench"></i>
            </button>
            <button onclick="deleteVehicle({{ $vehicle->id }})" class="btn btn-outline-danger btn-sm" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="16" class="text-center py-5 text-muted">
        <i class="fas fa-car fa-3x mb-3"></i>
        <p>No vehicles found matching your criteria</p>
    </td>
</tr>
@endforelse
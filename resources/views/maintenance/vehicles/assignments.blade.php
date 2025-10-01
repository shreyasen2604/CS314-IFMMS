@extends('layouts.app')

@section('title', 'Vehicle Assignments - IFMMS-ZAR')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h2 text-white mb-0">Vehicle Assignments</h1>
                <p class="text-white-50 mb-0">Manage driver-vehicle assignments and view assignment history</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-light" onclick="showColumnSettings()">
                    <i class="fas fa-columns me-2"></i>Customize Columns
                </button>
                <button class="btn btn-success" onclick="exportAssignments()">
                    <i class="fas fa-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search vehicles, drivers...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="assigned">Assigned</option>
                        <option value="unassigned">Unassigned</option>
                        <option value="maintenance">In Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Vehicle Type</label>
                    <select id="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        <option value="truck">Truck</option>
                        <option value="van">Van</option>
                        <option value="sedan">Sedan</option>
                        <option value="suv">SUV</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Department</label>
                    <select id="deptFilter" class="form-select">
                        <option value="">All Departments</option>
                        <option value="logistics">Logistics</option>
                        <option value="delivery">Delivery</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="admin">Administration</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary me-2" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary" onclick="resetFilters()">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="assignmentsTable">
                    <thead>
                        <tr>
                            <th data-column="select" class="column-select">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th data-column="vehicle_id" class="column-vehicle_id sortable">
                                Vehicle ID <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th data-column="vehicle_info" class="column-vehicle_info">
                                Vehicle Info
                            </th>
                            <th data-column="driver" class="column-driver sortable">
                                Assigned Driver <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th data-column="department" class="column-department">
                                Department
                            </th>
                            <th data-column="status" class="column-status">
                                Status
                            </th>
                            <th data-column="last_service" class="column-last_service sortable">
                                Last Service <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th data-column="next_service" class="column-next_service sortable">
                                Next Service <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th data-column="mileage" class="column-mileage sortable">
                                Mileage <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th data-column="fuel_type" class="column-fuel_type">
                                Fuel Type
                            </th>
                            <th data-column="location" class="column-location">
                                Current Location
                            </th>
                            <th data-column="assignment_date" class="column-assignment_date sortable">
                                Assignment Date <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th data-column="actions" class="column-actions">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $assignments = [
                                [
                                    'id' => 1,
                                    'vehicle_id' => 'TRK-001',
                                    'make' => 'Ford',
                                    'model' => 'F-150',
                                    'year' => 2022,
                                    'driver' => 'John Smith',
                                    'driver_id' => 'DRV-101',
                                    'department' => 'Logistics',
                                    'status' => 'assigned',
                                    'last_service' => '2024-01-15',
                                    'next_service' => '2024-04-15',
                                    'mileage' => 45230,
                                    'fuel_type' => 'Diesel',
                                    'location' => 'Depot A',
                                    'assignment_date' => '2023-06-01'
                                ],
                                [
                                    'id' => 2,
                                    'vehicle_id' => 'VAN-002',
                                    'make' => 'Mercedes',
                                    'model' => 'Sprinter',
                                    'year' => 2023,
                                    'driver' => 'Sarah Johnson',
                                    'driver_id' => 'DRV-102',
                                    'department' => 'Delivery',
                                    'status' => 'assigned',
                                    'last_service' => '2024-02-01',
                                    'next_service' => '2024-05-01',
                                    'mileage' => 28150,
                                    'fuel_type' => 'Diesel',
                                    'location' => 'Route B-12',
                                    'assignment_date' => '2023-08-15'
                                ],
                                [
                                    'id' => 3,
                                    'vehicle_id' => 'SED-003',
                                    'make' => 'Toyota',
                                    'model' => 'Camry',
                                    'year' => 2023,
                                    'driver' => null,
                                    'driver_id' => null,
                                    'department' => 'Admin',
                                    'status' => 'unassigned',
                                    'last_service' => '2024-01-20',
                                    'next_service' => '2024-04-20',
                                    'mileage' => 12500,
                                    'fuel_type' => 'Petrol',
                                    'location' => 'Parking Lot C',
                                    'assignment_date' => null
                                ],
                            ];
                        @endphp

                        @foreach($assignments as $assignment)
                        <tr>
                            <td class="column-select">
                                <input type="checkbox" class="form-check-input row-select" value="{{ $assignment['id'] }}">
                            </td>
                            <td class="column-vehicle_id">
                                <strong>{{ $assignment['vehicle_id'] }}</strong>
                            </td>
                            <td class="column-vehicle_info">
                                <div>
                                    <strong>{{ $assignment['make'] }} {{ $assignment['model'] }}</strong>
                                    <br>
                                    <small class="text-muted">Year: {{ $assignment['year'] }}</small>
                                </div>
                            </td>
                            <td class="column-driver">
                                @if($assignment['driver'])
                                    <div>
                                        <i class="fas fa-user-circle me-1"></i>
                                        {{ $assignment['driver'] }}
                                        <br>
                                        <small class="text-muted">{{ $assignment['driver_id'] }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td class="column-department">
                                <span class="badge bg-info">{{ $assignment['department'] }}</span>
                            </td>
                            <td class="column-status">
                                @if($assignment['status'] == 'assigned')
                                    <span class="badge bg-success">Assigned</span>
                                @elseif($assignment['status'] == 'unassigned')
                                    <span class="badge bg-warning">Unassigned</span>
                                @else
                                    <span class="badge bg-danger">In Maintenance</span>
                                @endif
                            </td>
                            <td class="column-last_service">
                                {{ $assignment['last_service'] }}
                            </td>
                            <td class="column-next_service">
                                <span class="text-primary">{{ $assignment['next_service'] }}</span>
                            </td>
                            <td class="column-mileage">
                                {{ number_format($assignment['mileage']) }} km
                            </td>
                            <td class="column-fuel_type">
                                {{ $assignment['fuel_type'] }}
                            </td>
                            <td class="column-location">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $assignment['location'] }}
                            </td>
                            <td class="column-assignment_date">
                                {{ $assignment['assignment_date'] ?: '-' }}
                            </td>
                            <td class="column-actions">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editAssignment({{ $assignment['id'] }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewHistory({{ $assignment['id'] }})" title="History">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    @if(!$assignment['driver'])
                                    <button class="btn btn-sm btn-outline-success" onclick="assignDriver({{ $assignment['id'] }})" title="Assign">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                    @endif
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

<!-- Column Settings Modal -->
<div class="modal fade" id="columnSettingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customize Table Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Select which columns to display in the table:</p>
                <div class="column-list">
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_vehicle_id" data-column="vehicle_id" checked>
                        <label class="form-check-label" for="col_vehicle_id">Vehicle ID</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_vehicle_info" data-column="vehicle_info" checked>
                        <label class="form-check-label" for="col_vehicle_info">Vehicle Info</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_driver" data-column="driver" checked>
                        <label class="form-check-label" for="col_driver">Assigned Driver</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_department" data-column="department" checked>
                        <label class="form-check-label" for="col_department">Department</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_status" data-column="status" checked>
                        <label class="form-check-label" for="col_status">Status</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_last_service" data-column="last_service" checked>
                        <label class="form-check-label" for="col_last_service">Last Service</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_next_service" data-column="next_service" checked>
                        <label class="form-check-label" for="col_next_service">Next Service</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_mileage" data-column="mileage" checked>
                        <label class="form-check-label" for="col_mileage">Mileage</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_fuel_type" data-column="fuel_type">
                        <label class="form-check-label" for="col_fuel_type">Fuel Type</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_location" data-column="location">
                        <label class="form-check-label" for="col_location">Current Location</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input column-toggle" type="checkbox" id="col_assignment_date" data-column="assignment_date">
                        <label class="form-check-label" for="col_assignment_date">Assignment Date</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyColumnSettings()">Apply Changes</button>
                <button type="button" class="btn btn-success" onclick="saveColumnPreferences()">Save as Default</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Driver Modal -->
<div class="modal fade" id="assignDriverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Driver to Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignDriverForm">
                    <div class="mb-3">
                        <label class="form-label">Select Driver</label>
                        <select class="form-select" id="driverSelect" required>
                            <option value="">Choose a driver...</option>
                            <option value="1">John Doe (DRV-201)</option>
                            <option value="2">Jane Smith (DRV-202)</option>
                            <option value="3">Mike Johnson (DRV-203)</option>
                            <option value="4">Emily Brown (DRV-204)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assignment Date</label>
                        <input type="date" class="form-control" id="assignmentDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="assignmentNotes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmAssignment()">Assign Driver</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    .sortable {
        cursor: pointer;
        user-select: none;
    }
    .sortable:hover {
        background-color: rgba(0,0,0,0.05);
    }
    .column-list {
        max-height: 400px;
        overflow-y: auto;
    }
    /* Hide columns based on saved preferences */
    .column-hidden {
        display: none !important;
    }
</style>

<script>
    // Load saved column preferences on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadColumnPreferences();
        initializeDataTable();
    });

    // Column visibility management
    function showColumnSettings() {
        const modal = new bootstrap.Modal(document.getElementById('columnSettingsModal'));
        modal.show();
    }

    function applyColumnSettings() {
        const toggles = document.querySelectorAll('.column-toggle');
        toggles.forEach(toggle => {
            const columnName = toggle.dataset.column;
            const columns = document.querySelectorAll(`.column-${columnName}`);
            
            if (toggle.checked) {
                columns.forEach(col => col.classList.remove('column-hidden'));
            } else {
                columns.forEach(col => col.classList.add('column-hidden'));
            }
        });
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('columnSettingsModal')).hide();
    }

    function saveColumnPreferences() {
        const preferences = {};
        const toggles = document.querySelectorAll('.column-toggle');
        
        toggles.forEach(toggle => {
            preferences[toggle.dataset.column] = toggle.checked;
        });
        
        // Save to localStorage for persistence
        localStorage.setItem('vehicleTableColumns', JSON.stringify(preferences));
        
        // Also save to server for user-specific preferences
        fetch('{{ route("maintenance.vehicles.columns.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ columns: preferences })
        }).then(response => {
            if (response.ok) {
                toastr.success('Column preferences saved successfully!');
                applyColumnSettings();
            }
        });
    }

    function loadColumnPreferences() {
        const saved = localStorage.getItem('vehicleTableColumns');
        if (saved) {
            const preferences = JSON.parse(saved);
            Object.keys(preferences).forEach(column => {
                const toggle = document.querySelector(`#col_${column}`);
                if (toggle) {
                    toggle.checked = preferences[column];
                }
                
                const columns = document.querySelectorAll(`.column-${column}`);
                if (!preferences[column]) {
                    columns.forEach(col => col.classList.add('column-hidden'));
                }
            });
        }
    }

    // DataTable initialization
    function initializeDataTable() {
        // Add DataTable functionality if needed
        $('#assignmentsTable').on('click', '.sortable', function() {
            const column = $(this).data('column');
            sortTable(column);
        });
    }

    // Sorting functionality
    let sortOrder = {};
    function sortTable(column) {
        const table = document.getElementById('assignmentsTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Toggle sort order
        sortOrder[column] = sortOrder[column] === 'asc' ? 'desc' : 'asc';
        
        // Sort rows
        rows.sort((a, b) => {
            const aValue = a.querySelector(`.column-${column}`).textContent.trim();
            const bValue = b.querySelector(`.column-${column}`).textContent.trim();
            
            if (sortOrder[column] === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });
        
        // Re-append sorted rows
        rows.forEach(row => tbody.appendChild(row));
        
        // Update sort icons
        updateSortIcons(column);
    }

    function updateSortIcons(column) {
        // Reset all icons
        document.querySelectorAll('.sortable i').forEach(icon => {
            icon.className = 'fas fa-sort ms-1';
        });
        
        // Update active column icon
        const header = document.querySelector(`th[data-column="${column}"] i`);
        if (header) {
            header.className = sortOrder[column] === 'asc' ? 
                'fas fa-sort-up ms-1' : 'fas fa-sort-down ms-1';
        }
    }

    // Filter functionality
    function applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const deptFilter = document.getElementById('deptFilter').value;
        
        const rows = document.querySelectorAll('#assignmentsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.querySelector('.column-status .badge').textContent.toLowerCase();
            const dept = row.querySelector('.column-department .badge').textContent.toLowerCase();
            
            let show = true;
            
            if (searchTerm && !text.includes(searchTerm)) show = false;
            if (statusFilter && !status.includes(statusFilter)) show = false;
            if (deptFilter && !dept.includes(deptFilter)) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('typeFilter').value = '';
        document.getElementById('deptFilter').value = '';
        applyFilters();
    }

    // Assignment functions
    function assignDriver(vehicleId) {
        document.getElementById('assignmentDate').value = new Date().toISOString().split('T')[0];
        const modal = new bootstrap.Modal(document.getElementById('assignDriverModal'));
        modal.show();
        
        // Store vehicle ID for later use
        document.getElementById('assignDriverModal').dataset.vehicleId = vehicleId;
    }

    function confirmAssignment() {
        const vehicleId = document.getElementById('assignDriverModal').dataset.vehicleId;
        const driverId = document.getElementById('driverSelect').value;
        const date = document.getElementById('assignmentDate').value;
        const notes = document.getElementById('assignmentNotes').value;
        
        if (!driverId || !date) {
            alert('Please select a driver and assignment date');
            return;
        }
        
        // Here you would make an API call to save the assignment
        console.log('Assigning driver', driverId, 'to vehicle', vehicleId);
        
        // Close modal and refresh table
        bootstrap.Modal.getInstance(document.getElementById('assignDriverModal')).hide();
        location.reload(); // In production, update the table dynamically
    }

    function editAssignment(id) {
        // Implement edit functionality
        console.log('Edit assignment', id);
    }

    function viewHistory(id) {
        // Implement history view
        console.log('View history for', id);
    }

    function exportAssignments() {
        // Implement export functionality
        window.location.href = '{{ route("maintenance.vehicles.reports") }}?export=csv';
    }

    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-select');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Real-time search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        applyFilters();
    });
</script>
@endpush
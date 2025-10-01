@extends('layouts.app')

@section('title', 'Vehicle Management - IFMMS-ZAR')
@section('page-title', 'Vehicle Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-3 me-3" style="background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%) !important;">
                        <i class="fas fa-car text-white fs-4"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-1 fw-bold">Vehicle Fleet Management</h1>
                        <p class="text-muted mb-0">Manage your entire vehicle fleet with advanced controls</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="showColumnManager()" class="btn btn-secondary">
                        <i class="fas fa-columns me-2"></i>Manage Columns
                    </button>
                    <button onclick="showAdvancedFilters()" class="btn btn-outline-primary">
                        <i class="fas fa-filter me-2"></i>Advanced Filters
                    </button>
                    <button onclick="exportData()" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <a href="{{ route('maintenance.vehicles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Vehicle
                    </a>
                </div>
            </div>
        </div>
    </div>

        <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Vehicles</p>
                            <h3 class="fw-bold mb-0">{{ isset($vehicles) ? $vehicles->total() : 45 }}</h3>
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
                            <p class="text-muted small mb-1">Active</p>
                            <h3 class="fw-bold mb-0 text-success">38</h3>
                        </div>
                        <div class="bg-success rounded-circle p-3">
                            <i class="fas fa-check-circle text-white"></i>
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
                            <p class="text-muted small mb-1">In Maintenance</p>
                            <h3 class="fw-bold mb-0 text-warning">5</h3>
                        </div>
                        <div class="bg-warning rounded-circle p-3">
                            <i class="fas fa-wrench text-white"></i>
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
                            <p class="text-muted small mb-1">Retired</p>
                            <h3 class="fw-bold mb-0 text-danger">2</h3>
                        </div>
                        <div class="bg-danger rounded-circle p-3">
                            <i class="fas fa-times-circle text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Quick Filters Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="position-relative">
                        <input type="text" id="quickSearch" placeholder="Search vehicles, VIN, plate..." 
                               class="form-control ps-5">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    </div>
                </div>
                <div class="col-md-2">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="maintenance">In Maintenance</option>
                        <option value="retired">Retired</option>
                        <option value="out_of_service">Out of Service</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        <option value="truck">Truck</option>
                        <option value="van">Van</option>
                        <option value="sedan">Sedan</option>
                        <option value="suv">SUV</option>
                        <option value="bus">Bus</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="fuelFilter" class="form-select">
                        <option value="">All Fuel Types</option>
                        <option value="gasoline">Gasoline</option>
                        <option value="diesel">Diesel</option>
                        <option value="electric">Electric</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button onclick="applyQuickFilters()" class="btn btn-primary">
                            Apply
                        </button>
                        <button onclick="resetAllFilters()" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Main Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small">
                        Showing <strong>{{ isset($vehicles) ? ($vehicles->firstItem() ?? 0) : 1 }}</strong> to 
                        <strong>{{ isset($vehicles) ? ($vehicles->lastItem() ?? 0) : 3 }}</strong> of 
                        <strong>{{ isset($vehicles) ? ($vehicles->total() ?? 0) : 45 }}</strong> vehicles
                    </span>
                    <select id="perPage" onchange="changePerPage(this.value)" class="form-select form-select-sm" style="width: auto;">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="toggleBulkSelect()" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-check-square me-1"></i>Bulk Select
                    </button>
                    <button onclick="refreshTable()" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        @include('maintenance.admin.vehicles.partials.table')

        <!-- Pagination -->
        <div class="card-footer">
            @if(isset($vehicles) && method_exists($vehicles, 'links'))
                {{ $vehicles->links() }}
            @endif
        </div>
    </div>
</div>

@include('maintenance.admin.vehicles.partials.modals')

@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Column Management Functions
    function showColumnManager() {
        const modal = new bootstrap.Modal(document.getElementById('columnManagerModal'));
        modal.show();
    }

    function selectAllColumns() {
        $('.column-toggle').prop('checked', true);
    }

    function deselectAllColumns() {
        $('.column-toggle').prop('checked', false);
    }

    function resetToDefault() {
        // Reset to default column visibility
        const defaultColumns = ['id', 'vehicle_number', 'details', 'license_plate', 'driver', 'status', 'fuel_type', 'mileage', 'health_score', 'next_maintenance'];
        $('.column-toggle').each(function() {
            const column = $(this).data('column');
            $(this).prop('checked', defaultColumns.includes(column));
        });
    }

    function applyColumnSettings() {
        const visibleColumns = [];
        $('.column-toggle:checked').each(function() {
            visibleColumns.push($(this).data('column'));
        });

        // Hide/show table columns
        $('th, td').each(function() {
            const classes = $(this).attr('class') || '';
            const columnMatch = classes.match(/column-(\w+)/);
            if (columnMatch) {
                const columnName = columnMatch[1];
                if (visibleColumns.includes(columnName)) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            }
        });

        // Save preferences if checkbox is checked
        if ($('#saveAsDefault').is(':checked')) {
            localStorage.setItem('vehicleTableColumns', JSON.stringify(visibleColumns));
        }

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('columnManagerModal')).hide();
        showAlert('Column settings applied successfully', 'success');
    }

    // Advanced Filters Functions
    function showAdvancedFilters() {
        const modal = new bootstrap.Modal(document.getElementById('advancedFiltersModal'));
        modal.show();
    }

    function clearAdvancedFilters() {
        $('#advancedFiltersForm')[0].reset();
        $('#healthMinValue').text('0%');
        $('#healthMaxValue').text('100%');
        $('#filter-health-min').val(0);
        $('#filter-health-max').val(100);
    }

    function applyAdvancedFilters() {
        const filters = {
            make: $('#filter-make').val(),
            model: $('#filter-model').val(),
            year_from: $('#filter-year-from').val(),
            year_to: $('#filter-year-to').val(),
            mileage_min: $('#filter-mileage-min').val(),
            mileage_max: $('#filter-mileage-max').val(),
            health_min: $('#filter-health-min').val(),
            health_max: $('#filter-health-max').val(),
            last_service_from: $('#filter-last-service-from').val(),
            last_service_to: $('#filter-last-service-to').val(),
            service_due: $('#filter-service-due').val(),
            assignment: $('#filter-assignment').val(),
            department: $('#filter-department').val(),
            location: $('#filter-location').val(),
            vin: $('#filter-vin').val(),
            license_plate: $('#filter-license-plate').val(),
            insurance: $('#filter-insurance').val(),
            registration: $('#filter-registration').val(),
            critical_alerts: $('#filter-critical-alerts').is(':checked'),
            active_alerts: $('#filter-active-alerts').is(':checked'),
            no_alerts: $('#filter-no-alerts').is(':checked')
        };

        // Apply filters via AJAX
        $.get('/maintenance/vehicles/filter', filters, function(data) {
            $('#vehiclesTable tbody').html(data);
            bootstrap.Modal.getInstance(document.getElementById('advancedFiltersModal')).hide();
            showAlert('Advanced filters applied successfully', 'success');
        }).fail(function() {
            showAlert('Error applying filters', 'error');
        });
    }

    // Vehicle Management Functions
    function deleteVehicle(id) {
        if (confirm('Are you sure you want to delete this vehicle?')) {
            $.ajax({
                url: `/maintenance/vehicles/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $(`#vehicle-row-${id}`).remove();
                        showAlert('Vehicle deleted successfully', 'success');
                    }
                },
                error: function(xhr) {
                    showAlert('Error deleting vehicle', 'error');
                }
            });
        }
    }

    function editVehicle(id) {
        window.location.href = `/maintenance/vehicles/${id}/edit`;
    }

    function viewVehicle(id) {
        window.location.href = `/maintenance/vehicles/${id}`;
    }

    function showMaintenanceModal(id) {
        // Placeholder for maintenance scheduling modal
        showAlert('Maintenance scheduling feature coming soon', 'info');
    }

    // Search and Filter Functions
    $('#quickSearch').on('keyup', function() {
        let searchTerm = $(this).val();
        if (searchTerm.length > 2 || searchTerm.length === 0) {
            $.get(`/maintenance/vehicles/search?q=${searchTerm}`, function(data) {
                $('#vehiclesTable tbody').html(data);
            });
        }
    });

    function applyQuickFilters() {
        let status = $('#statusFilter').val();
        let type = $('#typeFilter').val();
        let fuel = $('#fuelFilter').val();

        $.get('/maintenance/vehicles/filter', {
            status: status,
            type: type,
            fuel: fuel
        }, function(data) {
            $('#vehiclesTable tbody').html(data);
        });
    }

    function resetAllFilters() {
        $('#quickSearch').val('');
        $('#statusFilter').val('');
        $('#typeFilter').val('');
        $('#fuelFilter').val('');
        applyQuickFilters();
    }

    function exportData() {
        window.location.href = `/maintenance/vehicles/export`;
    }

    // Utility Functions
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

    function refreshTable() {
        location.reload();
    }

    function changePerPage(value) {
        window.location.href = `${window.location.pathname}?per_page=${value}`;
    }

    function toggleBulkSelect() {
        $('.row-checkbox').prop('checked', 
            !$('.row-checkbox').first().prop('checked')
        );
    }

    // Initialize on document ready
    $(document).ready(function() {
        // Load saved column preferences
        const savedColumns = localStorage.getItem('vehicleTableColumns');
        if (savedColumns) {
            const visibleColumns = JSON.parse(savedColumns);
            $('.column-toggle').each(function() {
                const column = $(this).data('column');
                $(this).prop('checked', visibleColumns.includes(column));
            });
            applyColumnSettings();
        }

        // Initialize health score range sliders
        $('#filter-health-min, #filter-health-max').on('input', function() {
            $('#healthMinValue').text($('#filter-health-min').val() + '%');
            $('#healthMaxValue').text($('#filter-health-max').val() + '%');
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
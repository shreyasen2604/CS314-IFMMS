<!-- Column Manager Modal -->
<div class="modal fade" id="columnManagerModal" tabindex="-1" aria-labelledby="columnManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnManagerModalLabel">
                    <i class="fas fa-columns me-2"></i>Manage Table Columns
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="text-muted mb-0">Select columns to display in the table</p>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" onclick="selectAllColumns()" class="btn btn-outline-primary">
                                Select All
                            </button>
                            <button type="button" onclick="deselectAllColumns()" class="btn btn-outline-secondary">
                                Deselect All
                            </button>
                            <button type="button" onclick="resetToDefault()" class="btn btn-outline-warning">
                                Reset to Default
                            </button>
                        </div>
                    </div>
                    
                    <div class="row g-2" style="max-height: 400px; overflow-y: auto;">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="id" checked id="col-id">
                                <label class="form-check-label" for="col-id">ID</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="vehicle_number" checked id="col-vehicle_number">
                                <label class="form-check-label" for="col-vehicle_number">Vehicle Number</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="details" checked id="col-details">
                                <label class="form-check-label" for="col-details">Details</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="vin" id="col-vin">
                                <label class="form-check-label" for="col-vin">VIN</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="license_plate" checked id="col-license_plate">
                                <label class="form-check-label" for="col-license_plate">License Plate</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="driver" checked id="col-driver">
                                <label class="form-check-label" for="col-driver">Assigned Driver</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="status" checked id="col-status">
                                <label class="form-check-label" for="col-status">Status</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="fuel_type" checked id="col-fuel_type">
                                <label class="form-check-label" for="col-fuel_type">Fuel Type</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="mileage" checked id="col-mileage">
                                <label class="form-check-label" for="col-mileage">Mileage</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="health_score" checked id="col-health_score">
                                <label class="form-check-label" for="col-health_score">Health Score</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="last_maintenance" id="col-last_maintenance">
                                <label class="form-check-label" for="col-last_maintenance">Last Service</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="next_maintenance" checked id="col-next_maintenance">
                                <label class="form-check-label" for="col-next_maintenance">Next Service</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="location" id="col-location">
                                <label class="form-check-label" for="col-location">Location</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input column-toggle" type="checkbox" data-column="department" id="col-department">
                                <label class="form-check-label" for="col-department">Department</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-check me-auto">
                    <input class="form-check-input" type="checkbox" id="saveAsDefault">
                    <label class="form-check-label" for="saveAsDefault">
                        Save as my default view
                    </label>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="applyColumnSettings()" class="btn btn-primary">Apply Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Filters Modal -->
<div class="modal fade" id="advancedFiltersModal" tabindex="-1" aria-labelledby="advancedFiltersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advancedFiltersModalLabel">
                    <i class="fas fa-filter me-2"></i>Advanced Filters
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="advancedFiltersForm">
                    <div class="row g-4">
                        <!-- Vehicle Information -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-car me-2"></i>Vehicle Information
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Make</label>
                                <select class="form-select" id="filter-make">
                                    <option value="">All Makes</option>
                                    <option value="ford">Ford</option>
                                    <option value="toyota">Toyota</option>
                                    <option value="mercedes">Mercedes</option>
                                    <option value="volvo">Volvo</option>
                                    <option value="chevrolet">Chevrolet</option>
                                    <option value="nissan">Nissan</option>
                                    <option value="honda">Honda</option>
                                    <option value="bmw">BMW</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control" id="filter-model" placeholder="Enter model">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Year Range</label>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="number" class="form-control" id="filter-year-from" placeholder="From" min="1990" max="2024">
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="filter-year-to" placeholder="To" min="1990" max="2024">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mileage Range (km)</label>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="number" class="form-control" id="filter-mileage-min" placeholder="Min">
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="filter-mileage-max" placeholder="Max">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Maintenance & Health -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success border-bottom pb-2 mb-3">
                                <i class="fas fa-wrench me-2"></i>Maintenance & Health
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Health Score Range</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="range" class="form-range" min="0" max="100" value="0" id="filter-health-min">
                                    <span class="badge bg-secondary" id="healthMinValue">0%</span>
                                    <span>-</span>
                                    <input type="range" class="form-range" min="0" max="100" value="100" id="filter-health-max">
                                    <span class="badge bg-secondary" id="healthMaxValue">100%</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Last Service Date</label>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="date" class="form-control" id="filter-last-service-from">
                                    </div>
                                    <div class="col">
                                        <input type="date" class="form-control" id="filter-last-service-to">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Service Due</label>
                                <select class="form-select" id="filter-service-due">
                                    <option value="">Any</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="week">Within 1 Week</option>
                                    <option value="month">Within 1 Month</option>
                                    <option value="quarter">Within 3 Months</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Maintenance Alerts</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter-critical-alerts">
                                    <label class="form-check-label" for="filter-critical-alerts">
                                        Has Critical Alerts
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter-active-alerts">
                                    <label class="form-check-label" for="filter-active-alerts">
                                        Has Active Alerts
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter-no-alerts">
                                    <label class="form-check-label" for="filter-no-alerts">
                                        No Alerts
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Assignment & Location -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-info border-bottom pb-2 mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Assignment & Location
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Assignment Status</label>
                                <select class="form-select" id="filter-assignment">
                                    <option value="">Any</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="unassigned">Unassigned</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Department</label>
                                <select multiple class="form-select" id="filter-department" size="4">
                                    <option value="logistics">Logistics</option>
                                    <option value="delivery">Delivery</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="admin">Administration</option>
                                    <option value="sales">Sales</option>
                                </select>
                                <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Current Location</label>
                                <input type="text" class="form-control" id="filter-location" placeholder="Enter location">
                            </div>
                        </div>
                        
                        <!-- Additional Options -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-warning border-bottom pb-2 mb-3">
                                <i class="fas fa-cog me-2"></i>Additional Options
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label">VIN</label>
                                <input type="text" class="form-control" id="filter-vin" placeholder="Enter VIN">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">License Plate</label>
                                <input type="text" class="form-control" id="filter-license-plate" placeholder="Enter license plate">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Insurance Status</label>
                                <select class="form-select" id="filter-insurance">
                                    <option value="">Any</option>
                                    <option value="valid">Valid</option>
                                    <option value="expiring">Expiring Soon</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Registration Status</label>
                                <select class="form-select" id="filter-registration">
                                    <option value="">Any</option>
                                    <option value="valid">Valid</option>
                                    <option value="expiring">Expiring Soon</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="clearAdvancedFilters()" class="btn btn-outline-danger me-auto">
                    <i class="fas fa-trash me-2"></i>Clear All Filters
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="applyAdvancedFilters()" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>
<script>
// Column Management
let defaultColumns = ['id', 'vehicle_number', 'details', 'license_plate', 'driver', 'status', 'fuel_type', 'mileage', 'health_score', 'next_maintenance'];
let currentColumns = [...defaultColumns];

function showColumnManager() {
    document.getElementById('columnManagerModal').classList.remove('hidden');
}

function closeColumnManager() {
    document.getElementById('columnManagerModal').classList.add('hidden');
}

function selectAllColumns() {
    document.querySelectorAll('.column-toggle').forEach(cb => cb.checked = true);
}

function deselectAllColumns() {
    document.querySelectorAll('.column-toggle').forEach(cb => cb.checked = false);
}

function resetToDefault() {
    document.querySelectorAll('.column-toggle').forEach(cb => {
        cb.checked = defaultColumns.includes(cb.dataset.column);
    });
}

function applyColumnSettings() {
    const toggles = document.querySelectorAll('.column-toggle');
    const saveAsDefault = document.getElementById('saveAsDefault').checked;
    
    toggles.forEach(toggle => {
        const columnName = toggle.dataset.column;
        const columns = document.querySelectorAll(`.column-${columnName}`);
        
        if (toggle.checked) {
            columns.forEach(col => col.classList.remove('hidden'));
        } else {
            columns.forEach(col => col.classList.add('hidden'));
        }
    });
    
    if (saveAsDefault) {
        // Save to localStorage
        const selectedColumns = Array.from(toggles)
            .filter(t => t.checked)
            .map(t => t.dataset.column);
        localStorage.setItem('vehicleTableColumns', JSON.stringify(selectedColumns));
        
        // Save to server
        fetch('{{ route("maintenance.vehicles.columns.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ columns: selectedColumns })
        });
    }
    
    closeColumnManager();
}

// Advanced Filters
function showAdvancedFilters() {
    document.getElementById('advancedFiltersModal').classList.remove('hidden');
}

function closeAdvancedFilters() {
    document.getElementById('advancedFiltersModal').classList.add('hidden');
}

function clearAdvancedFilters() {
    document.querySelectorAll('#advancedFiltersModal input').forEach(input => {
        if (input.type === 'checkbox') input.checked = false;
        else if (input.type === 'range') input.value = input.min || 0;
        else input.value = '';
    });
    document.querySelectorAll('#advancedFiltersModal select').forEach(select => {
        select.value = '';
    });
}

function applyAdvancedFilters() {
    // Collect all filter values and apply them
    // This would typically make an AJAX request to filter the data
    console.log('Applying advanced filters...');
    closeAdvancedFilters();
}

// Quick Filters
function applyQuickFilters() {
    const search = document.getElementById('quickSearch').value;
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const fuel = document.getElementById('fuelFilter').value;
    
    // Build query string
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (type) params.append('type', type);
    if (fuel) params.append('fuel', fuel);
    
    // Redirect with filters
    window.location.href = '{{ route("maintenance.vehicles.index") }}?' + params.toString();
}

function resetAllFilters() {
    document.getElementById('quickSearch').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('fuelFilter').value = '';
    clearAdvancedFilters();
    window.location.href = '{{ route("maintenance.vehicles.index") }}';
}

// Bulk Operations
let bulkSelectMode = false;

function toggleBulkSelect() {
    bulkSelectMode = !bulkSelectMode;
    const selectColumns = document.querySelectorAll('.column-select');
    selectColumns.forEach(col => {
        if (bulkSelectMode) {
            col.classList.remove('hidden');
        } else {
            col.classList.add('hidden');
        }
    });
}

document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Sorting
document.querySelectorAll('.sortable').forEach(header => {
    header.addEventListener('click', function() {
        const column = this.dataset.column;
        const icon = this.querySelector('i');
        const currentUrl = new URL(window.location.href);
        
        // Get current sort parameters
        const currentSort = currentUrl.searchParams.get('sort');
        const currentOrder = currentUrl.searchParams.get('order');
        
        // Determine new order
        let newOrder = 'asc';
        if (currentSort === column && currentOrder === 'asc') {
            newOrder = 'desc';
        }
        
        // Update URL parameters
        currentUrl.searchParams.set('sort', column);
        currentUrl.searchParams.set('order', newOrder);
        
        // Redirect with new sort
        window.location.href = currentUrl.toString();
    });
});

// Health score range sliders
document.getElementById('healthMin')?.addEventListener('input', function() {
    document.getElementById('healthMinValue').textContent = this.value + '%';
});

document.getElementById('healthMax')?.addEventListener('input', function() {
    document.getElementById('healthMaxValue').textContent = this.value + '%';
});

// Real-time search
let searchTimeout;
document.getElementById('quickSearch')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (this.value.length >= 3 || this.value.length === 0) {
            applyQuickFilters();
        }
    }, 500);
});

// Export functionality
function exportData() {
    const format = prompt('Export format: csv, excel, or pdf?', 'csv');
    if (format) {
        // Get current filters
        const params = new URLSearchParams();
        
        // Add current search filters
        const search = document.getElementById('quickSearch')?.value;
        const status = document.getElementById('statusFilter')?.value;
        const fuel = document.getElementById('fuelFilter')?.value;
        
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (fuel) params.append('fuel', fuel);
        
        // Add export format
        params.append('export', format);
        
        // Redirect to export route with filters
        window.location.href = '{{ url("maintenance/vehicles/export") }}?' + params.toString();
    }
}

// Other functions
function changePerPage(value) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('per_page', value);
    window.location.href = currentUrl.toString();
}

function refreshTable() {
    location.reload();
}

function viewVehicle(id) {
    window.location.href = '{{ url("maintenance/vehicles") }}/' + id + '/profile';
}

function showMaintenanceModal(id) {
    // Open maintenance scheduling modal
    if (confirm('Schedule maintenance for vehicle #' + id + '?')) {
        window.location.href = `{{ route("maintenance.schedule") }}?vehicle_id=${id}`;
    }
}

function deleteVehicle(id) {
    if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("maintenance/vehicles") }}/' + id;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Load saved column preferences on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedColumns = localStorage.getItem('vehicleTableColumns');
    if (savedColumns) {
        const columns = JSON.parse(savedColumns);
        document.querySelectorAll('.column-toggle').forEach(toggle => {
            toggle.checked = columns.includes(toggle.dataset.column);
        });
        
        // Apply saved columns
        document.querySelectorAll('[class*="column-"]').forEach(element => {
            const columnClass = Array.from(element.classList).find(c => c.startsWith('column-'));
            if (columnClass) {
                const columnName = columnClass.replace('column-', '');
                if (!columns.includes(columnName) && columnName !== 'actions') {
                    element.classList.add('hidden');
                }
            }
        });
    }
    
    // Initialize tooltips if available
    if (typeof tippy !== 'undefined') {
        tippy('[title]', {
            placement: 'top',
            animation: 'fade',
        });
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('quickSearch')?.focus();
    }
    
    // Ctrl/Cmd + E for export
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportData();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        closeColumnManager();
        closeAdvancedFilters();
    }
});
</script>
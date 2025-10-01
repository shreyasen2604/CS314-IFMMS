# Role-Based Access Control for Maintenance Module

## Overview
The Predictive Maintenance & Scheduling Module has been implemented with comprehensive role-based access control for three user types: **Admin**, **Driver**, and **Technician**. Each role has specific permissions and access levels tailored to their responsibilities.

## User Roles and Access Privileges

### 🔴 **ADMIN** - Full Access & Management
**Navigation Access:**
- ✅ Maintenance Dashboard (full access)
- ✅ Vehicle Management (create, edit, delete vehicles)
- ✅ Scheduler (create, edit, delete schedules)
- ✅ Analytics (full access)
- ✅ Records (full access + export)
- ✅ Alerts (full management + configuration)

**Specific Privileges:**
- **Vehicle Management**: Create, edit, and manage all vehicles in the fleet
- **Alert Configuration**: Set thresholds, create test alerts, configure notification preferences
- **Schedule Management**: Create, edit, and delete maintenance schedules for any vehicle
- **Export Capabilities**: Export maintenance records and reports
- **User Management**: Access to user management (existing feature)
- **System Configuration**: Full administrative control

**Restricted Actions:** None - Full system access

---

### 🟡 **TECHNICIAN** - Work Management & Execution
**Navigation Access:**
- ✅ Maintenance Dashboard (read-only)
- ✅ Work Queue (personal work management)
- ✅ Scheduler (create, edit schedules)
- ✅ Analytics (read-only)
- ✅ Records (read-only + export)
- ✅ Alerts (acknowledge, resolve)

**Specific Privileges:**
- **Work Queue Management**: View assigned tasks, claim available work, start/complete maintenance
- **Schedule Creation**: Create and modify maintenance schedules
- **Alert Management**: Acknowledge and resolve maintenance alerts
- **Progress Tracking**: Update work progress and completion status
- **Record Access**: View maintenance records and export reports
- **Cost Tracking**: Input parts and labor costs for completed work

**Restricted Actions:**
- ❌ Cannot configure alert thresholds
- ❌ Cannot create test alerts
- ❌ Cannot manage vehicles (create/edit/delete)
- ❌ Cannot access user management

---

### 🟢 **DRIVER** - Vehicle Monitoring & Requests
**Navigation Access:**
- ✅ Maintenance Dashboard (read-only)
- ✅ My Vehicle (personal vehicle status)
- ✅ Analytics (read-only)
- ✅ Records (read-only, filtered to their vehicle)
- ✅ Alerts (view-only)

**Specific Privileges:**
- **Vehicle Status**: View their assigned vehicle's maintenance status and health score
- **Maintenance Requests**: Submit maintenance requests with priority levels
- **History Access**: View maintenance history for their assigned vehicle
- **Alert Visibility**: See active alerts for their vehicle
- **Schedule Visibility**: View upcoming scheduled maintenance for their vehicle

**Restricted Actions:**
- ❌ Cannot access scheduler (no scheduling privileges)
- ❌ Cannot acknowledge or resolve alerts
- ❌ Cannot export records
- ❌ Cannot configure any system settings
- ❌ Cannot view other vehicles' information
- ❌ Cannot manage work queues

---

## Implementation Details

### Route Protection
```php
// Admin-only routes
Route::middleware([RoleMiddleware::class . ':Admin'])->group(function () {
    Route::post('/alerts/test', [MaintenanceController::class, 'createTestAlert']);
    Route::post('/alerts/configure', [MaintenanceController::class, 'configureThresholds']);
    Route::resource('vehicles', MaintenanceController::class);
});

// Admin & Technician routes
Route::middleware([RoleMiddleware::class . ':Admin|Technician'])->group(function () {
    Route::get('/schedule', [MaintenanceController::class, 'scheduler']);
    Route::post('/alerts/{id}/acknowledge', [MaintenanceController::class, 'acknowledgeAlert']);
    Route::get('/records/export', [MaintenanceController::class, 'exportRecords']);
});

// Technician-only routes
Route::middleware([RoleMiddleware::class . ':Technician'])->group(function () {
    Route::get('/work-queue', [MaintenanceController::class, 'technicianWorkQueue']);
    Route::post('/work/{id}/start', [MaintenanceController::class, 'startWork']);
});

// Driver-only routes
Route::middleware([RoleMiddleware::class . ':Driver'])->group(function () {
    Route::get('/my-vehicle', [MaintenanceController::class, 'driverVehicle']);
    Route::post('/request-maintenance', [MaintenanceController::class, 'requestMaintenance']);
});
```

### View-Level Access Control
```blade
{{-- Admin-only buttons --}}
@if(auth()->user()->role === 'Admin')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#configureModal">
        Configure Thresholds
    </button>
@endif

{{-- Admin & Technician buttons --}}
@if(in_array(auth()->user()->role, ['Admin', 'Technician']))
    <button class="btn btn-success" onclick="exportRecords()">
        Export CSV
    </button>
@endif

{{-- Driver-specific content --}}
@if(auth()->user()->role === 'Driver')
    <a href="{{ route('maintenance.driver.vehicle') }}">My Vehicle</a>
@endif
```

### Navigation Menu Customization
The sidebar navigation dynamically shows/hides menu items based on user roles:

- **Admin**: Sees all maintenance options + vehicle management
- **Technician**: Sees work queue + scheduling tools
- **Driver**: Sees "My Vehicle" option only

## Role-Specific Features

### Admin Features
1. **Vehicle Management Dashboard**: Complete CRUD operations for fleet vehicles
2. **Alert Configuration Panel**: Set system-wide thresholds and notification preferences
3. **Advanced Analytics**: Full access to all fleet data and trends
4. **System Monitoring**: Overview of all maintenance activities across the fleet

### Technician Features
1. **Personal Work Queue**: View assigned tasks and available work
2. **Work Management**: Start, update progress, and complete maintenance tasks
3. **Schedule Creation**: Create maintenance appointments for vehicles
4. **Alert Response**: Acknowledge and resolve maintenance alerts

### Driver Features
1. **Vehicle Health Dashboard**: Real-time status of their assigned vehicle
2. **Maintenance Request System**: Submit requests with priority levels
3. **Personal History**: View maintenance history for their vehicle only
4. **Alert Notifications**: See active alerts affecting their vehicle

## Security Measures

### Middleware Protection
- All routes protected by authentication middleware
- Role-specific middleware prevents unauthorized access
- Numeric constraints on route parameters prevent injection

### Data Filtering
- Drivers only see data for their assigned vehicle
- Technicians see work assigned to them or available for claiming
- Admins have unrestricted data access

### Action Restrictions
- Form submissions validated against user roles
- AJAX requests include role verification
- Database queries filtered by user permissions

## Benefits of This Implementation

1. **Security**: Each role has appropriate access levels
2. **Usability**: Users see only relevant features for their role
3. **Efficiency**: Streamlined interfaces reduce cognitive load
4. **Accountability**: Clear separation of responsibilities
5. **Scalability**: Easy to add new roles or modify permissions

## Future Enhancements

1. **Granular Permissions**: Sub-roles within each main role
2. **Department-Based Access**: Restrict access by organizational units
3. **Time-Based Permissions**: Temporary access grants
4. **Audit Logging**: Track all user actions for compliance
5. **Mobile App Integration**: Role-specific mobile interfaces

This role-based access control system ensures that each user type has the appropriate tools and information needed for their specific responsibilities while maintaining system security and data integrity.
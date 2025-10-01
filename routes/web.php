<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\RoleMiddleware;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\IncidentController as AdminIncidentController;
use App\Http\Controllers\Driver\IncidentController as DriverIncidentController;
use App\Http\Controllers\Technician\IncidentController as TechIncidentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MaintenanceDashboardController;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\PredictiveAnalyticsController;
use App\Http\Controllers\MaintenanceAlertController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RouteAssignmentController;
use App\Http\Controllers\DriverPerformanceController;

// Landing
Route::get('/', fn () =>
    auth()->check()
        ? redirect()->route(strtolower(auth()->user()->role).'.dashboard')
        : redirect()->route('login')
);

// Auth (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout (auth only)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/* -------------------- ADMIN -------------------- */
Route::prefix('admin')->name('admin.')->middleware(['auth', RoleMiddleware::class . ':Admin'])->group(function () {
    Route::get('/', fn () => view('dashboards.admin'))->name('dashboard');

    // Users
    Route::resource('users', UserController::class)->except(['show']);

    // Incidents
    Route::get('incidents', [AdminIncidentController::class, 'index'])->name('incidents.index');

    // ✅ Export placed BEFORE {incident} so it isn't captured by the param route
    Route::get('incidents/export', [AdminIncidentController::class, 'export'])->name('incidents.export');

    // Param routes (guarded with numeric constraint for safety)
    Route::get('incidents/{incident}', [AdminIncidentController::class, 'show'])
        ->whereNumber('incident')->name('incidents.show');
    Route::post('incidents/{incident}/assign', [AdminIncidentController::class, 'assign'])
        ->whereNumber('incident')->name('incidents.assign');
    Route::post('incidents/{incident}/status', [AdminIncidentController::class, 'status'])
        ->whereNumber('incident')->name('incidents.status');
    Route::post('incidents/{incident}/comment', [AdminIncidentController::class, 'comment'])
        ->whereNumber('incident')->name('incidents.comment');
});

/* -------------------- DRIVER -------------------- */
Route::prefix('driver')->name('driver.')->middleware(['auth', RoleMiddleware::class . ':Driver'])->group(function () {
    Route::get('/', fn () => view('dashboards.driver'))->name('dashboard');

    Route::get('/incidents', [DriverIncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [DriverIncidentController::class, 'create'])->name('incidents.create');
    Route::get('/incidents/emergency', [DriverIncidentController::class, 'emergency'])->name('incidents.emergency');
    Route::post('/incidents', [DriverIncidentController::class, 'store'])->name('incidents.store');
    Route::post('/incidents/emergency', [DriverIncidentController::class, 'emergencyStore'])->name('incidents.emergency.store');

    Route::get('/incidents/{incident}', [DriverIncidentController::class, 'show'])->name('incidents.show');
    Route::post('/incidents/{incident}/comment', [DriverIncidentController::class, 'comment'])->name('incidents.comment');
    Route::post('/incidents/{incident}/acknowledge', [DriverIncidentController::class, 'acknowledge'])->name('incidents.ack');
});

/* -------------------- TECHNICIAN -------------------- */
Route::prefix('technician')->name('technician.')->middleware(['auth', RoleMiddleware::class . ':Technician'])->group(function () {
    Route::get('/', fn () => view('dashboards.technician'))->name('dashboard');

    Route::get('incidents', [TechIncidentController::class, 'index'])->name('incidents.index');

    // ✅ Tech export lives here (correct alias)
    Route::get('incidents/export', [TechIncidentController::class, 'export'])->name('incidents.export');

    Route::get('incidents/{incident}', [TechIncidentController::class, 'show'])
        ->whereNumber('incident')->name('incidents.show');
    Route::post('incidents/{incident}/claim', [TechIncidentController::class, 'claim'])
        ->whereNumber('incident')->name('incidents.claim');
    Route::post('incidents/{incident}/status', [TechIncidentController::class, 'status'])
        ->whereNumber('incident')->name('incidents.status');
    Route::post('incidents/{incident}/comment', [TechIncidentController::class, 'comment'])
        ->whereNumber('incident')->name('incidents.comment');
});

/* -------------------- MAINTENANCE MODULE -------------------- */
Route::prefix('maintenance')->name('maintenance.')->middleware('auth')->group(function () {

    // ==================== DASHBOARD ROUTES ====================
    Route::get('/dashboard', [MaintenanceDashboardController::class, 'index'])->name('dashboard');

    // API endpoints for real-time dashboard updates
    Route::get('/api/fleet-stats', [MaintenanceDashboardController::class, 'getFleetStatsApi'])->name('api.fleet-stats');
    Route::get('/api/health-distribution', [MaintenanceDashboardController::class, 'getHealthDistributionApi'])->name('api.health-distribution');
    Route::get('/api/cost-analysis', [MaintenanceDashboardController::class, 'getCostAnalysisApi'])->name('api.cost-analysis');

    // ==================== ANALYTICS ROUTES ====================
    Route::get('/analytics', [PredictiveAnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/vehicle/{id}/health', [PredictiveAnalyticsController::class, 'getVehicleHealthData'])->name('analytics.vehicle-health');
    Route::get('/analytics/fleet-trends', [PredictiveAnalyticsController::class, 'getFleetTrends'])->name('analytics.fleet-trends');

    // ==================== RECORDS ROUTES ====================
    Route::get('/records', [MaintenanceRecordController::class, 'index'])->name('records');
    Route::get('/records/{id}', [MaintenanceRecordController::class, 'show'])->name('records.show');
    Route::get('/records/vehicle/{id}/history', [MaintenanceRecordController::class, 'getMaintenanceHistory'])->name('records.vehicle-history');
    Route::get('/records/cost-analysis', [MaintenanceRecordController::class, 'getCostAnalysis'])->name('records.cost-analysis');

    // ==================== ALERTS ROUTES ====================
    Route::get('/alerts', [MaintenanceAlertController::class, 'index'])->name('alerts');
    Route::get('/alerts/{id}', [MaintenanceAlertController::class, 'show'])->name('alerts.show');
    Route::get('/alerts/vehicle/{id}', [MaintenanceAlertController::class, 'getVehicleAlerts'])->name('alerts.vehicle');
    Route::get('/alerts/trends', [MaintenanceAlertController::class, 'getAlertTrends'])->name('alerts.trends');
    Route::get('/alerts/config', [MaintenanceAlertController::class, 'getConfiguration'])->name('alerts.config');

    // ==================== VEHICLE PROFILE ROUTES ====================
    Route::get('/vehicles/{id}/profile', [MaintenanceController::class, 'vehicleProfile'])->name('vehicle.profile');

    // ==================== ROLE-RESTRICTED ROUTES ====================

    // Admin & Technician only routes
    Route::middleware([RoleMiddleware::class . ':Admin|Technician'])->group(function () {

        // SCHEDULER ROUTES
        Route::get('/schedule', [MaintenanceScheduleController::class, 'index'])->name('schedule');
        Route::get('/schedule/calendar', [MaintenanceScheduleController::class, 'calendar'])->name('schedule.calendar');
        Route::post('/schedule', [MaintenanceScheduleController::class, 'store'])->name('schedule.store');
        Route::get('/schedule/{id}', [MaintenanceScheduleController::class, 'show'])->name('schedule.show');
        Route::put('/schedule/{id}', [MaintenanceScheduleController::class, 'update'])->name('schedule.update');
        Route::delete('/schedule/{id}', [MaintenanceScheduleController::class, 'destroy'])->name('schedule.delete');
        Route::post('/schedule/{id}/update-date', [MaintenanceScheduleController::class, 'updateDate'])->name('schedule.update-date');
        Route::post('/schedule/bulk', [MaintenanceScheduleController::class, 'bulkSchedule'])->name('schedule.bulk');
        Route::get('/schedule/calendar/events', [MaintenanceScheduleController::class, 'getCalendarEvents'])->name('schedule.calendar-events');
        Route::get('/schedule/recommendations/{vehicleId}', [MaintenanceScheduleController::class, 'getRecommendations'])->name('schedule.recommendations');

        // RECORDS MANAGEMENT
        Route::post('/records', [MaintenanceRecordController::class, 'store'])->name('records.store');
        Route::put('/records/{id}', [MaintenanceRecordController::class, 'update'])->name('records.update');
        Route::delete('/records/{id}', [MaintenanceRecordController::class, 'destroy'])->name('records.delete');
        Route::get('/records/export', [MaintenanceRecordController::class, 'export'])->name('records.export');

        // ALERT ACTIONS
        Route::post('/alerts/{id}/acknowledge', [MaintenanceAlertController::class, 'acknowledge'])->name('alerts.acknowledge');
        Route::post('/alerts/{id}/resolve', [MaintenanceAlertController::class, 'resolve'])->name('alerts.resolve');
        Route::post('/alerts/bulk-acknowledge', [MaintenanceAlertController::class, 'bulkAcknowledge'])->name('alerts.bulk-acknowledge');
        Route::post('/alerts/bulk-resolve', [MaintenanceAlertController::class, 'bulkResolve'])->name('alerts.bulk-resolve');
    });

    // Admin only routes
    Route::middleware([RoleMiddleware::class . ':Admin'])->group(function () {

        // ALERT CONFIGURATION
        Route::post('/alerts/test', [MaintenanceAlertController::class, 'createTestAlert'])->name('alerts.test');
        Route::post('/alerts/configure', [MaintenanceAlertController::class, 'configureThresholds'])->name('alerts.configure');
        Route::post('/alerts/generate-system', [MaintenanceAlertController::class, 'generateSystemAlerts'])->name('alerts.generate-system');
        Route::post('/alerts', [MaintenanceAlertController::class, 'store'])->name('alerts.store');

        // VEHICLE MANAGEMENT
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
        Route::get('/vehicles/filter', [VehicleController::class, 'filter'])->name('vehicles.filter');
        Route::get('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
        Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{id}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicles/{id}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::get('/vehicles/assignments', [VehicleController::class, 'assignments'])->name('vehicles.assignments');
        Route::post('/vehicles/assignments', [VehicleController::class, 'updateAssignments'])->name('vehicles.assignments.update');

        // ROUTE MANAGEMENT
        Route::resource('routes', RouteController::class);
        Route::post('/routes/{route}/optimize', [RouteController::class, 'optimize'])->name('routes.optimize');
        Route::get('/routes-export', [RouteController::class, 'export'])->name('routes-export');

        // ROUTE ASSIGNMENTS
        Route::resource('route-assignments', RouteAssignmentController::class);
        Route::post('/route-assignments/{routeAssignment}/start', [RouteAssignmentController::class, 'start'])->name('route-assignments.start');
        Route::post('/route-assignments/{routeAssignment}/complete', [RouteAssignmentController::class, 'complete'])->name('route-assignments.complete');
        Route::post('/route-assignments/{routeAssignment}/cancel', [RouteAssignmentController::class, 'cancel'])->name('route-assignments.cancel');
        Route::get('/route-assignments/{routeAssignment}/tracking', [RouteAssignmentController::class, 'tracking'])->name('route-assignments.tracking');
        Route::post('/route-assignments/{routeAssignment}/location', [RouteAssignmentController::class, 'updateLocation'])->name('route-assignments.location');
        Route::post('/route-assignments/bulk-assign', [RouteAssignmentController::class, 'bulkAssign'])->name('route-assignments.bulk-assign');
        Route::get('/route-assignments-calendar', [RouteAssignmentController::class, 'calendar'])->name('route-assignments.calendar');

        // DRIVER PERFORMANCE
        Route::get('/driver-performance', [DriverPerformanceController::class, 'index'])->name('driver-performance.index');
        Route::get('/driver-performance/{driver}', [DriverPerformanceController::class, 'show'])->name('driver-performance.show');
        Route::get('/driver-performance-compare', [DriverPerformanceController::class, 'compare'])->name('driver-performance.compare');
        Route::get('/driver-performance-rankings', [DriverPerformanceController::class, 'rankings'])->name('driver-performance.rankings');
        Route::get('/driver-performance-analytics', [DriverPerformanceController::class, 'analytics'])->name('driver-performance.analytics');
        Route::post('/driver-performance/update-metrics', [DriverPerformanceController::class, 'updateMetrics'])->name('driver-performance.update-metrics');
        Route::post('/driver-performance/generate-report', [DriverPerformanceController::class, 'generateReport'])->name('driver-performance.generate-report');
    });

    // Technician specific routes
    Route::middleware([RoleMiddleware::class . ':Technician'])->group(function () {
        // TECHNICIAN WORK QUEUE
        Route::get('/work-queue', [MaintenanceController::class, 'technicianWorkQueue'])->name('technician.work-queue');
        Route::post('/work/{id}/start', [MaintenanceController::class, 'startWork'])->name('technician.start-work');
        Route::post('/work/{id}/complete', [MaintenanceController::class, 'completeWork'])->name('technician.complete-work');
        Route::post('/work/{id}/update', [MaintenanceController::class, 'updateWorkProgress'])->name('technician.update-work');
    });

    // Driver specific routes
    Route::middleware([RoleMiddleware::class . ':Driver'])->group(function () {
        // DRIVER VEHICLE ACCESS
        Route::get('/my-vehicle', [MaintenanceController::class, 'driverVehicle'])->name('driver.vehicle');
        Route::post('/request-maintenance', [MaintenanceController::class, 'requestMaintenance'])->name('driver.request-maintenance');
    });

    Route::get('/maintenance/records/export', [MaintenanceController::class, 'export'])
        ->name('maintenance.export')
        ->middleware('auth');

    // Add GET route for edit form of maintenance records
    Route::get('/maintenance/records/{id}/edit', [MaintenanceRecordController::class, 'edit'])
        ->name('records.edit')
        ->middleware(['auth', RoleMiddleware::class . ':Admin|Technician']);
});

    Route::middleware(['auth'])->group(function () {
        // Driver-only list of their own requests
        Route::get('/driver/service-requests', [ServiceRequestController::class, 'driverIndex'])
            ->name('driver.service-requests.index');
        // (Show already exists → we’ll reuse your existing show() with a tiny guard below)
    });

/* -------------------- COMMUNICATION MODULE -------------------- */
Route::prefix('communication')->name('communication.')->middleware('auth')->group(function () {

    // Main communication dashboard
    Route::get('/', [CommunicationController::class, 'index'])->name('index');

    // Messages
    Route::get('/messages', [CommunicationController::class, 'messages'])->name('messages');
    Route::get('/messages/compose', [CommunicationController::class, 'compose'])->name('compose');
    Route::post('/messages/send', [CommunicationController::class, 'sendMessage'])->name('send-message');
    Route::get('/messages/{id}', [CommunicationController::class, 'viewMessage'])->name('view-message');
    Route::post('/messages/{id}/reply', [CommunicationController::class, 'replyMessage'])->name('reply-message');
    Route::delete('/messages/{id}/delete', [CommunicationController::class, 'deleteMessage'])->name('delete-message');
    Route::post('/messages/{id}/archive', [CommunicationController::class, 'archiveMessage'])->name('archive-message');

    // Announcements
    Route::get('/announcements', [CommunicationController::class, 'announcements'])->name('announcements');
    Route::get('/announcements/{id}', [CommunicationController::class, 'viewAnnouncement'])->name('view-announcement');

    // Admin only announcement creation
    Route::middleware([RoleMiddleware::class . ':Admin'])->group(function () {
        Route::get('/announcement/create', [CommunicationController::class, 'createAnnouncement'])->name('create-announcement');
        Route::post('/announcement/store', [CommunicationController::class, 'storeAnnouncement'])->name('store-announcement');
        Route::get('/announcements/{id}/edit', [CommunicationController::class, 'editAnnouncement'])->name('edit-announcement');
        Route::delete('/announcements/{id}/delete', [CommunicationController::class, 'deleteAnnouncement'])->name('delete-announcement');
    });

    // User preferences
    Route::post('/preferences/update', [CommunicationController::class, 'updatePreferences'])->name('update-preferences');

    // API endpoints
    Route::get('/api/users/search', [CommunicationController::class, 'searchUsers'])->name('search-users');
    Route::get('/api/notifications/count', [CommunicationController::class, 'getNotificationCount'])->name('notification-count');
    Route::post('/api/notifications/read', [CommunicationController::class, 'markNotificationsRead'])->name('mark-notifications-read');
});

/* -------------------- SERVICE SUPPORT & INCIDENT MANAGEMENT MODULE -------------------- */
Route::prefix('support')->name('support.')->middleware('auth')->group(function () {

    // Service Requests
    Route::get('/service-requests', [ServiceRequestController::class, 'index'])->name('service-requests.index');
    Route::get('/service-requests/create', [ServiceRequestController::class, 'create'])->name('service-requests.create');
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::get('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('service-requests.show');
    Route::put('/service-requests/{serviceRequest}', [ServiceRequestController::class, 'update'])->name('service-requests.update');
    Route::post('/service-requests/{serviceRequest}/comment', [ServiceRequestController::class, 'addComment'])->name('service-requests.comment');
    Route::post('/service-requests/{serviceRequest}/close', [ServiceRequestController::class, 'close'])->name('service-requests.close');

    // Service Request Actions (Admin/Technician)
    Route::middleware([RoleMiddleware::class . ':Admin|Technician'])->group(function () {
        Route::post('/service-requests/{serviceRequest}/assign', [ServiceRequestController::class, 'assignTechnician'])->name('service-requests.assign');
        Route::post('/service-requests/{serviceRequest}/priority', [ServiceRequestController::class, 'updatePriority'])->name('service-requests.priority');
    });

    // Incident Reports
    Route::get('/incidents', [IncidentReportController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [IncidentReportController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [IncidentReportController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/export', [IncidentReportController::class, 'export'])->name('incidents.export');
    Route::get('/incidents/{incident}', [IncidentReportController::class, 'show'])->name('incidents.show');
    Route::put('/incidents/{incident}', [IncidentReportController::class, 'update'])->name('incidents.update');
    Route::post('/incidents/{incident}/comment', [IncidentReportController::class, 'addComment'])->name('incidents.comment');

    // Incident Report Actions (Admin)
    Route::middleware([RoleMiddleware::class . ':Admin'])->group(function () {
        Route::post('/incidents/{incident}/insurance', [IncidentReportController::class, 'updateInsurance'])->name('incidents.insurance');
        Route::post('/incidents/{incident}/police', [IncidentReportController::class, 'updatePoliceReport'])->name('incidents.police');
    });

    // Statistics & Reports
    Route::get('/statistics', [ServiceRequestController::class, 'statistics'])->name('statistics');
    Route::get('/incident-statistics', [IncidentReportController::class, 'statistics'])->name('incident-statistics');
});

/* -------------------- HELP & SUPPORT -------------------- */
Route::prefix('help')->name('help.')->middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('help.index');
    })->name('index');

    Route::get('/tutorials', function () {
        return view('help.index') . '#tutorials';
    })->name('tutorials');

    Route::get('/documentation', function () {
        return view('help.index') . '#documentation';
    })->name('documentation');

    Route::get('/faqs', function () {
        return view('help.index') . '#faqs';
    })->name('faqs');

    Route::get('/contact', function () {
        return view('help.index') . '#contact';
    })->name('contact');
});

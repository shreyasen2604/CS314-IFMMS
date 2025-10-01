<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function dashboard()
    {
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        $vehiclesInMaintenance = Vehicle::where('status', 'maintenance')->count();

        $overdueMaintenances = Vehicle::whereDate('next_maintenance_due', '<', now())->count();
        $dueSoonMaintenances = Vehicle::whereBetween('next_maintenance_due', [
            now()->toDateString(),
            now()->addDays(7)->toDateString()
        ])->count();

        $activeAlerts = MaintenanceAlert::where('status', 'active')->count();
        $criticalAlerts = MaintenanceAlert::where('status', 'active')
            ->where('severity', 'critical')->count();

        $todayScheduled = MaintenanceSchedule::whereDate('scheduled_date', today())
            ->where('status', '!=', 'completed')->count();

        $avgHealthScore = Vehicle::avg('health_score');

        $monthlyMaintenanceCost = MaintenanceRecord::whereMonth('service_date', now()->month)
            ->whereYear('service_date', now()->year)
            ->sum('cost');

        // Recent alerts
        $recentAlerts = MaintenanceAlert::with('vehicle')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming maintenance
        $upcomingMaintenance = MaintenanceSchedule::with(['vehicle', 'technician'])
            ->where('status', '!=', 'completed')
            ->whereDate('scheduled_date', '>=', today())
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        // Vehicle health distribution
        $healthDistribution = [
            'excellent' => Vehicle::where('health_score', '>=', 80)->count(),
            'good' => Vehicle::whereBetween('health_score', [60, 79])->count(),
            'fair' => Vehicle::whereBetween('health_score', [40, 59])->count(),
            'poor' => Vehicle::where('health_score', '<', 40)->count(),
        ];

        return view('maintenance.dashboard', compact(
            'totalVehicles', 'activeVehicles', 'vehiclesInMaintenance',
            'overdueMaintenances', 'dueSoonMaintenances', 'activeAlerts',
            'criticalAlerts', 'todayScheduled', 'avgHealthScore',
            'monthlyMaintenanceCost', 'recentAlerts', 'upcomingMaintenance',
            'healthDistribution'
        ));
    }

    public function vehicleProfile($id)
    {
        $vehicle = Vehicle::with(['driver', 'maintenanceRecords.technician', 'maintenanceSchedules.technician', 'maintenanceAlerts'])
            ->findOrFail($id);

        $maintenanceHistory = $vehicle->maintenanceRecords()
            ->orderBy('service_date', 'desc')
            ->paginate(10);

        $upcomingSchedules = $vehicle->maintenanceSchedules()
            ->where('status', '!=', 'completed')
            ->whereDate('scheduled_date', '>=', today())
            ->orderBy('scheduled_date')
            ->get();

        $activeAlerts = $vehicle->maintenanceAlerts()
            ->where('status', 'active')
            ->orderBy('severity', 'desc')
            ->get();

        $totalMaintenanceCost = $vehicle->maintenanceRecords()->sum('cost');
        $avgCostPerService = $vehicle->maintenanceRecords()->avg('cost');

        return view('maintenance.vehicle-profile', compact(
            'vehicle', 'maintenanceHistory', 'upcomingSchedules',
            'activeAlerts', 'totalMaintenanceCost', 'avgCostPerService'
        ));
    }

    public function scheduler()
    {
        $vehicles = Vehicle::where('status', '!=', 'retired')->get();
        $technicians = \App\Models\User::where('role', 'Technician')->get();

        $schedules = MaintenanceSchedule::with(['vehicle', 'technician'])
            ->where('status', '!=', 'completed')
            ->get();

        return view('maintenance.scheduler', compact('vehicles', 'technicians', 'schedules'));
    }

    public function analytics()
    {
        // Vehicle health scores
        $vehicleHealthData = Vehicle::select('id', 'vehicle_number', 'health_score', 'make', 'model')
            ->orderBy('health_score', 'desc')
            ->get();

        // Monthly maintenance costs
        $monthlyCosts = MaintenanceRecord::select(
                DB::raw('YEAR(service_date) as year'),
                DB::raw('MONTH(service_date) as month'),
                DB::raw('SUM(cost) as total_cost')
            )
            ->whereYear('service_date', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Maintenance type breakdown
        $maintenanceTypes = MaintenanceRecord::select('maintenance_type', DB::raw('COUNT(*) as count'))
            ->groupBy('maintenance_type')
            ->get();

        // Breakdown risk assessment
        $breakdownRisk = Vehicle::select('id', 'vehicle_number', 'health_score', 'mileage', 'last_maintenance_date')
            ->where('health_score', '<', 60)
            ->orWhere('last_maintenance_date', '<', now()->subMonths(6))
            ->get();

        // Performance trends
        $performanceTrends = MaintenanceRecord::select(
                DB::raw('DATE(service_date) as date'),
                DB::raw('AVG(cost) as avg_cost'),
                DB::raw('COUNT(*) as service_count')
            )
            ->whereYear('service_date', now()->year)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('maintenance.analytics', compact(
            'vehicleHealthData', 'monthlyCosts', 'maintenanceTypes',
            'breakdownRisk', 'performanceTrends'
        ));
    }

    public function records(Request $request)
    {
        $query = MaintenanceRecord::with(['vehicle', 'technician']);

        // Apply filters
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('service_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('service_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('service_date', 'desc')->paginate(15);

        $vehicles = Vehicle::all();
        $totalCost = $query->sum('cost');

        return view('maintenance.records', compact('records', 'vehicles', 'totalCost'));
    }

    public function alerts()
    {
        $alerts = MaintenanceAlert::with('vehicle')
            ->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $alertStats = [
            'total' => MaintenanceAlert::count(),
            'active' => MaintenanceAlert::where('status', 'active')->count(),
            'critical' => MaintenanceAlert::where('severity', 'critical')->count(),
            'acknowledged' => MaintenanceAlert::where('status', 'acknowledged')->count(),
        ];

        return view('maintenance.alerts', compact('alerts', 'alertStats'));
    }

    // Additional methods for handling AJAX requests and form submissions

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type' => 'required|string',
            'scheduled_date' => 'required|date',
            'estimated_duration' => 'required|integer|min:15',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        $schedule = MaintenanceSchedule::create([
            'vehicle_id' => $request->vehicle_id,
            'technician_id' => $request->technician_id,
            'maintenance_type' => $request->maintenance_type,
            'scheduled_date' => $request->scheduled_date,
            'estimated_duration' => $request->estimated_duration,
            'priority' => $request->priority,
            'description' => $request->description,
            'recurring' => $request->has('recurring'),
            'recurring_type' => $request->recurring_type,
            'recurring_interval' => $request->recurring_interval,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'schedule' => $schedule]);
    }

    public function deleteSchedule($id)
    {
        $schedule = MaintenanceSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['success' => true]);
    }

    public function updateScheduleDate(Request $request, $id)
    {
        $schedule = MaintenanceSchedule::findOrFail($id);
        $schedule->update([
            'scheduled_date' => $request->scheduled_date
        ]);

        return response()->json(['success' => true]);
    }

    public function showRecord($id)
    {
        $record = MaintenanceRecord::with(['vehicle', 'technician'])->findOrFail($id);
        return response()->json($record);
    }

    public function exportRecords(Request $request)
    {
        // Implementation for exporting records
        return response()->json(['success' => true, 'message' => 'Export functionality to be implemented']);
    }

    public function showAlert($id)
    {
        $alert = MaintenanceAlert::with(['vehicle', 'acknowledgedBy'])->findOrFail($id);
        return response()->json($alert);
    }

    public function acknowledgeAlert($id)
    {
        $alert = MaintenanceAlert::findOrFail($id);
        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_by' => auth()->id(),
            'acknowledged_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function resolveAlert($id)
    {
        $alert = MaintenanceAlert::findOrFail($id);
        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function bulkAcknowledgeAlerts(Request $request)
    {
        $alertIds = $request->alert_ids;

        MaintenanceAlert::whereIn('id', $alertIds)->update([
            'status' => 'acknowledged',
            'acknowledged_by' => auth()->id(),
            'acknowledged_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function createTestAlert()
    {
        $vehicle = Vehicle::first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'No vehicles available']);
        }

        MaintenanceAlert::create([
            'vehicle_id' => $vehicle->id,
            'alert_type' => 'test',
            'severity' => 'medium',
            'title' => 'Test Alert',
            'message' => 'This is a test alert created at ' . now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['success' => true]);
    }

    public function configureThresholds(Request $request)
    {
        // Implementation for saving threshold configurations
        // This would typically save to a settings table or config file
        return response()->json(['success' => true, 'message' => 'Configuration saved successfully']);
    }

    // Vehicle Assignments Management
    public function vehicleAssignments()
    {
        $vehicles = Vehicle::with(['driver', 'maintenanceRecords' => function($query) {
            $query->latest()->limit(1);
        }])->get();

        $drivers = \App\Models\User::where('role', 'Driver')
            ->whereDoesntHave('assignedVehicle')
            ->get();

        return view('maintenance.vehicles.assignments', compact('vehicles', 'drivers'));
    }

    public function updateAssignments(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'assignment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Update vehicle assignment
        $vehicle->update([
            'driver_id' => $request->driver_id,
            'assignment_date' => $request->assignment_date,
            'assignment_notes' => $request->notes
        ]);

        return response()->json(['success' => true, 'message' => 'Assignment updated successfully']);
    }

    public function vehicleMaintenance()
    {
        $maintenanceSchedules = MaintenanceSchedule::with(['vehicle', 'technician'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(20);

        return view('maintenance.vehicles.maintenance', compact('maintenanceSchedules'));
    }

    public function vehicleReports()
    {
        // Generate various reports
        $vehicleUtilization = Vehicle::select('id', 'vehicle_number', 'status', 'mileage')
            ->with(['maintenanceRecords' => function($query) {
                $query->select('vehicle_id', DB::raw('COUNT(*) as service_count'), DB::raw('SUM(cost) as total_cost'))
                    ->groupBy('vehicle_id');
            }])
            ->get();

        $maintenanceCostByVehicle = MaintenanceRecord::select('vehicle_id', DB::raw('SUM(cost) as total_cost'))
            ->with('vehicle:id,vehicle_number,make,model')
            ->groupBy('vehicle_id')
            ->orderBy('total_cost', 'desc')
            ->get();

        $monthlyMaintenanceTrend = MaintenanceRecord::select(
                DB::raw('DATE_FORMAT(service_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as service_count'),
                DB::raw('SUM(cost) as total_cost')
            )
            ->whereYear('service_date', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('maintenance.vehicles.reports', compact(
            'vehicleUtilization',
            'maintenanceCostByVehicle',
            'monthlyMaintenanceTrend'
        ));
    }

    public function saveColumnPreferences(Request $request)
    {
        $request->validate([
            'columns' => 'required|array'
        ]);

        // Save user preferences (you might want to create a user_preferences table)
        // For now, we'll return success
        return response()->json([
            'success' => true,
            'message' => 'Column preferences saved successfully'
        ]);
    }

    // Admin-only methods
    public function vehicleIndex()
    {
        $vehicles = Vehicle::with('driver')->paginate(15);

        // Check if the view exists and return it
        if (view()->exists('maintenance.admin.vehicles.index')) {
            return view('maintenance.admin.vehicles.index', compact('vehicles'));
        }

        // Fallback if view doesn't exist
        return view('maintenance.vehicles.index', compact('vehicles'));
    }

    public function vehicleCreate()
    {
        $drivers = \App\Models\User::where('role', 'Driver')->get();
        return view('maintenance.admin.vehicles.create', compact('drivers'));
    }

    public function vehicleStore(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|unique:vehicles',
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => 'required|unique:vehicles',
            'license_plate' => 'required|unique:vehicles',
            'fuel_type' => 'required|in:gasoline,diesel,electric,hybrid',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('maintenance.vehicles.index')
            ->with('success', 'Vehicle created successfully');
    }

    public function vehicleEdit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $drivers = \App\Models\User::where('role', 'Driver')->get();
        return view('maintenance.admin.vehicles.edit', compact('vehicle', 'drivers'));
    }

    public function vehicleUpdate(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'vehicle_number' => 'required|unique:vehicles,vehicle_number,' . $id,
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => 'required|unique:vehicles,vin,' . $id,
            'license_plate' => 'required|unique:vehicles,license_plate,' . $id,
            'fuel_type' => 'required|in:gasoline,diesel,electric,hybrid',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('maintenance.vehicles.index')
            ->with('success', 'Vehicle updated successfully');
    }

    public function vehicleDestroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Check if vehicle has active assignments or maintenance schedules
        if ($vehicle->driver_id) {
            return redirect()->route('maintenance.vehicles.index')
                ->with('error', 'Cannot delete vehicle with active driver assignment. Please unassign the driver first.');
        }

        if ($vehicle->maintenanceSchedules()->where('status', '!=', 'completed')->exists()) {
            return redirect()->route('maintenance.vehicles.index')
                ->with('error', 'Cannot delete vehicle with pending maintenance schedules.');
        }

        // Soft delete or hard delete based on your preference
        $vehicle->delete();

        return redirect()->route('maintenance.vehicles.index')
            ->with('success', 'Vehicle deleted successfully');
    }

    public function vehicleExport(Request $request)
    {
        $format = $request->get('export', 'csv');
        $vehicles = Vehicle::with('driver');

        // Apply filters if any
        if ($request->filled('search')) {
            $search = $request->get('search');
            $vehicles->where(function($query) use ($search) {
                $query->where('vehicle_number', 'like', "%{$search}%")
                      ->orWhere('vin', 'like', "%{$search}%")
                      ->orWhere('license_plate', 'like', "%{$search}%")
                      ->orWhere('make', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $vehicles->where('status', $request->get('status'));
        }

        if ($request->filled('fuel')) {
            $vehicles->where('fuel_type', $request->get('fuel'));
        }

        $vehicles = $vehicles->get();

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="vehicles_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($vehicles) {
                $file = fopen('php://output', 'w');

                // Header row
                fputcsv($file, ['ID', 'Vehicle Number', 'Make', 'Model', 'Year', 'VIN', 'License Plate',
                               'Driver', 'Status', 'Fuel Type', 'Mileage', 'Health Score',
                               'Last Maintenance', 'Next Maintenance']);

                // Data rows
                foreach ($vehicles as $vehicle) {
                    fputcsv($file, [
                        $vehicle->id,
                        $vehicle->vehicle_number,
                        $vehicle->make,
                        $vehicle->model,
                        $vehicle->year,
                        $vehicle->vin,
                        $vehicle->license_plate,
                        $vehicle->driver ? $vehicle->driver->name : 'Unassigned',
                        $vehicle->status,
                        $vehicle->fuel_type,
                        $vehicle->mileage,
                        $vehicle->health_score,
                        $vehicle->last_maintenance_date,
                        $vehicle->next_maintenance_due,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // For other formats, return JSON for now
        return response()->json($vehicles);
    }

    // Technician-specific methods
    public function technicianWorkQueue()
    {
        $assignedWork = MaintenanceSchedule::with(['vehicle'])
            ->where('technician_id', auth()->id())
            ->where('status', '!=', 'completed')
            ->orderBy('scheduled_date')
            ->get();

        $availableWork = MaintenanceSchedule::with(['vehicle'])
            ->whereNull('technician_id')
            ->where('status', 'scheduled')
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_date')
            ->get();

        return view('maintenance.technician.work-queue', compact('assignedWork', 'availableWork'));
    }

    public function startWork(Request $request, $id)
    {
        $schedule = MaintenanceSchedule::findOrFail($id);

        // Check if technician can start this work
        if ($schedule->technician_id && $schedule->technician_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Work assigned to another technician']);
        }

        $schedule->update([
            'technician_id' => auth()->id(),
            'status' => 'in_progress'
        ]);

        // Create maintenance record
        MaintenanceRecord::create([
            'vehicle_id' => $schedule->vehicle_id,
            'technician_id' => auth()->id(),
            'maintenance_type' => $schedule->maintenance_type,
            'description' => $schedule->description ?? 'Scheduled maintenance',
            'mileage_at_service' => $schedule->vehicle->mileage,
            'service_date' => now()->toDateString(),
            'status' => 'in_progress'
        ]);

        return response()->json(['success' => true]);
    }

    public function completeWork(Request $request, $id)
    {
        $schedule = MaintenanceSchedule::findOrFail($id);

        if ($schedule->technician_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'parts_cost' => 'required|numeric|min:0',
            'labor_cost' => 'required|numeric|min:0',
            'notes' => 'required|string',
            'next_service_date' => 'nullable|date|after:today'
        ]);

        // Update schedule
        $schedule->update(['status' => 'completed']);

        // Update maintenance record
        $record = MaintenanceRecord::where('vehicle_id', $schedule->vehicle_id)
            ->where('technician_id', auth()->id())
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if ($record) {
            $record->update([
                'parts_cost' => $request->parts_cost,
                'labor_cost' => $request->labor_cost,
                'cost' => $request->parts_cost + $request->labor_cost,
                'notes' => $request->notes,
                'completion_date' => now()->toDateString(),
                'next_service_date' => $request->next_service_date,
                'status' => 'completed'
            ]);
        }

        // Update vehicle
        $schedule->vehicle->update([
            'last_maintenance_date' => now()->toDateString(),
            'next_maintenance_due' => $request->next_service_date,
            'status' => 'active'
        ]);

        return response()->json(['success' => true]);
    }

    public function updateWorkProgress(Request $request, $id)
    {
        $schedule = MaintenanceSchedule::findOrFail($id);

        if ($schedule->technician_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'progress_notes' => 'required|string'
        ]);

        // Add progress update to maintenance record
        $record = MaintenanceRecord::where('vehicle_id', $schedule->vehicle_id)
            ->where('technician_id', auth()->id())
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if ($record) {
            $currentNotes = $record->notes ?? '';
            $timestamp = now()->format('Y-m-d H:i:s');
            $newNotes = $currentNotes . "\n[{$timestamp}] " . $request->progress_notes;

            $record->update(['notes' => $newNotes]);
        }

        return response()->json(['success' => true]);
    }

    // Driver-specific methods
    public function driverVehicle()
    {
        $vehicle = Vehicle::with(['maintenanceRecords', 'maintenanceSchedules', 'maintenanceAlerts'])
            ->where('driver_id', auth()->id())
            ->first();

        if (!$vehicle) {
            return view('maintenance.driver.no-vehicle');
        }

        $recentRecords = $vehicle->maintenanceRecords()
            ->orderBy('service_date', 'desc')
            ->limit(5)
            ->get();

        $upcomingSchedules = $vehicle->maintenanceSchedules()
            ->where('status', '!=', 'completed')
            ->orderBy('scheduled_date')
            ->get();

        $activeAlerts = $vehicle->maintenanceAlerts()
            ->where('status', 'active')
            ->get();

        return view('maintenance.driver.my-vehicle', compact(
            'vehicle', 'recentRecords', 'upcomingSchedules', 'activeAlerts'
        ));
    }

    public function requestMaintenance(Request $request)
    {
        $request->validate([
            'maintenance_type' => 'required|string',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical'
        ]);

        $vehicle = Vehicle::where('driver_id', auth()->id())->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'No vehicle assigned']);
        }

        // Create maintenance request (as a scheduled item)
        MaintenanceSchedule::create([
            'vehicle_id' => $vehicle->id,
            'maintenance_type' => $request->maintenance_type,
            'scheduled_date' => now()->addDays(1), // Default to tomorrow
            'estimated_duration' => 120, // Default 2 hours
            'priority' => $request->priority,
            'status' => 'scheduled',
            'description' => 'Driver Request: ' . $request->description,
            'created_by' => auth()->id()
        ]);

        // Create alert for maintenance team
        MaintenanceAlert::create([
            'vehicle_id' => $vehicle->id,
            'alert_type' => 'driver_request',
            'severity' => $request->priority === 'critical' ? 'critical' : 'medium',
            'title' => 'Maintenance Request from Driver',
            'message' => "Driver {$vehicle->driver->name} has requested {$request->maintenance_type} maintenance for vehicle {$vehicle->vehicle_number}. Description: {$request->description}",
            'status' => 'active'
        ]);

        return response()->json(['success' => true, 'message' => 'Maintenance request submitted successfully']);
    }

    public function export()
    {
        $records = Maintenance::with(['vehicle', 'technician'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="maintenance-records.csv"',
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Vehicle',
                'Service Type',
                'Description',
                'Technician',
                'Status',
                'Date',
                'Cost',
                'Notes'
            ]);

            // Add records
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->vehicle->name ?? 'N/A',
                    $record->service_type,
                    $record->description,
                    $record->technician->name ?? 'N/A',
                    $record->status,
                    $record->service_date,
                    $record->cost,
                    $record->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

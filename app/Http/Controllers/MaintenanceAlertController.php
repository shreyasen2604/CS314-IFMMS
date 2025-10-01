<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\MaintenanceAlert;
use App\Models\VehicleHealthMetric;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceAlertController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceAlert::with(['vehicle', 'acknowledgedBy']);

        // Apply filters
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('alert_type')) {
            $query->where('alert_type', $request->alert_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $alerts = $query->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $vehicles = Vehicle::all();
        $alertStats = $this->getAlertStatistics();

        return view('maintenance.alerts.index', compact('alerts', 'vehicles', 'alertStats'));
    }

    public function show($id)
    {
        $alert = MaintenanceAlert::with(['vehicle', 'acknowledgedBy', 'resolvedBy'])
            ->findOrFail($id);

        return response()->json($alert);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'alert_type' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'threshold_value' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
            'auto_resolve' => 'boolean'
        ]);

        $alert = MaintenanceAlert::create([
            'vehicle_id' => $request->vehicle_id,
            'alert_type' => $request->alert_type,
            'severity' => $request->severity,
            'title' => $request->title,
            'message' => $request->message,
            'threshold_value' => $request->threshold_value,
            'current_value' => $request->current_value,
            'status' => 'active',
            'auto_resolve' => $request->auto_resolve ?? false
        ]);

        // Send notifications based on severity
        $this->sendAlertNotifications($alert);

        return response()->json([
            'success' => true,
            'alert' => $alert->load(['vehicle']),
            'message' => 'Alert created successfully'
        ]);
    }

    public function acknowledge(Request $request, $id)
    {
        $alert = MaintenanceAlert::findOrFail($id);

        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_by' => auth()->id(),
            'acknowledged_at' => now(),
            'acknowledgment_notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'alert' => $alert->load(['vehicle', 'acknowledgedBy']),
            'message' => 'Alert acknowledged successfully'
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $alert = MaintenanceAlert::findOrFail($id);

        $request->validate([
            'resolution_notes' => 'required|string'
        ]);

        $alert->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'resolution_notes' => $request->resolution_notes
        ]);

        return response()->json([
            'success' => true,
            'alert' => $alert->load(['vehicle', 'resolvedBy']),
            'message' => 'Alert resolved successfully'
        ]);
    }

    public function bulkAcknowledge(Request $request)
    {
        $request->validate([
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'exists:maintenance_alerts,id',
            'notes' => 'nullable|string'
        ]);

        $updated = MaintenanceAlert::whereIn('id', $request->alert_ids)
            ->where('status', 'active')
            ->update([
                'status' => 'acknowledged',
                'acknowledged_by' => auth()->id(),
                'acknowledged_at' => now(),
                'acknowledgment_notes' => $request->notes
            ]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
            'message' => "{$updated} alerts acknowledged successfully"
        ]);
    }

    public function bulkResolve(Request $request)
    {
        $request->validate([
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'exists:maintenance_alerts,id',
            'resolution_notes' => 'required|string'
        ]);

        $updated = MaintenanceAlert::whereIn('id', $request->alert_ids)
            ->whereIn('status', ['active', 'acknowledged'])
            ->update([
                'status' => 'resolved',
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
                'resolution_notes' => $request->resolution_notes
            ]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
            'message' => "{$updated} alerts resolved successfully"
        ]);
    }

    public function createTestAlert(Request $request)
    {
        $vehicle = Vehicle::first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'No vehicles available for test alert'
            ]);
        }

        $alert = MaintenanceAlert::create([
            'vehicle_id' => $vehicle->id,
            'alert_type' => 'test',
            'severity' => 'medium',
            'title' => 'Test Alert',
            'message' => 'This is a test alert created at ' . now()->format('Y-m-d H:i:s'),
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'alert' => $alert->load(['vehicle']),
            'message' => 'Test alert created successfully'
        ]);
    }

    public function configureThresholds(Request $request)
    {
        $request->validate([
            'critical_health_score' => 'required|numeric|min:0|max:100',
            'warning_health_score' => 'required|numeric|min:0|max:100',
            'maintenance_due_mileage' => 'required|numeric|min:0',
            'overdue_warning_mileage' => 'required|numeric|min:0',
            'maintenance_due_days' => 'required|numeric|min:0',
            'overdue_warning_days' => 'required|numeric|min:0',
            'monthly_cost_limit' => 'required|numeric|min:0',
            'service_cost_limit' => 'required|numeric|min:0',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'dashboard_notifications' => 'boolean'
        ]);

        // Store configuration (in a real app, this would go to a settings table)
        $config = $request->all();
        
        // For now, we'll store in cache or session
        cache()->put('maintenance_alert_config', $config, now()->addYear());

        return response()->json([
            'success' => true,
            'message' => 'Alert thresholds configured successfully'
        ]);
    }

    public function getConfiguration()
    {
        $config = cache()->get('maintenance_alert_config', [
            'critical_health_score' => 40,
            'warning_health_score' => 60,
            'maintenance_due_mileage' => 5000,
            'overdue_warning_mileage' => 1000,
            'maintenance_due_days' => 90,
            'overdue_warning_days' => 7,
            'monthly_cost_limit' => 10000,
            'service_cost_limit' => 2000,
            'email_notifications' => true,
            'sms_notifications' => false,
            'dashboard_notifications' => true
        ]);

        return response()->json($config);
    }

    public function generateSystemAlerts()
    {
        $alertsGenerated = 0;

        // Check for overdue maintenance
        $overdueVehicles = Vehicle::whereDate('next_maintenance_due', '<', now())
            ->whereDoesntHave('maintenanceAlerts', function($query) {
                $query->where('alert_type', 'overdue_maintenance')
                      ->where('status', 'active');
            })
            ->get();

        foreach ($overdueVehicles as $vehicle) {
            $this->createOverdueMaintenanceAlert($vehicle);
            $alertsGenerated++;
        }

        // Check for low health scores
        $unhealthyVehicles = Vehicle::where('health_score', '<', 60)
            ->whereDoesntHave('maintenanceAlerts', function($query) {
                $query->where('alert_type', 'low_health_score')
                      ->where('status', 'active');
            })
            ->get();

        foreach ($unhealthyVehicles as $vehicle) {
            $this->createLowHealthScoreAlert($vehicle);
            $alertsGenerated++;
        }

        // Check for high mileage without recent maintenance
        $highMileageVehicles = Vehicle::where('mileage', '>', 100000)
            ->where(function($query) {
                $query->whereNull('last_maintenance_date')
                      ->orWhere('last_maintenance_date', '<', now()->subMonths(6));
            })
            ->whereDoesntHave('maintenanceAlerts', function($query) {
                $query->where('alert_type', 'high_mileage_maintenance')
                      ->where('status', 'active');
            })
            ->get();

        foreach ($highMileageVehicles as $vehicle) {
            $this->createHighMileageAlert($vehicle);
            $alertsGenerated++;
        }

        return response()->json([
            'success' => true,
            'alerts_generated' => $alertsGenerated,
            'message' => "{$alertsGenerated} system alerts generated"
        ]);
    }

    public function getAlertTrends(Request $request)
    {
        $period = $request->get('period', 30); // days
        $startDate = now()->subDays($period);

        $trends = MaintenanceAlert::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_alerts'),
            DB::raw('SUM(CASE WHEN severity = "critical" THEN 1 ELSE 0 END) as critical_alerts'),
            DB::raw('SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved_alerts')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json($trends);
    }

    public function getVehicleAlerts($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        
        $alerts = MaintenanceAlert::where('vehicle_id', $vehicleId)
            ->with(['acknowledgedBy', 'resolvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_alerts' => $alerts->count(),
            'active_alerts' => $alerts->where('status', 'active')->count(),
            'critical_alerts' => $alerts->where('severity', 'critical')->count(),
            'avg_resolution_time' => $this->calculateAverageResolutionTime($alerts)
        ];

        return response()->json([
            'vehicle' => $vehicle,
            'alerts' => $alerts,
            'summary' => $summary
        ]);
    }

    private function getAlertStatistics()
    {
        return [
            'total' => MaintenanceAlert::count(),
            'active' => MaintenanceAlert::where('status', 'active')->count(),
            'critical' => MaintenanceAlert::where('severity', 'critical')->count(),
            'acknowledged' => MaintenanceAlert::where('status', 'acknowledged')->count(),
            'resolved' => MaintenanceAlert::where('status', 'resolved')->count(),
            'today' => MaintenanceAlert::whereDate('created_at', today())->count()
        ];
    }

    private function sendAlertNotifications($alert)
    {
        // In a real application, this would send emails, SMS, push notifications, etc.
        // For now, we'll just log the notification
        
        $config = cache()->get('maintenance_alert_config', []);
        
        if ($config['email_notifications'] ?? true) {
            // Send email notification
            \Log::info("Email notification sent for alert: {$alert->title}");
        }
        
        if ($config['sms_notifications'] ?? false) {
            // Send SMS notification
            \Log::info("SMS notification sent for alert: {$alert->title}");
        }
        
        if ($config['dashboard_notifications'] ?? true) {
            // Create dashboard notification
            \Log::info("Dashboard notification created for alert: {$alert->title}");
        }
    }

    private function createOverdueMaintenanceAlert($vehicle)
    {
        $daysPastDue = $vehicle->next_maintenance_due->diffInDays(now());
        
        MaintenanceAlert::create([
            'vehicle_id' => $vehicle->id,
            'alert_type' => 'overdue_maintenance',
            'severity' => $daysPastDue > 30 ? 'critical' : 'high',
            'title' => 'Overdue Maintenance',
            'message' => "Vehicle {$vehicle->vehicle_number} is {$daysPastDue} days overdue for maintenance",
            'status' => 'active'
        ]);
    }

    private function createLowHealthScoreAlert($vehicle)
    {
        $severity = $vehicle->health_score < 40 ? 'critical' : 'high';
        
        MaintenanceAlert::create([
            'vehicle_id' => $vehicle->id,
            'alert_type' => 'low_health_score',
            'severity' => $severity,
            'title' => 'Low Vehicle Health Score',
            'message' => "Vehicle {$vehicle->vehicle_number} has a health score of {$vehicle->health_score}%",
            'current_value' => $vehicle->health_score,
            'threshold_value' => 60,
            'status' => 'active'
        ]);
    }

    private function createHighMileageAlert($vehicle)
    {
        MaintenanceAlert::create([
            'vehicle_id' => $vehicle->id,
            'alert_type' => 'high_mileage_maintenance',
            'severity' => 'medium',
            'title' => 'High Mileage Maintenance Required',
            'message' => "Vehicle {$vehicle->vehicle_number} has high mileage ({$vehicle->mileage} miles) and needs maintenance",
            'current_value' => $vehicle->mileage,
            'status' => 'active'
        ]);
    }

    private function calculateAverageResolutionTime($alerts)
    {
        $resolvedAlerts = $alerts->where('status', 'resolved')
            ->whereNotNull('resolved_at');

        if ($resolvedAlerts->isEmpty()) {
            return null;
        }

        $totalHours = $resolvedAlerts->sum(function($alert) {
            return $alert->created_at->diffInHours($alert->resolved_at);
        });

        return round($totalHours / $resolvedAlerts->count(), 2);
    }
}
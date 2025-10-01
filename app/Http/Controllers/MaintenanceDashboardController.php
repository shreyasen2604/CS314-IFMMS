<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceAlert;
use App\Models\VehicleHealthMetric;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceDashboardController extends Controller
{
    public function index()
    {
        // Fleet Overview Statistics
        $fleetStats = $this->getFleetStatistics();
        
        // Health Distribution
        $healthDistribution = $this->getHealthDistribution();
        
        // Recent Activities
        $recentActivities = $this->getRecentActivities();
        
        // Upcoming Maintenance
        $upcomingMaintenance = $this->getUpcomingMaintenance();
        
        // Critical Alerts
        $criticalAlerts = $this->getCriticalAlerts();
        
        // Cost Analysis
        $costAnalysis = $this->getCostAnalysis();
        
        // Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('maintenance.dashboard', compact(
            'fleetStats',
            'healthDistribution',
            'recentActivities',
            'upcomingMaintenance',
            'criticalAlerts',
            'costAnalysis',
            'performanceMetrics'
        ));
    }

    private function getFleetStatistics()
    {
        return [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::where('status', 'active')->count(),
            'vehicles_in_maintenance' => Vehicle::where('status', 'maintenance')->count(),
            'overdue_maintenance' => Vehicle::whereDate('next_maintenance_due', '<', now())->count(),
            'due_soon' => Vehicle::whereBetween('next_maintenance_due', [
                now()->toDateString(),
                now()->addDays(7)->toDateString()
            ])->count(),
            'active_alerts' => MaintenanceAlert::where('status', 'active')->count(),
            'critical_alerts' => MaintenanceAlert::where('status', 'active')
                ->where('severity', 'critical')->count(),
            'scheduled_today' => MaintenanceSchedule::whereDate('scheduled_date', today())
                ->where('status', '!=', 'completed')->count(),
            'avg_health_score' => Vehicle::avg('health_score') ?? 100,
            'monthly_cost' => MaintenanceRecord::whereMonth('service_date', now()->month)
                ->whereYear('service_date', now()->year)
                ->sum('cost') ?? 0
        ];
    }

    private function getHealthDistribution()
    {
        return [
            'excellent' => Vehicle::where('health_score', '>=', 80)->count(),
            'good' => Vehicle::whereBetween('health_score', [60, 79])->count(),
            'fair' => Vehicle::whereBetween('health_score', [40, 59])->count(),
            'poor' => Vehicle::where('health_score', '<', 40)->count(),
        ];
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent maintenance records
        $recentMaintenance = MaintenanceRecord::with(['vehicle', 'technician'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($record) {
                return [
                    'type' => 'maintenance_completed',
                    'title' => "Maintenance completed on {$record->vehicle->vehicle_number}",
                    'description' => $record->maintenance_type,
                    'timestamp' => $record->updated_at,
                    'icon' => 'wrench',
                    'color' => 'success'
                ];
            });

        // Recent alerts
        $recentAlerts = MaintenanceAlert::with('vehicle')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($alert) {
                return [
                    'type' => 'alert_created',
                    'title' => $alert->title,
                    'description' => "Vehicle: {$alert->vehicle->vehicle_number}",
                    'timestamp' => $alert->created_at,
                    'icon' => 'exclamation-triangle',
                    'color' => $alert->severity === 'critical' ? 'danger' : 'warning'
                ];
            });

        return $activities->merge($recentMaintenance)
            ->merge($recentAlerts)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values();
    }

    private function getUpcomingMaintenance()
    {
        return MaintenanceSchedule::with(['vehicle', 'technician'])
            ->where('status', '!=', 'completed')
            ->whereDate('scheduled_date', '>=', today())
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get();
    }

    private function getCriticalAlerts()
    {
        return MaintenanceAlert::with('vehicle')
            ->where('status', 'active')
            ->where('severity', 'critical')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getCostAnalysis()
    {
        $currentMonth = now();
        $lastMonth = now()->subMonth();

        return [
            'current_month' => MaintenanceRecord::whereMonth('service_date', $currentMonth->month)
                ->whereYear('service_date', $currentMonth->year)
                ->sum('cost') ?? 0,
            'last_month' => MaintenanceRecord::whereMonth('service_date', $lastMonth->month)
                ->whereYear('service_date', $lastMonth->year)
                ->sum('cost') ?? 0,
            'ytd_total' => MaintenanceRecord::whereYear('service_date', now()->year)
                ->sum('cost') ?? 0,
            'avg_per_vehicle' => MaintenanceRecord::whereYear('service_date', now()->year)
                ->avg('cost') ?? 0,
            'cost_by_type' => MaintenanceRecord::select('maintenance_type', DB::raw('SUM(cost) as total_cost'))
                ->whereYear('service_date', now()->year)
                ->groupBy('maintenance_type')
                ->orderBy('total_cost', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    private function getPerformanceMetrics()
    {
        $thirtyDaysAgo = now()->subDays(30);

        return [
            'completion_rate' => $this->calculateCompletionRate(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
            'preventive_vs_corrective' => $this->getPreventiveVsCorrective(),
            'technician_efficiency' => $this->getTechnicianEfficiency(),
            'vehicle_availability' => $this->calculateVehicleAvailability()
        ];
    }

    private function calculateCompletionRate()
    {
        $total = MaintenanceSchedule::whereMonth('scheduled_date', now()->month)->count();
        $completed = MaintenanceSchedule::whereMonth('scheduled_date', now()->month)
            ->where('status', 'completed')->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 100;
    }

    private function calculateAverageResponseTime()
    {
        // Mock calculation - would be based on actual alert response times
        return rand(2, 8) . ' hours';
    }

    private function getPreventiveVsCorrective()
    {
        $preventive = MaintenanceRecord::whereYear('service_date', now()->year)
            ->where('maintenance_type', 'LIKE', '%preventive%')->count();
        $corrective = MaintenanceRecord::whereYear('service_date', now()->year)
            ->where('maintenance_type', 'LIKE', '%corrective%')->count();

        return [
            'preventive' => $preventive,
            'corrective' => $corrective,
            'ratio' => $corrective > 0 ? round($preventive / $corrective, 2) : 0
        ];
    }

    private function getTechnicianEfficiency()
    {
        // Mock data - would calculate based on actual work completion times
        return [
            'avg_efficiency' => 92,
            'top_performer' => 'John Smith',
            'improvement_needed' => 2
        ];
    }

    private function calculateVehicleAvailability()
    {
        $total = Vehicle::count();
        $available = Vehicle::where('status', 'active')->count();

        return $total > 0 ? round(($available / $total) * 100, 2) : 100;
    }

    // API endpoints for real-time updates
    public function getFleetStatsApi()
    {
        return response()->json($this->getFleetStatistics());
    }

    public function getHealthDistributionApi()
    {
        return response()->json($this->getHealthDistribution());
    }

    public function getCostAnalysisApi()
    {
        return response()->json($this->getCostAnalysis());
    }
}
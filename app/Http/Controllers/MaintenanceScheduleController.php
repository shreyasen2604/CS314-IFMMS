<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceType;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceSchedule::with(['vehicle', 'technician']);

        // Apply filters
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Get all schedules for display (no pagination for calendar/list view)
        $schedules = $query->orderBy('scheduled_date')->get();
        
        $vehicles = Vehicle::where('status', '!=', 'retired')->get();
        $technicians = User::where('role', 'Technician')->get();
        
        // Check if MaintenanceType model exists and has active scope
        try {
            $maintenanceTypes = MaintenanceType::active()->get();
        } catch (\Exception $e) {
            // If MaintenanceType doesn't exist or doesn't have active scope, use empty collection
            $maintenanceTypes = collect([]);
        }

        // Use simplified view for debugging
        return view('maintenance.scheduler-simple', compact(
            'schedules', 'vehicles', 'technicians', 'maintenanceTypes'
        ));
    }

    public function calendar(Request $request)
    {
        $schedules = MaintenanceSchedule::with(['vehicle', 'technician'])
            ->where('status', '!=', 'completed')
            ->get();

        $vehicles = Vehicle::where('status', '!=', 'retired')->get();
        $technicians = User::where('role', 'Technician')->get();
        
        // Check if MaintenanceType model exists and has active scope
        try {
            $maintenanceTypes = MaintenanceType::active()->get();
        } catch (\Exception $e) {
            // If MaintenanceType doesn't exist or doesn't have active scope, use empty collection
            $maintenanceTypes = collect([]);
        }

        return view('maintenance.scheduler', compact(
            'schedules', 'vehicles', 'technicians', 'maintenanceTypes'
        ));
    }

    public function store(Request $request)
    {
        // Handle both JSON and form data
        if ($request->isJson() || $request->wantsJson()) {
            $data = $request->json()->all();
        } else {
            $data = $request->all();
        }
        
        $validator = \Validator::make($data, [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type' => 'required|string',
            'scheduled_date' => 'required|date',
            'estimated_duration' => 'required|integer|min:15',
            'priority' => 'required|in:low,medium,high,critical',
            'technician_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'recurring' => 'boolean',
            'recurring_type' => 'nullable|required_if:recurring,true|in:daily,weekly,monthly,yearly',
            'recurring_interval' => 'nullable|required_if:recurring,true|integer|min:1',
            'recurring_end_date' => 'nullable|date|after:scheduled_date'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $scheduleData = [
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'technician_id' => $data['technician_id'] ?? null,
            'maintenance_type' => $data['maintenance_type'] ?? null,
            'scheduled_date' => $data['scheduled_date'] ?? null,
            'estimated_duration' => $data['estimated_duration'] ?? null,
            'priority' => $data['priority'] ?? null,
            'description' => $data['description'] ?? null
        ];
        
        $scheduleData['status'] = 'scheduled';
        $scheduleData['created_by'] = auth()->id();

        // Create main schedule
        $schedule = MaintenanceSchedule::create($scheduleData);

        // Handle recurring schedules
        if (isset($data['recurring']) && $data['recurring']) {
            $this->createRecurringSchedules($schedule, $data);
        }

        // Generate automatic recommendations
        $this->generateAutomaticRecommendations($schedule->vehicle_id);

        return response()->json([
            'success' => true,
            'schedule' => $schedule->load(['vehicle', 'technician']),
            'message' => 'Maintenance scheduled successfully'
        ]);
    }

    public function show($id)
    {
        $schedule = MaintenanceSchedule::with(['vehicle', 'technician', 'createdBy'])
            ->findOrFail($id);

        return response()->json($schedule);
    }

    public function update(Request $request, $id)
    {
        $schedule = MaintenanceSchedule::findOrFail($id);

        $request->validate([
            'scheduled_date' => 'required|date',
            'estimated_duration' => 'required|integer|min:15',
            'priority' => 'required|in:low,medium,high,critical',
            'technician_id' => 'nullable|exists:users,id',
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled',
            'description' => 'nullable|string'
        ]);

        $schedule->update($request->only([
            'scheduled_date', 'estimated_duration', 'priority',
            'technician_id', 'status', 'description'
        ]));

        return response()->json([
            'success' => true,
            'schedule' => $schedule->load(['vehicle', 'technician']),
            'message' => 'Schedule updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $schedule = MaintenanceSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function updateDate(Request $request, $id)
    {
        $request->validate([
            'scheduled_date' => 'required|date'
        ]);

        $schedule = MaintenanceSchedule::findOrFail($id);
        $schedule->update(['scheduled_date' => $request->scheduled_date]);

        return response()->json([
            'success' => true,
            'schedule' => $schedule->load(['vehicle', 'technician'])
        ]);
    }

    public function bulkSchedule(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'maintenance_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'estimated_duration' => 'required|integer|min:15',
            'priority' => 'required|in:low,medium,high,critical',
            'interval_days' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        $schedules = [];
        $currentDate = Carbon::parse($request->start_date);

        foreach ($request->vehicle_ids as $vehicleId) {
            $schedules[] = MaintenanceSchedule::create([
                'vehicle_id' => $vehicleId,
                'maintenance_type' => $request->maintenance_type,
                'scheduled_date' => $currentDate->copy(),
                'estimated_duration' => $request->estimated_duration,
                'priority' => $request->priority,
                'description' => $request->description,
                'status' => 'scheduled',
                'created_by' => auth()->id()
            ]);

            $currentDate->addDays($request->interval_days);
        }

        return response()->json([
            'success' => true,
            'schedules' => $schedules,
            'message' => count($schedules) . ' maintenance schedules created'
        ]);
    }

    public function getCalendarEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $schedules = MaintenanceSchedule::with(['vehicle', 'technician'])
            ->whereBetween('scheduled_date', [$start, $end])
            ->get();

        $events = $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->vehicle->vehicle_number . ' - ' . $schedule->maintenance_type,
                'start' => $schedule->scheduled_date->toISOString(),
                'end' => $schedule->scheduled_date->addMinutes($schedule->estimated_duration)->toISOString(),
                'backgroundColor' => $this->getPriorityColor($schedule->priority),
                'borderColor' => $this->getPriorityColor($schedule->priority),
                'extendedProps' => [
                    'vehicle' => $schedule->vehicle->vehicle_number,
                    'technician' => $schedule->technician->name ?? 'Unassigned',
                    'priority' => $schedule->priority,
                    'status' => $schedule->status,
                    'description' => $schedule->description
                ]
            ];
        });

        return response()->json($events);
    }

    public function getRecommendations($vehicleId)
    {
        $vehicle = Vehicle::with(['maintenanceRecords', 'healthMetrics'])->findOrFail($vehicleId);
        
        $recommendations = [];

        // Mileage-based recommendations
        if ($vehicle->mileage > 0) {
            $lastOilChange = $vehicle->maintenanceRecords()
                ->where('maintenance_type', 'oil_change')
                ->latest('service_date')
                ->first();

            if (!$lastOilChange || ($vehicle->mileage - $lastOilChange->mileage_at_service) > 5000) {
                $recommendations[] = [
                    'type' => 'oil_change',
                    'priority' => 'medium',
                    'reason' => 'Due for oil change based on mileage',
                    'suggested_date' => now()->addDays(7)
                ];
            }
        }

        // Time-based recommendations
        if ($vehicle->last_maintenance_date && $vehicle->last_maintenance_date->diffInMonths(now()) > 6) {
            $recommendations[] = [
                'type' => 'general_inspection',
                'priority' => 'high',
                'reason' => 'No maintenance in over 6 months',
                'suggested_date' => now()->addDays(3)
            ];
        }

        // Health metric-based recommendations
        $criticalMetrics = $vehicle->healthMetrics()
            ->where('status', 'critical')
            ->recent(30)
            ->get();

        foreach ($criticalMetrics as $metric) {
            $recommendations[] = [
                'type' => 'diagnostic_check',
                'priority' => 'critical',
                'reason' => "Critical {$metric->metric_type} reading: {$metric->metric_value}",
                'suggested_date' => now()->addDays(1)
            ];
        }

        return response()->json($recommendations);
    }

    private function createRecurringSchedules($baseSchedule, $data)
    {
        $currentDate = Carbon::parse($baseSchedule->scheduled_date);
        $endDate = isset($data['recurring_end_date']) ? Carbon::parse($data['recurring_end_date']) : $currentDate->copy()->addYear();
        $interval = $data['recurring_interval'] ?? 1;

        while ($currentDate->lt($endDate)) {
            switch ($data['recurring_type'] ?? 'monthly') {
                case 'daily':
                    $currentDate->addDays($interval);
                    break;
                case 'weekly':
                    $currentDate->addWeeks($interval);
                    break;
                case 'monthly':
                    $currentDate->addMonths($interval);
                    break;
                case 'yearly':
                    $currentDate->addYears($interval);
                    break;
            }

            if ($currentDate->lte($endDate)) {
                MaintenanceSchedule::create([
                    'vehicle_id' => $baseSchedule->vehicle_id,
                    'technician_id' => $baseSchedule->technician_id,
                    'maintenance_type' => $baseSchedule->maintenance_type,
                    'scheduled_date' => $currentDate->copy(),
                    'estimated_duration' => $baseSchedule->estimated_duration,
                    'priority' => $baseSchedule->priority,
                    'description' => $baseSchedule->description,
                    'status' => 'scheduled',
                    'created_by' => auth()->id(),
                    'parent_schedule_id' => $baseSchedule->id
                ]);
            }
        }
    }

    private function generateAutomaticRecommendations($vehicleId)
    {
        // This would analyze vehicle data and create recommended future schedules
        // Implementation would depend on business rules
    }

    private function getPriorityColor($priority)
    {
        return match($priority) {
            'critical' => '#dc3545',
            'high' => '#fd7e14',
            'medium' => '#ffc107',
            'low' => '#28a745',
            default => '#6c757d'
        };
    }
}
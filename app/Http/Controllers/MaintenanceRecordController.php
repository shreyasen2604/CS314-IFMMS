<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['vehicle', 'technician']);

        // Apply filters
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
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

        if ($request->filled('cost_min')) {
            $query->where('cost', '>=', $request->cost_min);
        }

        if ($request->filled('cost_max')) {
            $query->where('cost', '<=', $request->cost_max);
        }

        $records = $query->orderBy('service_date', 'desc')->paginate(15);

        $vehicles = Vehicle::all();
        $technicians = User::where('role', 'Technician')->get();
        $totalCost = $query->sum('cost');

        // Statistics
        $statistics = $this->getRecordStatistics($request);

        return view('maintenance.records', compact(
            'records', 'vehicles', 'technicians', 'totalCost', 'statistics'
        ));
    }

    public function edit($id)
    {
        $record = MaintenanceRecord::findOrFail($id);
        $vehicles = Vehicle::all();
        $technicians = User::where('role', 'Technician')->get();

        return view('maintenance.records.edit', compact('record', 'vehicles', 'technicians'));
    }

    public function show($id)
    {
        $record = MaintenanceRecord::with(['vehicle', 'technician', 'schedule'])
            ->findOrFail($id);

        // Append the total_cost accessor to the JSON response
        $record->append('total_cost');

        return response()->json($record);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'technician_id' => 'required|exists:users,id',
            'maintenance_type' => 'required|string',
            'service_date' => 'required|date',
            'description' => 'required|string',
            'mileage_at_service' => 'required|integer|min:0',
            'parts_cost' => 'required|numeric|min:0',
            'labor_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'next_service_date' => 'nullable|date|after:service_date',
            'schedule_id' => 'nullable|exists:maintenance_schedules,id'
        ]);

        $recordData = $request->all();
        $recordData['cost'] = $request->parts_cost + $request->labor_cost;
        $recordData['status'] = 'completed';

        $record = MaintenanceRecord::create($recordData);

        // Update vehicle information
        $this->updateVehicleAfterMaintenance($record);

        // Update related schedule if exists
        if ($request->schedule_id) {
            MaintenanceSchedule::where('id', $request->schedule_id)
                ->update(['status' => 'completed']);
        }

        return response()->json([
            'success' => true,
            'record' => $record->load(['vehicle', 'technician']),
            'message' => 'Maintenance record created successfully'
        ]);
    }

    public function update(Request $request, $id)
    {
        $record = MaintenanceRecord::findOrFail($id);

        $request->validate([
            'maintenance_type' => 'required|string',
            'service_date' => 'required|date',
            'description' => 'required|string',
            'mileage_at_service' => 'required|integer|min:0',
            'parts_cost' => 'required|numeric|min:0',
            'labor_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'next_service_date' => 'nullable|date|after:service_date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $updateData = $request->all();
        $updateData['cost'] = $request->parts_cost + $request->labor_cost;

        $record->update($updateData);

        // Update vehicle if record is completed
        if ($request->status === 'completed') {
            $this->updateVehicleAfterMaintenance($record);
        }

        return response()->json([
            'success' => true,
            'record' => $record->load(['vehicle', 'technician']),
            'message' => 'Maintenance record updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $record = MaintenanceRecord::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maintenance record deleted successfully'
        ]);
    }

    public function export(Request $request)
    {
        $query = MaintenanceRecord::with(['vehicle', 'technician']);

        // Apply same filters as index
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('service_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('service_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('service_date', 'desc')->get();

        $filename = 'maintenance_records_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Date', 'Vehicle', 'Technician', 'Type', 'Description',
                'Mileage', 'Parts Cost', 'Labor Cost', 'Total Cost',
                'Status', 'Next Service Date', 'Notes'
            ]);

            // CSV data
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->service_date->format('Y-m-d'),
                    $record->vehicle->vehicle_number,
                    $record->technician->name ?? 'N/A',
                    $record->maintenance_type,
                    $record->description,
                    $record->mileage_at_service,
                    $record->parts_cost,
                    $record->labor_cost,
                    $record->cost,
                    $record->status,
                    $record->next_service_date ? $record->next_service_date->format('Y-m-d') : '',
                    $record->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getCostAnalysis(Request $request)
    {
        $period = $request->get('period', 'month'); // month, quarter, year
        $vehicleId = $request->get('vehicle_id');

        $query = MaintenanceRecord::query();

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        switch ($period) {
            case 'month':
                $data = $query->select(
                    DB::raw('YEAR(service_date) as year'),
                    DB::raw('MONTH(service_date) as month'),
                    DB::raw('SUM(cost) as total_cost'),
                    DB::raw('COUNT(*) as service_count')
                )
                ->whereYear('service_date', '>=', now()->subYear())
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
                break;

            case 'quarter':
                $data = $query->select(
                    DB::raw('YEAR(service_date) as year'),
                    DB::raw('QUARTER(service_date) as quarter'),
                    DB::raw('SUM(cost) as total_cost'),
                    DB::raw('COUNT(*) as service_count')
                )
                ->whereYear('service_date', '>=', now()->subYears(2))
                ->groupBy('year', 'quarter')
                ->orderBy('year', 'desc')
                ->orderBy('quarter', 'desc')
                ->get();
                break;

            case 'year':
                $data = $query->select(
                    DB::raw('YEAR(service_date) as year'),
                    DB::raw('SUM(cost) as total_cost'),
                    DB::raw('COUNT(*) as service_count')
                )
                ->whereYear('service_date', '>=', now()->subYears(5))
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get();
                break;
        }

        return response()->json($data);
    }

    public function getMaintenanceHistory($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $history = MaintenanceRecord::with('technician')
            ->where('vehicle_id', $vehicleId)
            ->orderBy('service_date', 'desc')
            ->get();

        $summary = [
            'total_records' => $history->count(),
            'total_cost' => $history->sum('cost'),
            'avg_cost' => $history->avg('cost'),
            'last_service' => $history->first()?->service_date,
            'most_common_type' => $history->groupBy('maintenance_type')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first()
        ];

        return response()->json([
            'vehicle' => $vehicle,
            'history' => $history,
            'summary' => $summary
        ]);
    }

    private function getRecordStatistics($request)
    {
        $query = MaintenanceRecord::query();

        // Apply same filters as main query
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('service_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('service_date', '<=', $request->date_to);
        }

        return [
            'total_records' => $query->count(),
            'total_cost' => $query->sum('cost'),
            'avg_cost' => $query->avg('cost'),
            'completed_count' => $query->where('status', 'completed')->count(),
            'in_progress_count' => $query->where('status', 'in_progress')->count(),
            'preventive_count' => $query->where('maintenance_type', 'LIKE', '%preventive%')->count(),
            'corrective_count' => $query->where('maintenance_type', 'LIKE', '%corrective%')->count(),
        ];
    }

    private function updateVehicleAfterMaintenance($record)
    {
        $vehicle = $record->vehicle;

        $updateData = [
            'last_maintenance_date' => $record->service_date,
            'mileage' => max($vehicle->mileage, $record->mileage_at_service)
        ];

        if ($record->next_service_date) {
            $updateData['next_maintenance_due'] = $record->next_service_date;
        }

        // Update health score based on maintenance
        $updateData['health_score'] = $vehicle->calculateHealthScore();

        $vehicle->update($updateData);
    }
}

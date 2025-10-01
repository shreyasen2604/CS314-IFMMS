<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $vehicles = Vehicle::with('driver')->paginate($perPage);
        return view('maintenance.admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $drivers = User::where('role', 'Driver')->get();
        return view('maintenance.admin.vehicles.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number',
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer',
            'mileage' => 'required|integer|min:0',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('maintenance.vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $drivers = User::where('role', 'Driver')->get();
        return view('maintenance.admin.vehicles.edit', compact('vehicle', 'drivers'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number,' . $vehicle->id,
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer',
            'mileage' => 'required|integer|min:0',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('maintenance.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return redirect()->route('maintenance.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $vehicles = Vehicle::where('vehicle_number', 'LIKE', "%{$query}%")
                          ->orWhere('make', 'LIKE', "%{$query}%")
                          ->orWhere('model', 'LIKE', "%{$query}%")
                          ->get();
        
        return response()->json($vehicles);
    }

    public function filter(Request $request)
    {
        $query = Vehicle::with('driver');

        // Basic filters
        if ($request->filled('make')) {
            $query->where('make', 'LIKE', '%' . $request->make . '%');
        }

        if ($request->filled('model')) {
            $query->where('model', 'LIKE', '%' . $request->model . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Advanced filters
        if ($request->filled('year_from')) {
            $query->where('year', '>=', $request->year_from);
        }

        if ($request->filled('year_to')) {
            $query->where('year', '<=', $request->year_to);
        }

        if ($request->filled('mileage_min')) {
            $query->where('mileage', '>=', $request->mileage_min);
        }

        if ($request->filled('mileage_max')) {
            $query->where('mileage', '<=', $request->mileage_max);
        }

        if ($request->filled('health_min')) {
            $query->where('health_score', '>=', $request->health_min);
        }

        if ($request->filled('health_max')) {
            $query->where('health_score', '<=', $request->health_max);
        }

        if ($request->filled('last_service_from')) {
            $query->where('last_maintenance_date', '>=', $request->last_service_from);
        }

        if ($request->filled('last_service_to')) {
            $query->where('last_maintenance_date', '<=', $request->last_service_to);
        }

        if ($request->filled('service_due')) {
            $now = now();
            switch ($request->service_due) {
                case 'overdue':
                    $query->where('next_maintenance_due', '<', $now);
                    break;
                case 'week':
                    $query->whereBetween('next_maintenance_due', [$now, $now->copy()->addWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('next_maintenance_due', [$now, $now->copy()->addMonth()]);
                    break;
                case 'quarter':
                    $query->whereBetween('next_maintenance_due', [$now, $now->copy()->addMonths(3)]);
                    break;
            }
        }

        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('driver_id');
            } elseif ($request->assignment === 'unassigned') {
                $query->whereNull('driver_id');
            }
        }

        if ($request->filled('department')) {
            $departments = is_array($request->department) ? $request->department : [$request->department];
            $query->whereIn('department', $departments);
        }

        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }

        if ($request->filled('vin')) {
            $query->where('vin', 'LIKE', '%' . $request->vin . '%');
        }

        if ($request->filled('license_plate')) {
            $query->where('license_plate', 'LIKE', '%' . $request->license_plate . '%');
        }

        $vehicles = $query->get();

        // If this is an AJAX request, return partial view
        if ($request->ajax()) {
            return view('maintenance.admin.vehicles.partials.table-rows', compact('vehicles'))->render();
        }

        return response()->json($vehicles);
    }

    public function export()
    {
        $vehicles = Vehicle::all();
        
        $csvData = "Vehicle Number,Make,Model,Year,Mileage,Status\n";
        foreach ($vehicles as $vehicle) {
            $csvData .= "{$vehicle->vehicle_number},{$vehicle->make},{$vehicle->model},{$vehicle->year},{$vehicle->mileage},{$vehicle->status}\n";
        }

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vehicles_export.csv"'
        ]);
    }

    public function assignments()
    {
        $vehicles = Vehicle::with('assignedUser')->get();
        $users = User::where('role', 'Driver')->get();
        
        return view('maintenance.admin.vehicles.assignments', compact('vehicles', 'users'));
    }

    public function updateAssignments(Request $request)
    {
        $assignments = $request->input('assignments', []);
        
        foreach ($assignments as $vehicleId => $userId) {
            $vehicle = Vehicle::find($vehicleId);
            if ($vehicle) {
                $vehicle->driver_id = $userId ?: null;
                $vehicle->save();
            }
        }

        return redirect()->route('maintenance.vehicles.assignments')
                        ->with('success', 'Vehicle assignments updated successfully.');
    }
}

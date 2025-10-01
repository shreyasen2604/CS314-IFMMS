<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteCheckpoint;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::with(['creator', 'assignments.driver', 'checkpoints']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('route_type')) {
            $query->where('route_type', $request->route_type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('route_name', 'LIKE', "%{$search}%")
                  ->orWhere('route_code', 'LIKE', "%{$search}%")
                  ->orWhere('start_location', 'LIKE', "%{$search}%")
                  ->orWhere('end_location', 'LIKE', "%{$search}%");
            });
        }

        $routes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total_routes' => Route::count(),
            'active_routes' => Route::where('status', 'active')->count(),
            'total_distance' => Route::sum('total_distance'),
            'avg_duration' => Route::avg('estimated_duration')
        ];

        return view('routes.index', compact('routes', 'stats'));
    }

    public function create()
    {
        return view('routes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_name' => 'required|string|max:255',
            'route_code' => 'required|string|max:50|unique:routes',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'route_type' => 'required|in:delivery,pickup,service,maintenance,emergency',
            'priority' => 'required|in:low,medium,high,urgent',
            'total_distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'checkpoints' => 'required|array|min:1',
            'checkpoints.*.name' => 'required|string|max:255',
            'checkpoints.*.address' => 'required|string|max:500',
            'checkpoints.*.latitude' => 'required|numeric|between:-90,90',
            'checkpoints.*.longitude' => 'required|numeric|between:-180,180',
            'checkpoints.*.type' => 'required|in:pickup,delivery,rest_stop,fuel_station,inspection',
            'checkpoints.*.duration' => 'required|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            $route = Route::create([
                'route_name' => $request->route_name,
                'route_code' => $request->route_code,
                'description' => $request->description,
                'start_location' => $request->start_location,
                'end_location' => $request->end_location,
                'route_type' => $request->route_type,
                'priority' => $request->priority,
                'status' => 'active',
                'total_distance' => $request->total_distance,
                'estimated_duration' => $request->estimated_duration,
                'schedule_days' => $request->schedule_days ?? [],
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'fuel_cost_estimate' => $request->fuel_cost_estimate,
                'special_instructions' => $request->special_instructions,
                'waypoints' => $request->waypoints ?? [],
                'created_by' => Auth::id()
            ]);

            // Create checkpoints
            foreach ($request->checkpoints as $index => $checkpointData) {
                RouteCheckpoint::create([
                    'route_id' => $route->id,
                    'checkpoint_name' => $checkpointData['name'],
                    'address' => $checkpointData['address'],
                    'latitude' => $checkpointData['latitude'],
                    'longitude' => $checkpointData['longitude'],
                    'sequence_order' => $index + 1,
                    'checkpoint_type' => $checkpointData['type'],
                    'estimated_duration' => $checkpointData['duration'],
                    'special_instructions' => $checkpointData['instructions'] ?? null,
                    'is_mandatory' => $checkpointData['mandatory'] ?? true,
                    'contact_info' => $checkpointData['contact_info'] ?? null
                ]);
            }

            DB::commit();
            return redirect()->route('maintenance.routes.index')->with('success', 'Route created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create route: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Route $route)
    {
        $route->load(['checkpoints', 'assignments.driver', 'assignments.vehicle', 'creator']);
        
        // Get recent assignments
        $recentAssignments = $route->assignments()
            ->with(['driver', 'vehicle', 'checkpointVisits.checkpoint'])
            ->orderBy('assignment_date', 'desc')
            ->limit(10)
            ->get();

        // Calculate route statistics
        $stats = [
            'total_assignments' => $route->assignments()->count(),
            'completed_assignments' => $route->assignments()->where('status', 'completed')->count(),
            'average_completion_time' => $route->average_completion_time,
            'efficiency_rating' => $route->efficiency_rating,
            'total_checkpoints' => $route->checkpoints()->count()
        ];

        return view('routes.show', compact('route', 'recentAssignments', 'stats'));
    }

    public function edit(Route $route)
    {
        $route->load('checkpoints');
        return view('routes.edit', compact('route'));
    }

    public function update(Request $request, Route $route)
    {
        $request->validate([
            'route_name' => 'required|string|max:255',
            'route_code' => 'required|string|max:50|unique:routes,route_code,' . $route->id,
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'route_type' => 'required|in:delivery,pickup,service,maintenance,emergency',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:active,inactive,under_review',
            'total_distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $route->update([
                'route_name' => $request->route_name,
                'route_code' => $request->route_code,
                'description' => $request->description,
                'start_location' => $request->start_location,
                'end_location' => $request->end_location,
                'route_type' => $request->route_type,
                'priority' => $request->priority,
                'status' => $request->status,
                'total_distance' => $request->total_distance,
                'estimated_duration' => $request->estimated_duration,
                'schedule_days' => $request->schedule_days ?? [],
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'fuel_cost_estimate' => $request->fuel_cost_estimate,
                'special_instructions' => $request->special_instructions,
                'waypoints' => $request->waypoints ?? []
            ]);

            // Update checkpoints if provided
            if ($request->has('checkpoints')) {
                // Delete existing checkpoints
                $route->checkpoints()->delete();

                // Create new checkpoints
                foreach ($request->checkpoints as $index => $checkpointData) {
                    RouteCheckpoint::create([
                        'route_id' => $route->id,
                        'checkpoint_name' => $checkpointData['name'],
                        'address' => $checkpointData['address'],
                        'latitude' => $checkpointData['latitude'],
                        'longitude' => $checkpointData['longitude'],
                        'sequence_order' => $index + 1,
                        'checkpoint_type' => $checkpointData['type'],
                        'estimated_duration' => $checkpointData['duration'],
                        'special_instructions' => $checkpointData['instructions'] ?? null,
                        'is_mandatory' => $checkpointData['mandatory'] ?? true,
                        'contact_info' => $checkpointData['contact_info'] ?? null
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('routes.show', $route)->with('success', 'Route updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update route: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Route $route)
    {
        try {
            // Check if route has active assignments
            if ($route->activeAssignments()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete route with active assignments.']);
            }

            $route->delete();
            return redirect()->route('maintenance.routes.index')->with('success', 'Route deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete route: ' . $e->getMessage()]);
        }
    }

    public function optimize(Route $route)
    {
        // Basic route optimization logic
        $checkpoints = $route->checkpoints()->orderBy('sequence_order')->get();
        
        if ($checkpoints->count() < 2) {
            return back()->withErrors(['error' => 'Route must have at least 2 checkpoints to optimize.']);
        }

        // Simple optimization: reorder checkpoints by geographical proximity
        $optimizedOrder = $this->optimizeCheckpointOrder($checkpoints);
        
        DB::beginTransaction();
        try {
            foreach ($optimizedOrder as $index => $checkpoint) {
                $checkpoint->update(['sequence_order' => $index + 1]);
            }

            // Recalculate total distance
            $totalDistance = $this->calculateTotalDistance($optimizedOrder);
            $route->update(['total_distance' => $totalDistance]);

            DB::commit();
            return back()->with('success', 'Route optimized successfully. Total distance: ' . $totalDistance . ' km');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to optimize route: ' . $e->getMessage()]);
        }
    }

    private function optimizeCheckpointOrder($checkpoints)
    {
        // Simple nearest neighbor algorithm
        $unvisited = $checkpoints->toArray();
        $optimized = [];
        
        // Start with the first checkpoint
        $current = array_shift($unvisited);
        $optimized[] = $current;

        while (!empty($unvisited)) {
            $nearest = null;
            $minDistance = PHP_FLOAT_MAX;
            $nearestIndex = -1;

            foreach ($unvisited as $index => $checkpoint) {
                $distance = $this->calculateDistance(
                    $current['latitude'],
                    $current['longitude'],
                    $checkpoint['latitude'],
                    $checkpoint['longitude']
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $checkpoint;
                    $nearestIndex = $index;
                }
            }

            if ($nearest) {
                $optimized[] = $nearest;
                $current = $nearest;
                unset($unvisited[$nearestIndex]);
                $unvisited = array_values($unvisited); // Reindex array
            }
        }

        return collect($optimized)->map(function ($data) {
            return RouteCheckpoint::find($data['id']);
        });
    }

    private function calculateTotalDistance($checkpoints)
    {
        $totalDistance = 0;
        
        for ($i = 1; $i < $checkpoints->count(); $i++) {
            $prev = $checkpoints[$i - 1];
            $current = $checkpoints[$i];
            
            $totalDistance += $this->calculateDistance(
                $prev->latitude,
                $prev->longitude,
                $current->latitude,
                $current->longitude
            );
        }

        return round($totalDistance, 2);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    public function export(Request $request)
    {
        $routes = Route::with(['checkpoints', 'assignments'])->get();
        
        $csvData = "Route Code,Route Name,Type,Priority,Status,Distance (km),Duration (min),Checkpoints,Total Assignments\n";
        
        foreach ($routes as $route) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%.2f,%d,%d,%d\n",
                $route->route_code,
                $route->route_name,
                $route->route_type,
                $route->priority,
                $route->status,
                $route->total_distance,
                $route->estimated_duration,
                $route->checkpoints->count(),
                $route->assignments->count()
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="routes_export.csv"');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteAssignment;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\CheckpointVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RouteAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = RouteAssignment::with(['route', 'driver', 'vehicle', 'assignedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('assignment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('assignment_date', '<=', $request->date_to);
        }

        if ($request->filled('route_type')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('route_type', $request->route_type);
            });
        }

        $assignments = $query->orderBy('assignment_date', 'desc')
                           ->orderBy('scheduled_start_time', 'asc')
                           ->paginate(20);

        // Get data for filters
        $drivers = User::where('role', 'Driver')->get();
        $routes = Route::active()->get();

        // Get statistics
        $stats = [
            'total_assignments' => RouteAssignment::count(),
            'today_assignments' => RouteAssignment::today()->count(),
            'completed_today' => RouteAssignment::today()->where('status', 'completed')->count(),
            'in_progress' => RouteAssignment::where('status', 'in_progress')->count()
        ];

        return view('route-assignments.index', compact('assignments', 'drivers', 'routes', 'stats'));
    }

    public function create()
    {
        $routes = Route::active()->get();
        $drivers = User::where('role', 'Driver')->get();
        $vehicles = Vehicle::where('status', 'active')->get();

        return view('route-assignments.create', compact('routes', 'drivers', 'vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'assignment_date' => 'required|date|after_or_equal:today',
            'scheduled_start_time' => 'required|date_format:H:i',
            'scheduled_end_time' => 'required|date_format:H:i|after:scheduled_start_time'
        ]);

        // Check for conflicts
        $conflicts = RouteAssignment::where('driver_id', $request->driver_id)
            ->whereDate('assignment_date', $request->assignment_date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('scheduled_start_time', [$request->scheduled_start_time, $request->scheduled_end_time])
                      ->orWhereBetween('scheduled_end_time', [$request->scheduled_start_time, $request->scheduled_end_time])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('scheduled_start_time', '<=', $request->scheduled_start_time)
                            ->where('scheduled_end_time', '>=', $request->scheduled_end_time);
                      });
            })
            ->whereIn('status', ['assigned', 'in_progress'])
            ->exists();

        if ($conflicts) {
            return back()->withErrors(['error' => 'Driver has conflicting assignments at this time.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $assignment = RouteAssignment::create([
                'route_id' => $request->route_id,
                'driver_id' => $request->driver_id,
                'vehicle_id' => $request->vehicle_id,
                'assignment_date' => $request->assignment_date,
                'status' => 'assigned',
                'scheduled_start_time' => $request->scheduled_start_time,
                'scheduled_end_time' => $request->scheduled_end_time,
                'notes' => $request->notes,
                'assigned_by' => Auth::id()
            ]);

            // Create checkpoint visits for tracking
            $route = Route::with('checkpoints')->find($request->route_id);
            foreach ($route->checkpoints as $checkpoint) {
                CheckpointVisit::create([
                    'route_assignment_id' => $assignment->id,
                    'checkpoint_id' => $checkpoint->id,
                    'status' => 'pending'
                ]);
            }

            DB::commit();
            return redirect()->route('route-assignments.index')->with('success', 'Route assignment created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create assignment: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(RouteAssignment $routeAssignment)
    {
        $routeAssignment->load([
            'route.checkpoints',
            'driver',
            'vehicle',
            'assignedBy',
            'checkpointVisits.checkpoint'
        ]);

        return view('route-assignments.show', compact('routeAssignment'));
    }

    public function edit(RouteAssignment $routeAssignment)
    {
        if ($routeAssignment->status === 'completed') {
            return back()->withErrors(['error' => 'Cannot edit completed assignments.']);
        }

        $routes = Route::active()->get();
        $drivers = User::where('role', 'Driver')->get();
        $vehicles = Vehicle::where('status', 'active')->get();

        return view('route-assignments.edit', compact('routeAssignment', 'routes', 'drivers', 'vehicles'));
    }

    public function update(Request $request, RouteAssignment $routeAssignment)
    {
        if ($routeAssignment->status === 'completed') {
            return back()->withErrors(['error' => 'Cannot update completed assignments.']);
        }

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'assignment_date' => 'required|date',
            'scheduled_start_time' => 'required|date_format:H:i',
            'scheduled_end_time' => 'required|date_format:H:i|after:scheduled_start_time',
            'status' => 'required|in:assigned,in_progress,completed,cancelled'
        ]);

        $routeAssignment->update($request->only([
            'route_id',
            'driver_id',
            'vehicle_id',
            'assignment_date',
            'scheduled_start_time',
            'scheduled_end_time',
            'status',
            'notes'
        ]));

        return redirect()->route('route-assignments.show', $routeAssignment)
                        ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(RouteAssignment $routeAssignment)
    {
        if ($routeAssignment->status === 'in_progress') {
            return back()->withErrors(['error' => 'Cannot delete assignment in progress.']);
        }

        $routeAssignment->delete();
        return redirect()->route('route-assignments.index')->with('success', 'Assignment deleted successfully.');
    }

    public function start(RouteAssignment $routeAssignment)
    {
        if ($routeAssignment->status !== 'assigned') {
            return response()->json(['error' => 'Assignment cannot be started.'], 400);
        }

        $routeAssignment->update([
            'status' => 'in_progress',
            'actual_start_time' => now()->format('H:i')
        ]);

        return response()->json(['success' => 'Route started successfully.']);
    }

    public function complete(RouteAssignment $routeAssignment, Request $request)
    {
        if ($routeAssignment->status !== 'in_progress') {
            return response()->json(['error' => 'Assignment is not in progress.'], 400);
        }

        $request->validate([
            'actual_distance' => 'required|numeric|min:0',
            'fuel_consumed' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $actualEndTime = now();
        $actualDuration = $routeAssignment->actual_start_time ? 
            Carbon::parse($routeAssignment->actual_start_time)->diffInMinutes($actualEndTime) : null;

        $routeAssignment->update([
            'status' => 'completed',
            'actual_end_time' => $actualEndTime->format('H:i'),
            'actual_distance' => $request->actual_distance,
            'actual_duration' => $actualDuration,
            'fuel_consumed' => $request->fuel_consumed,
            'notes' => $request->notes
        ]);

        return response()->json(['success' => 'Route completed successfully.']);
    }

    public function cancel(RouteAssignment $routeAssignment, Request $request)
    {
        if ($routeAssignment->status === 'completed') {
            return response()->json(['error' => 'Cannot cancel completed assignment.'], 400);
        }

        $routeAssignment->update([
            'status' => 'cancelled',
            'notes' => $request->notes ?? $routeAssignment->notes
        ]);

        return response()->json(['success' => 'Assignment cancelled successfully.']);
    }

    public function tracking(RouteAssignment $routeAssignment)
    {
        $routeAssignment->load([
            'route.checkpoints',
            'driver',
            'vehicle',
            'checkpointVisits.checkpoint'
        ]);

        return view('route-assignments.tracking', compact('routeAssignment'));
    }

    public function updateLocation(RouteAssignment $routeAssignment, Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'timestamp' => 'required|date'
        ]);

        // Update GPS tracking data
        $gpsData = $routeAssignment->gps_tracking_data ?? [];
        $gpsData[] = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timestamp' => $request->timestamp,
            'speed' => $request->speed ?? null,
            'heading' => $request->heading ?? null
        ];

        $routeAssignment->update(['gps_tracking_data' => $gpsData]);

        return response()->json(['success' => 'Location updated successfully.']);
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.route_id' => 'required|exists:routes,id',
            'assignments.*.driver_id' => 'required|exists:users,id',
            'assignments.*.vehicle_id' => 'required|exists:vehicles,id',
            'assignments.*.assignment_date' => 'required|date|after_or_equal:today',
            'assignments.*.scheduled_start_time' => 'required|date_format:H:i',
            'assignments.*.scheduled_end_time' => 'required|date_format:H:i'
        ]);

        DB::beginTransaction();
        try {
            $created = 0;
            foreach ($request->assignments as $assignmentData) {
                // Check for conflicts
                $conflicts = RouteAssignment::where('driver_id', $assignmentData['driver_id'])
                    ->whereDate('assignment_date', $assignmentData['assignment_date'])
                    ->where(function ($query) use ($assignmentData) {
                        $query->whereBetween('scheduled_start_time', [$assignmentData['scheduled_start_time'], $assignmentData['scheduled_end_time']])
                              ->orWhereBetween('scheduled_end_time', [$assignmentData['scheduled_start_time'], $assignmentData['scheduled_end_time']]);
                    })
                    ->whereIn('status', ['assigned', 'in_progress'])
                    ->exists();

                if (!$conflicts) {
                    $assignment = RouteAssignment::create([
                        'route_id' => $assignmentData['route_id'],
                        'driver_id' => $assignmentData['driver_id'],
                        'vehicle_id' => $assignmentData['vehicle_id'],
                        'assignment_date' => $assignmentData['assignment_date'],
                        'status' => 'assigned',
                        'scheduled_start_time' => $assignmentData['scheduled_start_time'],
                        'scheduled_end_time' => $assignmentData['scheduled_end_time'],
                        'assigned_by' => Auth::id()
                    ]);

                    // Create checkpoint visits
                    $route = Route::with('checkpoints')->find($assignmentData['route_id']);
                    foreach ($route->checkpoints as $checkpoint) {
                        CheckpointVisit::create([
                            'route_assignment_id' => $assignment->id,
                            'checkpoint_id' => $checkpoint->id,
                            'status' => 'pending'
                        ]);
                    }

                    $created++;
                }
            }

            DB::commit();
            return response()->json([
                'success' => "Successfully created {$created} assignments.",
                'created' => $created
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create bulk assignments: ' . $e->getMessage()], 500);
        }
    }

    public function calendar(Request $request)
    {
        $start = $request->get('start', now()->startOfMonth());
        $end = $request->get('end', now()->endOfMonth());

        $assignments = RouteAssignment::with(['route', 'driver', 'vehicle'])
            ->whereBetween('assignment_date', [$start, $end])
            ->get();

        $events = $assignments->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'title' => $assignment->route->route_name . ' - ' . $assignment->driver->name,
                'start' => $assignment->assignment_date->format('Y-m-d') . 'T' . $assignment->scheduled_start_time,
                'end' => $assignment->assignment_date->format('Y-m-d') . 'T' . $assignment->scheduled_end_time,
                'backgroundColor' => $this->getStatusColor($assignment->status),
                'borderColor' => $this->getStatusColor($assignment->status),
                'extendedProps' => [
                    'route' => $assignment->route->route_name,
                    'driver' => $assignment->driver->name,
                    'vehicle' => $assignment->vehicle->vehicle_number,
                    'status' => $assignment->status
                ]
            ];
        });

        return response()->json($events);
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'assigned' => '#007bff',
            'in_progress' => '#ffc107',
            'completed' => '#28a745',
            'cancelled' => '#dc3545',
            default => '#6c757d'
        };
    }
}
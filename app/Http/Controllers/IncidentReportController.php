<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncidentReportController extends Controller
{
    public function index(Request $request)
{
    if ($request->user() && strcasecmp($request->user()->role, 'driver') === 0) {
        return redirect()->route('driver.dashboard'); 
    }

    $query = IncidentReport::with(['vehicle', 'driver', 'reporter']);

    // Filter by status
    if ($request->has('status') && $request->status != 'all') {
        $query->where('status', $request->status);
    }

    // Filter by type
    if ($request->has('type') && $request->type != 'all') {
        $query->where('type', $request->type);
    }

    // Filter by severity
    if ($request->has('severity') && $request->severity != 'all') {
        $query->where('severity', $request->severity);
    }

    // Date range filter
    if ($request->has('date_from')) {
        $query->where('incident_date', '>=', $request->date_from);
    }
    if ($request->has('date_to')) {
        $query->where('incident_date', '<=', $request->date_to);
    }

    // Search
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('incident_number', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $incidents = $query->orderBy('incident_date', 'desc')->paginate(10);

    // Get statistics
    $stats = [
        'total'       => IncidentReport::count(),
        'active'      => IncidentReport::active()->count(),
        'critical'    => IncidentReport::critical()->count(),
        'this_month'  => IncidentReport::whereMonth('incident_date', now()->month)->count(),
        'resolved'    => IncidentReport::where('status', 'resolved')->count(),
    ];

    return view('support.incidents.index', compact('incidents', 'stats'));
}

    public function create()
    {
        $vehicles = Vehicle::all();
        $drivers = User::where('role', 'Driver')->get();
        
        return view('support.incidents.create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:users,id',
            'type' => 'required|in:accident,breakdown,theft,vandalism,traffic_violation,other',
            'severity' => 'required|in:minor,moderate,major,critical',
            'incident_date' => 'required|date',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'required|string',
            'immediate_action_taken' => 'nullable|string',
            'police_report_filed' => 'sometimes|boolean',
            'police_report_number' => 'nullable|string|max:100',
            'insurance_notified' => 'sometimes|boolean',
            'insurance_claim_number' => 'nullable|string|max:100',
            'estimated_damage_cost' => 'nullable|numeric',
            'photos.*' => 'nullable|image|max:5120',
            'documents.*' => 'nullable|file|max:10240'
        ]);

        DB::beginTransaction();
        try {
            $validated['reported_by'] = Auth::id();
            $validated['status'] = 'reported';
            $validated['police_report_filed'] = $request->has('police_report_filed');
            $validated['insurance_notified'] = $request->has('insurance_notified');

            // Handle photo uploads
            if ($request->hasFile('photos')) {
                $photos = [];
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('incidents/photos', 'public');
                    $photos[] = [
                        'path' => $path,
                        'name' => $photo->getClientOriginalName()
                    ];
                }
                $validated['photos'] = $photos;
            }

            // Handle document uploads
            if ($request->hasFile('documents')) {
                $documents = [];
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('incidents/documents', 'public');
                    $documents[] = [
                        'path' => $path,
                        'name' => $document->getClientOriginalName()
                    ];
                }
                $validated['documents'] = $documents;
            }

            // Handle involved parties
            if ($request->has('involved_parties')) {
                $validated['involved_parties'] = array_filter($request->involved_parties);
            }

            // Handle witnesses
            if ($request->has('witnesses')) {
                $validated['witnesses'] = array_filter($request->witnesses);
            }

            $incident = IncidentReport::create($validated);

            // Create automatic service request for critical incidents
            if ($validated['severity'] === 'critical' && $validated['vehicle_id']) {
                $incident->vehicle->serviceRequests()->create([
                    'requester_id' => Auth::id(),
                    'category' => 'breakdown',
                    'priority' => 'critical',
                    'subject' => 'Critical Incident: ' . $incident->incident_number,
                    'description' => 'Automatic service request created for critical incident. ' . $validated['description'],
                    'location' => $validated['location'],
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('support.incidents.show', $incident)
                ->with('success', 'Incident report created successfully. Report #' . $incident->incident_number);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to create incident report: ' . $e->getMessage());
        }
    }

    public function show(IncidentReport $incident)
    {
        $incident->load(['vehicle', 'driver', 'reporter', 'comments.user']);
        
        return view('support.incidents.show', compact('incident'));
    }

    public function update(Request $request, IncidentReport $incident)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:reported,investigating,processing,resolved,closed',
            'investigation_notes' => 'sometimes|nullable|string',
            'resolution_notes' => 'sometimes|nullable|string',
            'estimated_damage_cost' => 'sometimes|nullable|numeric'
        ]);

        if (isset($validated['status']) && in_array($validated['status'], ['resolved', 'closed'])) {
            $validated['resolved_at'] = now();
        }

        $incident->update($validated);

        return redirect()->route('support.incidents.show', $incident)
            ->with('success', 'Incident report updated successfully');
    }

    public function addComment(Request $request, IncidentReport $incident)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'sometimes|boolean',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_internal'] = $request->has('is_internal') ? true : false;

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('incident-comments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $incident->comments()->create($validated);

        return back()->with('success', 'Comment added successfully');
    }

    public function updateInsurance(Request $request, IncidentReport $incident)
    {
        $validated = $request->validate([
            'insurance_notified' => 'required|boolean',
            'insurance_claim_number' => 'nullable|string|max:100'
        ]);

        $incident->update($validated);

        return back()->with('success', 'Insurance information updated');
    }

    public function updatePoliceReport(Request $request, IncidentReport $incident)
    {
        $validated = $request->validate([
            'police_report_filed' => 'required|boolean',
            'police_report_number' => 'nullable|string|max:100'
        ]);

        $incident->update($validated);

        return back()->with('success', 'Police report information updated');
    }

    public function export(Request $request)
    {
        $query = IncidentReport::with(['vehicle', 'driver', 'reporter']);

        // Apply filters
        if ($request->has('date_from')) {
            $query->where('incident_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('incident_date', '<=', $request->date_to);
        }
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $incidents = $query->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="incident_reports_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Incident Number', 'Date', 'Type', 'Severity', 'Status',
                'Vehicle', 'Driver', 'Location', 'Description',
                'Police Report', 'Insurance Claim', 'Estimated Cost'
            ]);

            // Data
            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->incident_number,
                    $incident->incident_date->format('Y-m-d H:i'),
                    $incident->type,
                    $incident->severity,
                    $incident->status,
                    $incident->vehicle ? $incident->vehicle->registration_number : 'N/A',
                    $incident->driver ? $incident->driver->name : 'N/A',
                    $incident->location,
                    $incident->description,
                    $incident->police_report_number ?: 'N/A',
                    $incident->insurance_claim_number ?: 'N/A',
                    $incident->estimated_damage_cost ?: 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function statistics()
    {
        $stats = [
            'by_type' => IncidentReport::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
            'by_severity' => IncidentReport::select('severity', DB::raw('count(*) as count'))
                ->groupBy('severity')
                ->get(),
            'by_status' => IncidentReport::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'monthly_trend' => IncidentReport::selectRaw('DATE_FORMAT(incident_date, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
            'total_damage_cost' => IncidentReport::sum('estimated_damage_cost'),
            'avg_resolution_time' => IncidentReport::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, incident_date, resolved_at)) as avg_days')
                ->first()->avg_days,
            'vehicles_most_incidents' => Vehicle::withCount('incidentReports')
                ->orderBy('incident_reports_count', 'desc')
                ->limit(5)
                ->get()
        ];

        return view('support.incident-statistics', compact('stats'));
    }
}
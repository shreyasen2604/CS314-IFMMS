<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentUpdate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;


class IncidentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
{
    $statuses   = ['New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'];
    $severities = ['P1','P2','P3','P4'];
    $technicians = User::where('role', 'Technician')
    ->orderBy('name')
    ->get(['id','name']);

    $by = $request->get('by','keyword'); // keyword|status|severity|vehicle|date

    // ✅ sort whitelist
    $sortable = ['id','title','severity','status','created_at'];
    $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'created_at';
    $dir  = $request->get('dir') === 'asc' ? 'asc' : 'desc';

    $perPage = in_array((int) $request->get('per_page'), [10,25,50,100])
        ? (int) $request->get('per_page') : 10;

    $incidents = \App\Models\Incident::with('assignee')
        ->where('reported_by_user_id', auth()->id())
        ->when($by === 'keyword' && $request->filled('q'), function ($q) use ($request) {
            $t = '%'.$request->q.'%';
            $q->where(function ($k) use ($t) {
                $k->where('title','like',$t)
                  ->orWhere('description','like',$t)
                  ->orWhere('vehicle_identifier','like',$t);
            });
        })
        ->when($by === 'status'   && $request->filled('status'),   fn($q) => $q->where('status',   $request->status))
        ->when($by === 'assignee' && $request->filled('assignee'),
             fn($q) => $q->where('assigned_to_user_id', $request->assignee))
        ->when($by === 'severity' && $request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
        ->when($by === 'vehicle'  && $request->filled('vehicle'),  fn($q) => $q->where('vehicle_identifier','like','%'.$request->vehicle.'%'))
        ->when($by === 'date' && ($request->filled('from') || $request->filled('to')), function ($q) use ($request) {
            if ($request->filled('from')) $q->whereDate('created_at','>=',$request->from);
            if ($request->filled('to'))   $q->whereDate('created_at','<=',$request->to);
        })
        ->orderBy($sort, $dir) 
        ->paginate($perPage) 
        ->withQueryString();

    return view('driver.incidents.index', [
        'incidents'   => $incidents,
        'statuses'    => $statuses,
        'severities'  => $severities,
        'technicians' => $technicians, 
        'by'          => $by,
        'perPage'     => $perPage,
        'sort'        => $sort,
        'dir'         => $dir,
    ]);
}


    public function create()
    {
        $severities = ['P1','P2','P3','P4'];
        $categories = ['Engine','Electrical','Brakes','Tires','Cooling','Suspension','Transmission','Other'];
        return view('driver.incidents.create', compact('severities','categories'));
    }

    public function emergency()
    {
        return view('driver.incidents.emergency');
    }

    public function store(Request $request)
    {
        $severities = ['P1','P2','P3','P4'];
        $categories = ['Engine','Electrical','Brakes','Tires','Cooling','Suspension','Transmission','Other'];

        $data = $request->validate([
            'title'              => ['required','string','max:255'],
            'description'        => ['required','string'],
            'category'           => ['nullable', Rule::in($categories)],
            'severity'           => ['required', Rule::in($severities)],
            'vehicle_identifier' => ['nullable','string','max:50'],
            'odometer'           => ['nullable','integer','min:0'],
            'latitude'           => ['nullable','numeric','between:-90,90'],
            'longitude'          => ['nullable','numeric','between:-180,180'],
            'dtc_codes'          => ['nullable','string'], // comma-separated, we'll parse
        ]);

        // Parse dtc_codes "P0217,P0300" -> ['P0217','P0300']
        if (!empty($data['dtc_codes'])) {
            $codes = array_filter(array_map('trim', explode(',', $data['dtc_codes'])));
            $data['dtc_codes'] = $codes ?: null;
        }

        $data['reported_by_user_id'] = auth()->id();   // driver is the reporter
        // status defaults to "New" by migration; assignee left null
        Incident::create($data);

        return redirect()->route('driver.incidents.index')->with('ok', 'Incident submitted.');
    }

    /* -------------------- NEW: view one incident -------------------- */
    public function show(Incident $incident)
    {
        // Ensure drivers can only view their own incidents
        abort_unless($incident->reported_by_user_id === auth()->id(), 403);

        $incident->load(['assignee','reporter']);
        $updates = IncidentUpdate::with('user')
            ->where('incident_id', $incident->id)
            ->latest()
            ->get();

        return view('driver.incidents.show', compact('incident','updates'));
    }

    /* -------------------- NEW: post a comment -------------------- */
    public function comment(Request $request, Incident $incident)
    {
        abort_unless($incident->reported_by_user_id === auth()->id(), 403);

        $validated = $request->validate([
            'body' => ['required','string'],
        ]);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id'     => auth()->id(),
            'type'        => 'comment',
            'body'        => $validated['body'],
        ]);

        return back()->with('ok', 'Comment posted.');
    }

    /* -------------------- NEW: acknowledge resolution -> close -------------------- */
    public function acknowledge(Incident $incident)
    {
        abort_unless($incident->reported_by_user_id === auth()->id(), 403);

        if ($incident->status !== 'Resolved') {
            return back()->with('err', 'You can acknowledge only after it is marked Resolved.');
        }

        $incident->update([
            'status'    => 'Closed',
            'closed_at' => $incident->closed_at ?: now(),
        ]);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id'     => auth()->id(),
            'type'        => 'status',
            'body'        => 'Driver acknowledged resolution: Resolved → Closed',
            'data'        => ['from' => 'Resolved', 'to' => 'Closed'],
        ]);

        return back()->with('ok', 'Thanks — ticket closed.');
    }

    /* -------------------- NEW: emergency report store -------------------- */
    public function emergencyStore(Request $request)
    {
        $reportType = $request->input('report_type');
        
        if ($reportType === 'quick') {
            return $this->storeQuickEmergencyReport($request);
        } else {
            return $this->storeDetailedEmergencyReport($request);
        }
    }

    private function storeQuickEmergencyReport(Request $request)
    {
        $data = $request->validate([
            'vehicle_identifier' => ['required','string','max:50'],
            'location_description' => ['nullable','string','max:255'],
            'latitude' => ['nullable','numeric','between:-90,90'],
            'longitude' => ['nullable','numeric','between:-180,180'],
            'emergency_types' => ['required','array','min:1'],
            'emergency_types.*' => ['string','in:fuel_low,engine_trouble,electrical_problem,oil_low,tire_issues,overheating,other'],
            'other_description' => ['nullable','string','max:255'],
            'needs_assistance' => ['nullable','boolean'],
        ]);

        // Generate title and description from emergency types
        $emergencyTypes = $data['emergency_types'];
        $typeLabels = [
            'fuel_low' => 'Fuel Low',
            'engine_trouble' => 'Engine Trouble',
            'electrical_problem' => 'Electrical Problem',
            'oil_low' => 'Oil Low',
            'tire_issues' => 'Tire Issues',
            'overheating' => 'Overheating',
            'other' => 'Other Emergency'
        ];

        $selectedTypes = array_map(fn($type) => $typeLabels[$type] ?? $type, $emergencyTypes);
        $title = 'EMERGENCY: ' . implode(', ', $selectedTypes);
        
        $description = "Quick Emergency Report\n\n";
        $description .= "Emergency Types:\n";
        foreach ($selectedTypes as $type) {
            $description .= "• " . $type . "\n";
        }
        
        if (in_array('other', $emergencyTypes) && !empty($data['other_description'])) {
            $description .= "\nOther Details: " . $data['other_description'];
        }
        
        if ($data['needs_assistance'] ?? false) {
            $description .= "\n\n⚠️ IMMEDIATE ROADSIDE ASSISTANCE REQUESTED";
        }

        // Determine severity based on emergency types
        $criticalTypes = ['engine_trouble', 'overheating', 'electrical_problem'];
        $highTypes = ['tire_issues', 'oil_low'];
        
        $severity = 'P3'; // Default medium
        if (array_intersect($criticalTypes, $emergencyTypes)) {
            $severity = 'P1'; // Critical
        } elseif (array_intersect($highTypes, $emergencyTypes)) {
            $severity = 'P2'; // High
        } elseif (in_array('fuel_low', $emergencyTypes)) {
            $severity = 'P2'; // High for fuel issues
        }

        $incident = Incident::create([
            'title' => $title,
            'description' => $description,
            'category' => $this->mapEmergencyTypeToCategory($emergencyTypes[0]),
            'severity' => $severity,
            'vehicle_identifier' => $data['vehicle_identifier'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'location_description' => $data['location_description'],
            'reported_by_user_id' => auth()->id(),
            'status' => 'New',
        ]);

        // Generate report ID and response time
        $reportId = 'ER-' . date('Y') . '-' . str_pad($incident->id, 3, '0', STR_PAD_LEFT);
        $responseTime = $severity === 'P1' ? '5-15 min' : ($severity === 'P2' ? '15-30 min' : '30-60 min');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok'            => true,
                'id'            => $incident->id,
                'report_id'     => $reportId,
                'response_time' => $responseTime,
            ]);
        }

        return redirect()->route('driver.incidents.emergency')
            ->with('success', true)
            ->with('report_id', $reportId)
            ->with('response_time', $responseTime);
    }

    private function storeDetailedEmergencyReport(Request $request)
    {
        $severities = ['P1','P2','P3','P4'];
        $categories = ['Engine','Transmission','Brakes','Electrical','Fuel System','Cooling System','Tires','Suspension','Body/Exterior','Safety Equipment','Other'];

        $data = $request->validate([
            'vehicle_identifier' => ['required','string','max:50'],
            'category' => ['required', Rule::in($categories)],
            'incident_datetime' => ['required','date'],
            'location_description' => ['required','string','max:255'],
            'latitude' => ['nullable','numeric','between:-90,90'],
            'longitude' => ['nullable','numeric','between:-180,180'],
            'description' => ['required','string'],
            'severity' => ['required', Rule::in($severities)],
            'needs_assistance' => ['nullable','boolean'],
            'photos' => ['nullable','array'],
            'photos.*' => ['image','max:5120'], // 5MB max per image
        ]);

        // Generate title from category and severity
        $title = $data['severity'] . ' ' . $data['category'] . ' Emergency';
        
        $description = $data['description'];
        if ($data['needs_assistance'] ?? false) {
            $description .= "\n\n⚠️ IMMEDIATE ROADSIDE ASSISTANCE REQUESTED";
        }

        $incident = Incident::create([
            'title' => $title,
            'description' => $description,
            'category' => $data['category'],
            'severity' => $data['severity'],
            'vehicle_identifier' => $data['vehicle_identifier'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'location_description' => $data['location_description'],
            'reported_by_user_id' => auth()->id(),
            'status' => 'New',
            'created_at' => $data['incident_datetime'],
        ]);

        // Handle photo uploads (basic implementation)
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('incident-photos', 'public');
                // You would typically save this to a photos table or add to incident data
                // For now, we'll add it to the description
                $incident->update([
                    'description' => $incident->description . "\n\nPhoto uploaded: " . $path
                ]);
            }
        }

        // Generate report ID and response time
        $reportId = 'ER-' . date('Y') . '-' . str_pad($incident->id, 3, '0', STR_PAD_LEFT);
        $responseTime = $data['severity'] === 'P1' ? '5-15 min' : ($data['severity'] === 'P2' ? '15-30 min' : '30-60 min');

        return redirect()->route('driver.incidents.emergency')
            ->with('success', true)
            ->with('report_id', $reportId)
            ->with('response_time', $responseTime);
    }

    private function mapEmergencyTypeToCategory($emergencyType)
    {
        $mapping = [
            'fuel_low' => 'Fuel System',
            'engine_trouble' => 'Engine',
            'electrical_problem' => 'Electrical',
            'oil_low' => 'Engine',
            'tire_issues' => 'Tires',
            'overheating' => 'Cooling System',
            'other' => 'Other'
        ];

        return $mapping[$emergencyType] ?? 'Other';
    }

}

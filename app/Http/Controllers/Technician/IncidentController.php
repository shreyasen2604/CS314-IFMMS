<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentUpdate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
{
    $statuses   = ['New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'];
    $severities = ['P1','P2','P3','P4'];
    $reporters  = \App\Models\User::where('role','Driver')->orderBy('name')->get(['id','name']);

    $by = $request->get('by', 'keyword'); // keyword|status|severity|vehicle|reporter|date

    // ✅ sort whitelist + current choice
    $sortable = ['id','title','severity','status','created_at'];

    // Unassigned sort
    $uSort = in_array($request->get('u_sort'), $sortable) ? $request->get('u_sort') : 'created_at';
    $uDir  = $request->get('u_dir') === 'asc' ? 'asc' : 'desc';

    // Assigned-to-me sort
    $mSort = in_array($request->get('m_sort'), $sortable) ? $request->get('m_sort') : 'created_at';
    $mDir  = $request->get('m_dir') === 'asc' ? 'asc' : 'desc';

    $base = \App\Models\Incident::with('reporter');

    $perPage = in_array((int)$request->get('per_page'), [10,25,50,100])
        ? (int)$request->get('per_page') : 10;

    // Unassigned (filter + sort)
    $unassigned = (clone $base)
        ->whereNull('assigned_to_user_id')
        ->whereNotIn('status', ['Closed','Cancelled'])
        ->when($by === 'keyword' && $request->filled('q'), function ($q) use ($request) {
            $t = '%'.$request->q.'%';
            $q->where(function ($k) use ($t) {
                $k->where('title','like',$t)
                  ->orWhere('description','like',$t)
                  ->orWhere('vehicle_identifier','like',$t);
            });
        })
        ->when($by === 'status'   && $request->filled('status'),   fn($q) => $q->where('status',   $request->status))
        ->when($by === 'severity' && $request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
        ->when($by === 'vehicle'  && $request->filled('vehicle'),  fn($q) => $q->where('vehicle_identifier','like','%'.$request->vehicle.'%'))
        ->when($by === 'reporter' && $request->filled('reporter'), fn($q) => $q->where('reported_by_user_id',$request->reporter))
        ->when($by === 'date' && ($request->filled('from') || $request->filled('to')), function ($q) use ($request) {
            if ($request->filled('from')) $q->whereDate('created_at','>=',$request->from);
            if ($request->filled('to'))   $q->whereDate('created_at','<=',$request->to);
        })
        ->orderBy($uSort, $uDir)     
        ->paginate($perPage, ['*'], 'u_page');
        

    // Assigned to me (filter + sort)
    $mine = (clone $base)
        ->where('assigned_to_user_id', auth()->id())
        ->when($by === 'keyword' && $request->filled('q'), function ($q) use ($request) {
            $t = '%'.$request->q.'%';
            $q->where(function ($k) use ($t) {
                $k->where('title','like',$t)
                  ->orWhere('description','like',$t)
                  ->orWhere('vehicle_identifier','like',$t);
            });
        })
        ->when($by === 'status'   && $request->filled('status'),   fn($q) => $q->where('status',   $request->status))
        ->when($by === 'severity' && $request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
        ->when($by === 'vehicle'  && $request->filled('vehicle'),  fn($q) => $q->where('vehicle_identifier','like','%'.$request->vehicle.'%'))
        ->when($by === 'reporter' && $request->filled('reporter'), fn($q) => $q->where('reported_by_user_id',$request->reporter))
        ->when($by === 'date' && ($request->filled('from') || $request->filled('to')), function ($q) use ($request) {
            if ($request->filled('from')) $q->whereDate('created_at','>=',$request->from);
            if ($request->filled('to'))   $q->whereDate('created_at','<=',$request->to);
        })
        ->orderBy($mSort, $mDir)   
        ->paginate($perPage, ['*'], 'm_page') 
        ->withQueryString();

    return view('technician.incidents.index', [
    'unassigned' => $unassigned,
    'mine'       => $mine,
    'statuses'   => $statuses,
    'severities' => $severities,
    'reporters'  => $reporters,
    'by'         => $by,
    'perPage'    => $perPage,
]);
}


    public function show(Incident $incident)
    {
        $incident->load(['reporter','assignee']);
        $updates = IncidentUpdate::with('user')
            ->where('incident_id', $incident->id)
            ->latest()
            ->get();

        $canClaim = is_null($incident->assigned_to_user_id);
        $canWork  = $incident->assigned_to_user_id === auth()->id();

        // Allowed statuses for technicians
        $statuses = ['Acknowledged','In Progress','Waiting','Resolved'];

        return view('technician.incidents.show', compact('incident','updates','canClaim','canWork','statuses'));
    }

    public function claim(Incident $incident)
    {
        if (!is_null($incident->assigned_to_user_id)) {
            return back()->with('err', 'This incident is already assigned.');
        }

        $incident->update(['assigned_to_user_id' => auth()->id()]);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id'     => auth()->id(),
            'type'        => 'assignment',
            'body'        => 'Incident claimed by technician.',
        ]);

        return back()->with('ok', 'You have claimed this incident.');
    }

    public function status(Request $request, Incident $incident)
    {
        if ($incident->assigned_to_user_id !== auth()->id()) {
            abort(403, 'Only the assigned technician can update status.');
        }

        $statuses = ['Acknowledged','In Progress','Waiting','Resolved'];

        $validated = $request->validate([
            'status' => ['required', Rule::in($statuses)],
        ]);

        $old = $incident->status;
        $new = $validated['status'];

        $payload = ['status' => $new];
        $now = now();
        $map = [
            'Acknowledged' => 'acknowledged_at',
            'In Progress'  => 'started_at',
            'Resolved'     => 'resolved_at',
        ];
        if (isset($map[$new]) && is_null($incident->{$map[$new]})) {
            $payload[$map[$new]] = $now;
        }

        $incident->update($payload);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id'     => auth()->id(),
            'type'        => 'status',
            'body'        => "Status changed: {$old} → {$new}",
            'data'        => ['from' => $old, 'to' => $new],
        ]);

        return back()->with('ok', 'Status updated.');
    }

    public function comment(Request $request, Incident $incident)
    {
        if ($incident->assigned_to_user_id !== auth()->id()) {
            abort(403, 'Only the assigned technician can comment.');
        }

        $validated = $request->validate([
            'body' => ['required','string'],
        ]);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id'     => auth()->id(),
            'type'        => 'comment',
            'body'        => $validated['body'],
        ]);

        return back()->with('ok', 'Comment added.');
    }

    public function export(\Illuminate\Http\Request $request)
{
    $statuses   = ['New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'];
    $severities = ['P1','P2','P3','P4'];
    $by = $request->get('by','keyword');
    $scope = $request->get('scope','mine'); // unassigned | mine

    $sortable = ['id','title','severity','status','created_at'];
    $uSort = in_array($request->get('u_sort'), $sortable) ? $request->get('u_sort') : 'created_at';
    $uDir  = $request->get('u_dir') === 'asc' ? 'asc' : 'desc';
    $mSort = in_array($request->get('m_sort'), $sortable) ? $request->get('m_sort') : 'created_at';
    $mDir  = $request->get('m_dir') === 'asc' ? 'asc' : 'desc';

    $base = \App\Models\Incident::with('reporter:id,name');

    $q = (clone $base)
        ->when($scope === 'unassigned', fn($qq) => $qq->whereNull('assigned_to_user_id')->whereNotIn('status',['Closed','Cancelled']))
        ->when($scope === 'mine',       fn($qq) => $qq->where('assigned_to_user_id', auth()->id()))
        ->when($by === 'keyword' && $request->filled('q'), function ($qq) use ($request) {
            $t = '%'.$request->q.'%';
            $qq->where(function ($k) use ($t) {
                $k->where('title','like',$t)
                  ->orWhere('description','like',$t)
                  ->orWhere('vehicle_identifier','like',$t);
            });
        })
        ->when($by === 'status'   && $request->filled('status'),   fn($qq) => $qq->where('status',   $request->status))
        ->when($by === 'severity' && $request->filled('severity'), fn($qq) => $qq->where('severity', $request->severity))
        ->when($by === 'vehicle'  && $request->filled('vehicle'),  fn($qq) => $qq->where('vehicle_identifier','like','%'.$request->vehicle.'%'))
        ->when($by === 'reporter' && $request->filled('reporter'), fn($qq) => $qq->where('reported_by_user_id',$request->reporter))
        ->when($by === 'date' && ($request->filled('from') || $request->filled('to')), function ($qq) use ($request) {
            if ($request->filled('from')) $qq->whereDate('created_at','>=',$request->from);
            if ($request->filled('to'))   $qq->whereDate('created_at','<=',$request->to);
        })
        ->when($scope === 'unassigned', fn($qq) => $qq->orderBy($uSort, $uDir))
        ->when($scope === 'mine',       fn($qq) => $qq->orderBy($mSort, $mDir));

    $filename = 'incidents_tech_'.$scope.'_'.now()->format('Ymd_His').'.csv';

    return response()->streamDownload(function () use ($q) {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Title','Severity','Status','Reporter','Vehicle','Created']);
        $q->chunk(100, function ($rows) use ($out) {
            foreach ($rows as $i) {
                fputcsv($out, [
                    $i->id,
                    $i->title,
                    $i->severity,
                    $i->status,
                    optional($i->reporter)->name,
                    $i->vehicle_identifier,
                    optional($i->created_at)?->format('Y-m-d H:i'),
                ]);
            }
        });
        fclose($out);
    }, $filename, ['Content-Type' => 'text/csv']);
}

}

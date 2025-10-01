<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
public function index(Request $request)
{
    $statuses   = ['New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'];
    $severities = ['P1','P2','P3','P4'];

    // For dropdowns when needed
    $reporters   = \App\Models\User::where('role','Driver')->orderBy('name')->get(['id','name']);
    $technicians = \App\Models\User::where('role','Technician')->orderBy('name')->get(['id','name']);

    $by = $request->get('by', 'keyword'); // keyword|status|severity|vehicle|assignee|reporter|date

    // Allow sorting on these columns only (safety)
    $sortable = ['id','title','severity','status','created_at'];

    // Current sort column (default = created_at) and direction (default = desc)
    $sort = in_array(request('sort'), $sortable) ? request('sort') : 'created_at';
    $dir  = request('dir') === 'asc' ? 'asc' : 'desc';

    // Pagination (allowed: 10 / 25 / 50 / 100)
    $perPage = in_array((int)$request->get('per_page'), [10,25,50,100])
        ? (int)$request->get('per_page') : 10;

    $incidents = \App\Models\Incident::with(['reporter','assignee'])
        ->when($by === 'keyword' && $request->filled('q'), function ($q) use ($request) {
            $t = '%'.$request->q.'%';
            $q->where(function ($k) use ($t) {
                $k->where('title', 'like', $t)
                  ->orWhere('description', 'like', $t)
                  ->orWhere('vehicle_identifier', 'like', $t);
            });
        })
        ->when($by === 'status' && $request->filled('status'),   fn($q) => $q->where('status',   $request->status))
        ->when($by === 'severity' && $request->filled('severity'), fn($q) => $q->where('severity', $request->severity))
        ->when($by === 'vehicle' && $request->filled('vehicle'), fn($q) => $q->where('vehicle_identifier', 'like', '%'.$request->vehicle.'%'))
        ->when($by === 'assignee' && $request->filled('assignee'), fn($q) => $q->where('assigned_to_user_id', $request->assignee))
        ->when($by === 'reporter' && $request->filled('reporter'), fn($q) => $q->where('reported_by_user_id', $request->reporter))
        ->when($by === 'date' && ($request->filled('from') || $request->filled('to')), function ($q) use ($request) {
            if ($request->filled('from')) { $q->whereDate('created_at', '>=', $request->from); }
            if ($request->filled('to'))   { $q->whereDate('created_at', '<=', $request->to);   }
        })
        ->orderBy($sort, $dir)
        ->paginate($perPage)
        ->withQueryString();

        return view('admin.incidents.index', compact(
            'incidents','statuses','severities','reporters','technicians','by'
        ) + ['perPage' => $perPage]); // optional, handy for the view
}


    public function show(Incident $incident)
    {
        $incident->load(['reporter','assignee']);
        $updates = IncidentUpdate::with('user')
            ->where('incident_id', $incident->id)
            ->latest()
            ->get();

        $technicians = User::where('role', 'Technician')
            ->orderBy('name')
            ->get(['id','name']);

        $statuses = ['New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'];

        return view('admin.incidents.show', compact('incident','updates','technicians','statuses'));
    }

    public function assign(Request $request, Incident $incident)
    {
        $request->validate([
            'assigned_to_user_id' => ['nullable','exists:users,id'],
        ]);

        $old = $incident->assignee?->name ?? 'Unassigned';

        $incident->update([
            'assigned_to_user_id' => $request->assigned_to_user_id ?: null,
        ]);

        $newAssignee = $incident->assignee?->name ?? 'Unassigned';

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id'     => auth()->id(),
            'type'        => 'assignment',
            'body'        => "Assignment changed: {$old} → {$newAssignee}",
        ]);

        return back()->with('ok', 'Assignee updated.');
    }

    public function status(Request $request, Incident $incident)
    {
        $statuses = ['New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'];

        $validated = $request->validate([
            'status' => ['required', Rule::in($statuses)],
        ]);

        $old = $incident->status;
        $new = $validated['status'];

        // Update status + first-time timestamps
        $payload = ['status' => $new];

        $now = now();
        $map = [
            'Acknowledged' => 'acknowledged_at',
            'Dispatched'   => 'dispatched_at',
            'In Progress'  => 'started_at',
            'Resolved'     => 'resolved_at',
            'Closed'       => 'closed_at',
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
    // same whitelists you use in index()
    $statuses   = ['New','Acknowledged','Dispatched','In Progress','Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'];
    $severities = ['P1','P2','P3','P4'];

    $by   = $request->get('by', 'keyword');
    $sortable = ['id','title','severity','status','created_at'];
    $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'created_at';
    $dir  = $request->get('dir') === 'asc' ? 'asc' : 'desc';

    $q = \App\Models\Incident::with(['reporter:id,name','assignee:id,name'])
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
        ->when($by === 'assignee' && $request->filled('assignee'), fn($q) => $q->where('assigned_to_user_id',$request->assignee))
        ->when($by === 'reporter' && $request->filled('reporter'), fn($q) => $q->where('reported_by_user_id',$request->reporter))
        ->when($by === 'date' && ($request->filled('from') || $request->filled('to')), function ($q) use ($request) {
            if ($request->filled('from')) $q->whereDate('created_at','>=',$request->from);
            if ($request->filled('to'))   $q->whereDate('created_at','<=',$request->to);
        })
        ->orderBy($sort, $dir);

    $filename = 'incidents_admin_'.now()->format('Ymd_His').'.csv';

    return response()->streamDownload(function () use ($q) {
        $out = fopen('php://output', 'w');
        // header row
        fputcsv($out, ['ID','Title','Severity','Status','Reporter','Assignee','Vehicle','Created']);
        $q->chunk(100, function ($rows) use ($out) {
            foreach ($rows as $i) {
                fputcsv($out, [
                    $i->id,
                    $i->title,
                    $i->severity,
                    $i->status,
                    optional($i->reporter)->name,
                    optional($i->assignee)->name,
                    $i->vehicle_identifier,
                    optional($i->created_at)?->format('Y-m-d H:i'),
                ]);
            }
        });
        fclose($out);
    }, $filename, ['Content-Type' => 'text/csv']);
}


}


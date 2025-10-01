@extends('layouts.app')

@section('title', 'Incident #' . $incident->id . ' - IFMMS-ZAR')
@section('page-title', 'Incident Details')

@section('content')
<div class="container-fluid p-6">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Incident #{{ $incident->id }}</h1>
        <p class="text-gray-600 mt-1">{{ $incident->title }}</p>
      </div>
      <div class="flex items-center space-x-3">
        <span class="px-3 py-1 rounded-full text-sm font-medium 
          @if($incident->severity === 'Critical') bg-red-100 text-red-800
          @elseif($incident->severity === 'High') bg-orange-100 text-orange-800
          @elseif($incident->severity === 'Medium') bg-yellow-100 text-yellow-800
          @else bg-green-100 text-green-800 @endif">
          {{ $incident->severity }}
        </span>
        <span class="px-3 py-1 rounded-full text-sm font-medium
          @if($incident->status === 'Open') bg-blue-100 text-blue-800
          @elseif($incident->status === 'In Progress') bg-yellow-100 text-yellow-800
          @elseif($incident->status === 'Resolved') bg-green-100 text-green-800
          @else bg-gray-100 text-gray-800 @endif">
          {{ $incident->status }}
        </span>
        <a href="{{ route('admin.incidents.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
          <i class="fas fa-arrow-left mr-2"></i>Back to Incidents
        </a>
      </div>
    </div>

    @if (session('ok'))
      <div class="p-3 rounded bg-green-50 text-green-700 text-sm">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
      <div class="p-3 rounded bg-red-50 text-red-700 text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Details -->
      <div class="md:col-span-2 space-y-4">
        <div class="bg-white rounded-xl shadow p-5">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-lg font-semibold">{{ $incident->title }}</div>
              <div class="text-sm text-gray-600">Severity: <b>{{ $incident->severity }}</b> · Status: <b>{{ $incident->status }}</b></div>
              <div class="text-sm text-gray-600 mt-1">Category: {{ $incident->category ?: '—' }}</div>
            </div>
            <div class="text-right text-sm text-gray-500">
              Reported by <b>{{ $incident->reporter?->name }}</b><br>
              {{ $incident->created_at->format('Y-m-d H:i') }}
            </div>
          </div>

          <div class="mt-4 text-sm">
            <b>Description</b>
            <p class="mt-1 whitespace-pre-line">{{ $incident->description }}</p>
          </div>

          <div class="mt-4 grid md:grid-cols-3 gap-4 text-sm">
            <div><b>Vehicle</b><div class="mt-1">{{ $incident->vehicle_identifier ?: '—' }}</div></div>
            <div><b>Odometer</b><div class="mt-1">{{ $incident->odometer ? number_format($incident->odometer) . ' km' : '—' }}</div></div>
            <div><b>DTC Codes</b><div class="mt-1">{{ $incident->dtc_codes ? implode(', ', $incident->dtc_codes) : '—' }}</div></div>
            <div><b>Location</b><div class="mt-1">
              @if ($incident->latitude && $incident->longitude)
                {{ $incident->latitude }}, {{ $incident->longitude }}
              @else — @endif
            </div></div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow p-5">
          <div class="text-lg font-semibold mb-3">Updates</div>
          <div class="space-y-4">
            @forelse ($updates as $u)
              <div class="border-t pt-3">
                <div class="text-xs text-gray-500">
                  {{ $u->created_at->format('Y-m-d H:i') }} — {{ $u->user?->name }} ({{ $u->type }})
                </div>
                @if($u->body)
                  <div class="mt-1 text-sm">{{ $u->body }}</div>
                @endif
              </div>
            @empty
              <div class="text-sm text-gray-500">No updates yet.</div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="space-y-4">
        <div class="bg-white rounded-xl shadow p-5">
          <div class="text-lg font-semibold mb-3">Assign Technician</div>
          <form method="POST" action="{{ route('admin.incidents.assign', $incident) }}" class="space-y-3">
            @csrf
            <select name="assigned_to_user_id" class="w-full rounded border-gray-300">
              <option value="">— Unassigned —</option>
              @foreach ($technicians as $t)
                <option value="{{ $t->id }}" @selected($incident->assigned_to_user_id == $t->id)>{{ $t->name }}</option>
              @endforeach
            </select>
            <button class="px-3 py-2 rounded bg-gray-900 text-white w-full">Update Assignee</button>
          </form>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
          <div class="text-lg font-semibold mb-3">Change Status</div>
          <form method="POST" action="{{ route('admin.incidents.status', $incident) }}" class="space-y-3">
            @csrf
            <select name="status" class="w-full rounded border-gray-300">
              @foreach ($statuses as $s)
                <option value="{{ $s }}" @selected($incident->status === $s)>{{ $s }}</option>
              @endforeach
            </select>
            <button class="px-3 py-2 rounded bg-gray-900 text-white w-full">Update Status</button>
          </form>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
          <div class="text-lg font-semibold mb-3">Add Comment</div>
          <form method="POST" action="{{ route('admin.incidents.comment', $incident) }}" class="space-y-3">
            @csrf
            <textarea name="body" rows="3" class="w-full rounded border-gray-300" placeholder="Note to driver/technician..."></textarea>
            <button class="px-3 py-2 rounded bg-gray-900 text-white w-full">Post Comment</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

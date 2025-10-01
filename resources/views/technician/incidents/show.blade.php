@extends('layouts.app')

@section('title', 'Incident #' . $incident->id . ' - IFMMS-ZAR')

@section('content')
<div class="container-fluid py-4">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Incident #{{ $incident->id }}</h1>
          <p class="text-gray-600 mt-1">{{ $incident->title }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <span class="badge bg-{{ $incident->severity === 'Critical' ? 'danger' : ($incident->severity === 'High' ? 'warning' : 'info') }}">
            {{ $incident->severity }}
          </span>
          <span class="badge bg-{{ $incident->status === 'Open' ? 'primary' : ($incident->status === 'In Progress' ? 'warning' : 'success') }}">
            {{ $incident->status }}
          </span>
          <a href="{{ route('technician.incidents.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Queue
          </a>
        </div>
      </div>
    </div>

    @if (session('ok'))
      <div class="p-3 rounded bg-green-50 text-green-700 text-sm">{{ session('ok') }}</div>
    @endif
    @if (session('err'))
      <div class="p-3 rounded bg-red-50 text-red-700 text-sm">{{ session('err') }}</div>
    @endif
    @if ($errors->any())
      <div class="p-3 rounded bg-red-50 text-red-700 text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
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

      <div class="space-y-4">
        @if ($canClaim)
          <div class="bg-white rounded-xl shadow p-5">
            <div class="text-lg font-semibold mb-3">Claim Incident</div>
            <form method="POST" action="{{ route('technician.incidents.claim', $incident) }}">
              @csrf
              <button class="px-3 py-2 rounded bg-gray-900 text-white w-full">Claim</button>
            </form>
          </div>
        @endif

        @if ($canWork)
          <div class="bg-white rounded-xl shadow p-5">
            <div class="text-lg font-semibold mb-3">Change Status</div>
            <form method="POST" action="{{ route('technician.incidents.status', $incident) }}" class="space-y-3">
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
            <form method="POST" action="{{ route('technician.incidents.comment', $incident) }}" class="space-y-3">
              @csrf
              <textarea name="body" rows="3" class="w-full rounded border-gray-300" placeholder="Diagnosis, parts needed, next steps..."></textarea>
              <button class="px-3 py-2 rounded bg-gray-900 text-white w-full">Post Comment</button>
            </form>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

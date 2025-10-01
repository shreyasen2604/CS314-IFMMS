@extends('layouts.app')

@section('title', 'Incidents - IFMMS-ZAR')
@section('page-title', 'Incident Management')

@section('content')
<div class="container-fluid p-6">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Incident Management</h1>
        <p class="text-gray-600 mt-1">Monitor and manage all system incidents</p>
      </div>
      <div class="flex items-center space-x-3">
        <a href="{{ route('admin.incidents.export', request()->query()) }}" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
          <i class="fas fa-download mr-2"></i>Export CSV
        </a>
        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
          <i class="fas fa-arrow-left mr-2"></i>Dashboard
        </a>
      </div>
    </div>

        <!-- Filters -->
<form method="GET" class="bg-white rounded-xl shadow p-3">
  <div class="flex flex-wrap items-end gap-3">

    <!-- Control 1: Search By -->
    <div>
      <label class="block text-xs font-medium">Search by</label>
      <select name="by" id="by" class="mt-1 rounded border-gray-300">
        <option value="keyword"  {{ ($by ?? request('by','keyword'))==='keyword' ? 'selected' : '' }}>Keyword</option>
        <option value="status"   {{ ($by ?? request('by'))==='status' ? 'selected' : '' }}>Status</option>
        <option value="severity" {{ ($by ?? request('by'))==='severity' ? 'selected' : '' }}>Severity</option>
        <option value="vehicle"  {{ ($by ?? request('by'))==='vehicle' ? 'selected' : '' }}>Vehicle ID</option>
        <option value="assignee" {{ ($by ?? request('by'))==='assignee' ? 'selected' : '' }}>Assignee</option>
        <option value="reporter" {{ ($by ?? request('by'))==='reporter' ? 'selected' : '' }}>Reporter</option>
        <option value="date"     {{ ($by ?? request('by'))==='date' ? 'selected' : '' }}>Date</option>
      </select>
    </div>

    <!-- Control 2: Dynamic Value Area -->
    <div id="valueArea" class="flex items-end gap-2 flex-wrap">
      <!-- Keyword -->
      <div data-role="keyword" class="w-72">
        <label class="block text-xs font-medium">Keyword</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="title / description / vehicle"
               class="mt-1 w-full rounded border-gray-300">
      </div>

      <!-- Status -->
      <div data-role="status">
        <label class="block text-xs font-medium">Status</label>
        <select name="status" class="mt-1 rounded border-gray-300">
          <option value="">Select…</option>
          @foreach ($statuses as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <!-- Severity -->
      <div data-role="severity">
        <label class="block text-xs font-medium">Severity</label>
        <select name="severity" class="mt-1 rounded border-gray-300">
          <option value="">Select…</option>
          @foreach ($severities as $s)
            <option value="{{ $s }}" @selected(request('severity')===$s)>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      <!-- Vehicle -->
      <div data-role="vehicle" class="w-56">
        <label class="block text-xs font-medium">Vehicle ID</label>
        <input type="text" name="vehicle" value="{{ request('vehicle') }}" class="mt-1 w-full rounded border-gray-300" placeholder="e.g., TRK-001">
      </div>

      <!-- Assignee -->
      <div data-role="assignee">
        <label class="block text-xs font-medium">Assignee</label>
        <select name="assignee" class="mt-1 rounded border-gray-300">
          <option value="">Select…</option>
          @foreach ($technicians as $t)
            <option value="{{ $t->id }}" @selected(request('assignee')==$t->id)>{{ $t->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Reporter -->
      <div data-role="reporter">
        <label class="block text-xs font-medium">Reporter</label>
        <select name="reporter" class="mt-1 rounded border-gray-300">
          <option value="">Select…</option>
          @foreach ($reporters as $r)
            <option value="{{ $r->id }}" @selected(request('reporter')==$r->id)>{{ $r->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Date (range) -->
      <div data-role="date" class="flex items-end gap-2">
        <div>
          <label class="block text-xs font-medium">From</label>
          <input type="date" name="from" value="{{ request('from') }}" class="mt-1 rounded border-gray-300">
        </div>
        <div>
          <label class="block text-xs font-medium">To</label>
          <input type="date" name="to" value="{{ request('to') }}" class="mt-1 rounded border-gray-300">
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="ml-auto flex items-end gap-2">
  <div>
    <label class="block text-xs font-medium">Per page</label>
    <select name="per_page" class="mt-1 rounded border-gray-300" onchange="this.form.submit()">
      @foreach ([10,25,50,100] as $n)
        <option value="{{ $n }}" @selected((int)request('per_page', 10) === $n)>{{ $n }}</option>
      @endforeach
    </select>
  </div>

  <button class="px-3 py-2 rounded bg-gray-900 text-white">Apply</button>
  <a href="{{ route('admin.incidents.export', request()->query()) }}" class="px-3 py-2 rounded border">Export CSV</a>
  <a href="{{ route('admin.incidents.index') }}" class="px-3 py-2 rounded border">Reset</a>
</div>


  </form>

    <div class="overflow-x-auto bg-white rounded-xl shadow">
      <table class="min-w-full">
        <thead class="bg-gray-100 text-left text-sm">
  <tr>
    {{-- # (id) --}}
    @php $dir = (request('sort')==='id' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
    <th class="px-4 py-3">
      <a href="{{ route('admin.incidents.index', array_merge(request()->query(), ['sort'=>'id','dir'=>$dir])) }}"
         class="flex items-center gap-1">
        #
        @if(request('sort')==='id') <span>{{ request('dir')==='asc' ? '↑' : '↓' }}</span> @endif
      </a>
    </th>

    {{-- Title --}}
    @php $dir = (request('sort')==='title' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
    <th class="px-4 py-3">
      <a href="{{ route('admin.incidents.index', array_merge(request()->query(), ['sort'=>'title','dir'=>$dir])) }}"
         class="flex items-center gap-1">
        Title
        @if(request('sort')==='title') <span>{{ request('dir')==='asc' ? '↑' : '↓' }}</span> @endif
      </a>
    </th>

    {{-- Severity --}}
    @php $dir = (request('sort')==='severity' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
    <th class="px-4 py-3">
      <a href="{{ route('admin.incidents.index', array_merge(request()->query(), ['sort'=>'severity','dir'=>$dir])) }}"
         class="flex items-center gap-1">
        Severity
        @if(request('sort')==='severity') <span>{{ request('dir')==='asc' ? '↑' : '↓' }}</span> @endif
      </a>
    </th>

    {{-- Status --}}
    @php $dir = (request('sort')==='status' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
    <th class="px-4 py-3">
      <a href="{{ route('admin.incidents.index', array_merge(request()->query(), ['sort'=>'status','dir'=>$dir])) }}"
         class="flex items-center gap-1">
        Status
        @if(request('sort')==='status') <span>{{ request('dir')==='asc' ? '↑' : '↓' }}</span> @endif
      </a>
    </th>

    {{-- Reporter (not sortable for now) --}}
    <th class="px-4 py-3">Reporter</th>

    {{-- Assignee (not sortable for now) --}}
    <th class="px-4 py-3">Assignee</th>

    {{-- Created --}}
    @php $dir = (request('sort')==='created_at' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
    <th class="px-4 py-3">
      <a href="{{ route('admin.incidents.index', array_merge(request()->query(), ['sort'=>'created_at','dir'=>$dir])) }}"
         class="flex items-center gap-1">
        Created
        @if(request('sort')==='created_at') <span>{{ request('dir')==='asc' ? '↑' : '↓' }}</span> @endif
      </a>
    </th>
  </tr>
</thead>

        <tbody class="text-sm">
        @forelse ($incidents as $i)
          <tr class="border-t">
            <td class="px-4 py-3">{{ $i->id }}</td>
            <td class="px-4 py-3">
                <a href="{{ route('admin.incidents.show', $i) }}" class="underline hover:no-underline">
                    {{ $i->title }}
                </a>
                </td>

            <td class="px-4 py-3">{{ $i->severity }}</td>
            <td class="px-4 py-3">{{ $i->status }}</td>
            <td class="px-4 py-3">{{ $i->reporter?->name }}</td>
            <td class="px-4 py-3">{{ $i->assignee?->name ?? 'Unassigned' }}</td>
            <td class="px-4 py-3">{{ $i->created_at->format('Y-m-d H:i') }}</td>
          </tr>
        @empty
          <tr><td class="px-4 py-6 text-gray-500" colspan="7">No incidents yet.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div>{{ $incidents->links() }}</div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script>
  (function () {
    const by = document.getElementById('by');
    const roles = document.querySelectorAll('#valueArea [data-role]');
    function sync() {
      roles.forEach(el => el.style.display = 'none');
      const pick = document.querySelector('#valueArea [data-role="' + by.value + '"]');
      if (pick) pick.style.display = '';
    }
    by.addEventListener('change', sync);
    sync(); // initial
  })();
</script>
@endpush

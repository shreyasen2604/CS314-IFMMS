@extends('layouts.app')

@section('title', 'Vehicle Incidents - IFMMS-ZAR')

@section('content')
<div class="container-fluid">
  <div class="max-w-7xl mx-auto p-6 space-y-8">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="bg-red-100 p-3 rounded-full">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Reported Incidents</h1>
            <p class="text-gray-600">View reported incidents.</p>
          </div>
        </div>
        <div>
        <a href="{{ route('driver.dashboard') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
          <i class="fas fa-arrow-left mr-2"></i>Dashboard
        </a>
      </div>
      </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center mb-4">
        <div class="bg-blue-100 p-2 rounded-full mr-3">
          <i class="fas fa-search text-blue-600"></i>
        </div>
        <h2 class="text-xl font-semibold text-gray-800">Search & Filter Incidents</h2>
      </div>
      
      <form method="GET" class="space-y-4">
        <div class="grid md:grid-cols-6 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search by</label>
            <select name="by" id="by-driver" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="keyword"  {{ ($by ?? request('by','keyword'))==='keyword' ? 'selected' : '' }}>🔍 Keyword</option>
              <option value="status"   {{ ($by ?? request('by'))==='status' ? 'selected' : '' }}>📊 Status</option>
              <option value="severity" {{ ($by ?? request('by'))==='severity' ? 'selected' : '' }}>⚠️ Severity</option>
              <option value="vehicle"  {{ ($by ?? request('by'))==='vehicle' ? 'selected' : '' }}>🚛 Vehicle ID</option>
              <option value="assignee" {{ ($by ?? request('by'))==='assignee' ? 'selected' : '' }}>👤 Assignee</option>
              <option value="date"     {{ ($by ?? request('by'))==='date' ? 'selected' : '' }}>📅 Date</option>
            </select>
          </div>

          <div id="valueAreaDriver" class="md:col-span-4">
            <div data-role="keyword">
              <label class="block text-sm font-medium text-gray-700 mb-2">Search Keywords</label>
              <input type="text" name="q" value="{{ request('q') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search in title, description, or vehicle...">
            </div>

            <div data-role="status" style="display: none;">
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Statuses</option>
                @foreach ($statuses as $s)
                  <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                @endforeach
              </select>
            </div>

            <div data-role="severity" style="display: none;">
              <label class="block text-sm font-medium text-gray-700 mb-2">Severity Level</label>
              <select name="severity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Severities</option>
                @foreach ($severities as $s)
                  <option value="{{ $s }}" @selected(request('severity')===$s)>{{ $s }}</option>
                @endforeach
              </select>
            </div>

            <div data-role="vehicle" style="display: none;">
              <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle ID</label>
              <input type="text" name="vehicle" value="{{ request('vehicle') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., TRK-001, VAN-205">
            </div>

            <div data-role="assignee" style="display: none;">
              <label class="block text-sm font-medium text-gray-700 mb-2">Assigned Technician</label>
              <select name="assignee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Technicians</option>
                @foreach ($technicians as $t)
                  <option value="{{ $t->id }}" @selected(request('assignee') == $t->id)>{{ $t->name }}</option>
                @endforeach
              </select>
            </div>

            <div data-role="date" style="display: none;">
              <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
              <div class="grid grid-cols-2 gap-2">
                <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all">
              <i class="fas fa-search mr-2"></i>Search
            </button>
            <a href="{{ route('driver.incidents.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
              <i class="fas fa-undo mr-2"></i>Reset
            </a>
          </div>
        </div>

        <input type="hidden" name="sort" value="{{ request('sort','created_at') }}">
        <input type="hidden" name="dir"  value="{{ request('dir','desc') }}">
      </form>
    </div>

    @if (session('ok'))
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
          <i class="fas fa-check-circle text-green-600 mr-2"></i>
          <span class="text-green-800">{{ session('ok') }}</span>
        </div>
      </div>
    @endif

    <!-- Incidents Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-800">Recent Incidents</h2>
          <div class="text-sm text-gray-500">
            Total: {{ $incidents->total() ?? 0 }} incidents
          </div>
        </div>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              @php $dir = (request('sort')==='id' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <a href="{{ route('driver.incidents.index', array_merge(request()->query(), ['sort'=>'id','dir'=>$dir])) }}" class="flex items-center gap-1 hover:text-gray-700">
                  ID
                  @if(request('sort')==='id') <i class="fas fa-sort-{{ request('dir')==='asc' ? 'up' : 'down' }}"></i> @endif
                </a>
              </th>

              @php $dir = (request('sort')==='title' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <a href="{{ route('driver.incidents.index', array_merge(request()->query(), ['sort'=>'title','dir'=>$dir])) }}" class="flex items-center gap-1 hover:text-gray-700">
                  Incident Details
                  @if(request('sort')==='title') <i class="fas fa-sort-{{ request('dir')==='asc' ? 'up' : 'down' }}"></i> @endif
                </a>
              </th>

              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>

              @php $dir = (request('sort')==='severity' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <a href="{{ route('driver.incidents.index', array_merge(request()->query(), ['sort'=>'severity','dir'=>$dir])) }}" class="flex items-center gap-1 hover:text-gray-700">
                  Severity
                  @if(request('sort')==='severity') <i class="fas fa-sort-{{ request('dir')==='asc' ? 'up' : 'down' }}"></i> @endif
                </a>
              </th>

              @php $dir = (request('sort')==='status' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <a href="{{ route('driver.incidents.index', array_merge(request()->query(), ['sort'=>'status','dir'=>$dir])) }}" class="flex items-center gap-1 hover:text-gray-700">
                  Status
                  @if(request('sort')==='status') <i class="fas fa-sort-{{ request('dir')==='asc' ? 'up' : 'down' }}"></i> @endif
                </a>
              </th>

              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>

              @php $dir = (request('sort')==='created_at' && request('dir')==='asc') ? 'desc' : 'asc'; @endphp
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <a href="{{ route('driver.incidents.index', array_merge(request()->query(), ['sort'=>'created_at','dir'=>$dir])) }}" class="flex items-center gap-1 hover:text-gray-700">
                  Date
                  @if(request('sort')==='created_at') <i class="fas fa-sort-{{ request('dir')==='asc' ? 'up' : 'down' }}"></i> @endif
                </a>
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($incidents as $i)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">#{{ $i->id }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-start">
                    <div class="flex-shrink-0 mr-3">
                      @if($i->category === 'Fuel Low')
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                          <i class="fas fa-gas-pump text-yellow-600 text-sm"></i>
                        </div>
                      @elseif($i->category === 'Oil Low')
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                          <i class="fas fa-oil-can text-orange-600 text-sm"></i>
                        </div>
                      @elseif($i->category === 'Water/Coolant Low')
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                          <i class="fas fa-tint text-blue-600 text-sm"></i>
                        </div>
                      @elseif($i->category === 'Tire Issue')
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                          <i class="fas fa-circle text-gray-600 text-sm"></i>
                        </div>
                      @else
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                          <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                        </div>
                      @endif
                    </div>
                    <div>
                      <a href="{{ route('driver.incidents.show', $i) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                        {{ $i->title }}
                      </a>
                      @if($i->category)
                        <div class="text-xs text-gray-500 mt-1">{{ $i->category }}</div>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ $i->vehicle_identifier ?: '—' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if($i->severity === 'Critical')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      🔴 Critical
                    </span>
                  @elseif($i->severity === 'High')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                      🟠 High
                    </span>
                  @elseif($i->severity === 'Medium')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      🟡 Medium
                    </span>
                  @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      🟢 Low
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if($i->status === 'Open')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      📋 Open
                    </span>
                  @elseif($i->status === 'In Progress')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      🔧 In Progress
                    </span>
                  @elseif($i->status === 'Resolved')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      ✅ Resolved
                    </span>
                  @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ $i->status }}
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $i->assignee?->name ?? 'Unassigned' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div>{{ $i->created_at->format('M d, Y') }}</div>
                  <div class="text-xs">{{ $i->created_at->format('H:i') }}</div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No incidents found</h3>
                    <p class="text-gray-500">Use <strong>Service & Support → Report Incident</strong> to create a new incident.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if(isset($incidents) && method_exists($incidents, 'links'))
      <div class="flex justify-center">
        {{ $incidents->links() }}
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script>
  (function () {
    const by = document.getElementById('by-driver');
    const roles = document.querySelectorAll('#valueAreaDriver [data-role]');
    function sync() {
      roles.forEach(el => el.style.display = 'none');
      const pick = document.querySelector('#valueAreaDriver [data-role="' + by.value + '"]');
      if (pick) pick.style.display = 'block';
    }
    by.addEventListener('change', sync);
    sync();
  })();
</script>
@endpush
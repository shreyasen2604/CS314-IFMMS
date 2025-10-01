@extends('layouts.app')

@section('title', 'Incident #' . $incident->id . ' - IFMMS-ZAR')

@section('content')
<div class="container-fluid py-4">
  <div class="max-w-7xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="bg-red-100 p-3 rounded-full">
            @if($incident->category === 'Fuel Low' || $incident->category === 'Fuel System')
              <i class="fas fa-gas-pump text-red-600 text-xl"></i>
            @elseif($incident->category === 'Oil Low' || $incident->category === 'Engine')
              <i class="fas fa-oil-can text-red-600 text-xl"></i>
            @elseif($incident->category === 'Water/Coolant Low' || $incident->category === 'Cooling System')
              <i class="fas fa-tint text-red-600 text-xl"></i>
            @elseif($incident->category === 'Tire Issue' || $incident->category === 'Tires')
              <i class="fas fa-circle text-red-600 text-xl"></i>
            @elseif($incident->category === 'Electrical')
              <i class="fas fa-bolt text-red-600 text-xl"></i>
            @elseif($incident->category === 'Brakes' || $incident->category === 'Brake Issue')
              <i class="fas fa-hand-paper text-red-600 text-xl"></i>
            @else
              <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            @endif
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Incident #{{ $incident->id }}</h1>
            <p class="text-gray-600">{{ $incident->title }}</p>
          </div>
        </div>
        <div class="flex gap-3">
          <a href="{{ route('driver.incidents.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Incidents
          </a>
          @if($incident->status !== 'Closed')
            <button onclick="printReport()" class="flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
              <i class="fas fa-print mr-2"></i>Print
            </button>
          @endif
        </div>
      </div>
    </div>

    @if (session('ok'))
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
          <i class="fas fa-check-circle text-green-600 mr-2"></i>
          <span class="text-green-800">{{ session('ok') }}</span>
        </div>
      </div>
    @endif
    @if (session('err'))
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
          <span class="text-red-800">{{ session('err') }}</span>
        </div>
      </div>
    @endif
    @if ($errors->any())
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
          <span class="text-red-800">{{ $errors->first() }}</span>
        </div>
      </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Incident Overview -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Incident Overview</h2>
            <div class="flex items-center space-x-4">
              <!-- Severity Badge -->
              @if($incident->severity === 'Critical')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                  🔴 Critical
                </span>
              @elseif($incident->severity === 'High')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                  🟠 High
                </span>
              @elseif($incident->severity === 'Medium')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                  🟡 Medium
                </span>
              @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  🟢 Low
                </span>
              @endif

              <!-- Status Badge -->
              @if($incident->status === 'Open')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  📋 Open
                </span>
              @elseif($incident->status === 'In Progress')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                  🔧 In Progress
                </span>
              @elseif($incident->status === 'Resolved')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  ✅ Resolved
                </span>
              @elseif($incident->status === 'Closed')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                  🔒 Closed
                </span>
              @endif
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
              <p class="text-gray-900 whitespace-pre-line bg-gray-50 p-4 rounded-lg">{{ $incident->description }}</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Category</h3>
                <div class="flex items-center">
                  @if($incident->category === 'Engine')
                    <i class="fas fa-cog text-gray-600 mr-2"></i>
                  @elseif($incident->category === 'Electrical')
                    <i class="fas fa-bolt text-gray-600 mr-2"></i>
                  @elseif($incident->category === 'Brakes')
                    <i class="fas fa-hand-paper text-gray-600 mr-2"></i>
                  @elseif($incident->category === 'Tires')
                    <i class="fas fa-circle text-gray-600 mr-2"></i>
                  @else
                    <i class="fas fa-wrench text-gray-600 mr-2"></i>
                  @endif
                  <span class="text-gray-900">{{ $incident->category ?: 'Not specified' }}</span>
                </div>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Assigned Technician</h3>
                <div class="flex items-center">
                  <i class="fas fa-user text-gray-600 mr-2"></i>
                  <span class="text-gray-900">{{ $incident->assignee?->name ?? 'Unassigned' }}</span>
                </div>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Reported Date</h3>
                <div class="flex items-center">
                  <i class="fas fa-calendar text-gray-600 mr-2"></i>
                  <span class="text-gray-900">{{ $incident->created_at->format('M d, Y \a\t H:i') }}</span>
                </div>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Last Updated</h3>
                <div class="flex items-center">
                  <i class="fas fa-clock text-gray-600 mr-2"></i>
                  <span class="text-gray-900">{{ $incident->updated_at->format('M d, Y \a\t H:i') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vehicle Information -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-green-100 p-2 rounded-full mr-3">
              <i class="fas fa-truck text-green-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Vehicle Information</h2>
          </div>

          <div class="grid md:grid-cols-3 gap-6">
            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">Vehicle ID</h3>
              <div class="flex items-center">
                <i class="fas fa-id-card text-gray-600 mr-2"></i>
                <span class="text-gray-900 font-mono">{{ $incident->vehicle_identifier ?: 'Not specified' }}</span>
              </div>
            </div>

            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">Odometer Reading</h3>
              <div class="flex items-center">
                <i class="fas fa-tachometer-alt text-gray-600 mr-2"></i>
                <span class="text-gray-900">{{ $incident->odometer ? number_format($incident->odometer) . ' km' : 'Not recorded' }}</span>
              </div>
            </div>

            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">Fuel Level</h3>
              <div class="flex items-center">
                <i class="fas fa-gas-pump text-gray-600 mr-2"></i>
                <span class="text-gray-900">{{ $incident->fuel_level ?? 'Not recorded' }}</span>
              </div>
            </div>

            @if($incident->dtc_codes)
            <div class="md:col-span-3">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Diagnostic Trouble Codes</h3>
              <div class="flex items-center">
                <i class="fas fa-code text-gray-600 mr-2"></i>
                <div class="flex flex-wrap gap-2">
                  @foreach($incident->dtc_codes as $code)
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-sm font-mono rounded">{{ $code }}</span>
                  @endforeach
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>

        <!-- Location Information -->
        @if($incident->latitude || $incident->longitude || $incident->location_description)
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-purple-100 p-2 rounded-full mr-3">
              <i class="fas fa-map-marker-alt text-purple-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Location Information</h2>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            @if($incident->location_description)
            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">Location Description</h3>
              <div class="flex items-center">
                <i class="fas fa-map text-gray-600 mr-2"></i>
                <span class="text-gray-900">{{ $incident->location_description }}</span>
              </div>
            </div>
            @endif

            @if($incident->latitude && $incident->longitude)
            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">GPS Coordinates</h3>
              <div class="flex items-center">
                <i class="fas fa-crosshairs text-gray-600 mr-2"></i>
                <span class="text-gray-900 font-mono">{{ $incident->latitude }}, {{ $incident->longitude }}</span>
                <a href="https://maps.google.com/?q={{ $incident->latitude }},{{ $incident->longitude }}" target="_blank" class="ml-2 text-blue-600 hover:text-blue-800">
                  <i class="fas fa-external-link-alt"></i>
                </a>
              </div>
            </div>
            @endif

            @if($incident->weather_conditions || $incident->road_conditions)
            <div class="md:col-span-2 grid md:grid-cols-2 gap-6">
              @if($incident->weather_conditions)
              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Weather Conditions</h3>
                <div class="flex items-center">
                  <i class="fas fa-cloud text-gray-600 mr-2"></i>
                  <span class="text-gray-900">{{ $incident->weather_conditions }}</span>
                </div>
              </div>
              @endif

              @if($incident->road_conditions)
              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Road Conditions</h3>
                <div class="flex items-center">
                  <i class="fas fa-road text-gray-600 mr-2"></i>
                  <span class="text-gray-900">{{ $incident->road_conditions }}</span>
                </div>
              </div>
              @endif
            </div>
            @endif
          </div>
        </div>
        @endif

        <!-- Additional Notes -->
        @if($incident->additional_notes)
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-gray-100 p-2 rounded-full mr-3">
              <i class="fas fa-sticky-note text-gray-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Additional Notes</h2>
          </div>
          <p class="text-gray-900 whitespace-pre-line bg-gray-50 p-4 rounded-lg">{{ $incident->additional_notes }}</p>
        </div>
        @endif

        <!-- Updates Timeline -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-indigo-100 p-2 rounded-full mr-3">
              <i class="fas fa-history text-indigo-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Updates & Timeline</h2>
          </div>

          <div class="space-y-4">
            @forelse ($updates as $u)
              <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                  @if($u->type === 'comment')
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <i class="fas fa-comment text-blue-600 text-sm"></i>
                    </div>
                  @elseif($u->type === 'status_change')
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                      <i class="fas fa-exchange-alt text-green-600 text-sm"></i>
                    </div>
                  @else
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                      <i class="fas fa-info text-gray-600 text-sm"></i>
                    </div>
                  @endif
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-900">{{ $u->user?->name ?? 'System' }}</div>
                    <div class="text-xs text-gray-500">{{ $u->created_at->format('M d, Y \a\t H:i') }}</div>
                  </div>
                  @if($u->body)
                    <p class="mt-1 text-sm text-gray-700">{{ $u->body }}</p>
                  @endif
                  <div class="mt-1 text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $u->type) }}</div>
                </div>
              </div>
            @empty
              <div class="text-center py-8">
                <i class="fas fa-comments text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-500">No updates yet.</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
          
          <!-- Add Comment -->
          <form method="POST" action="{{ route('driver.incidents.comment', $incident) }}" class="space-y-4">
            @csrf
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Add Comment</label>
              <textarea name="body" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe any new symptoms, provide updates, or ask questions..."></textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all">
              <i class="fas fa-comment mr-2"></i>Post Comment
            </button>
          </form>

          @if ($incident->status === 'Resolved')
            <div class="mt-4 pt-4 border-t border-gray-200">
              <form method="POST" action="{{ route('driver.incidents.ack', $incident) }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all">
                  <i class="fas fa-check-circle mr-2"></i>Acknowledge & Close
                </button>
              </form>
              <p class="text-xs text-gray-500 mt-2">Confirm that the issue has been resolved to your satisfaction.</p>
            </div>
          @endif
        </div>

        <!-- Incident Statistics -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Incident Stats</h2>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Response Time</span>
              <span class="text-sm font-medium text-gray-900">
                @if($incident->assignee)
                  {{ $incident->created_at->diffForHumans($incident->updated_at) }}
                @else
                  Pending
                @endif
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Age</span>
              <span class="text-sm font-medium text-gray-900">{{ $incident->created_at->diffForHumans() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Updates</span>
              <span class="text-sm font-medium text-gray-900">{{ $updates->count() }}</span>
            </div>
          </div>
        </div>

        <!-- Emergency Contacts -->
        @if($incident->severity === 'Critical' || $incident->severity === 'High')
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
          <h2 class="text-lg font-semibold text-red-800 mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>Emergency Contacts
          </h2>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-red-700">Fleet Manager</span>
              <a href="tel:+1234567890" class="text-red-800 font-medium">+1 (234) 567-890</a>
            </div>
            <div class="flex justify-between">
              <span class="text-red-700">Roadside Assistance</span>
              <a href="tel:+1800123456" class="text-red-800 font-medium">+1 (800) 123-456</a>
            </div>
            <div class="flex justify-between">
              <span class="text-red-700">Emergency Services</span>
              <a href="tel:911" class="text-red-800 font-medium">911</a>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

  </div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script>
  function printReport() {
    window.print();
  }
</script>
@endpush
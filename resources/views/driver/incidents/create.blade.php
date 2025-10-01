@extends('layouts.app')

@section('title', 'Report Vehicle Incident - IFMMS-ZAR')

@section('content')
<div class="container-fluid">
  <div class="max-w-4xl mx-auto p-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="bg-red-100 p-3 rounded-full">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Report Vehicle Incident</h1>
            <p class="text-gray-600">Detailed incident reporting with photos and location</p>
          </div>
        </div>
        <a href="{{ route('driver.incidents.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
          <i class="fas fa-arrow-left mr-2"></i>Back to Incidents
        </a>
      </div>
    </div>

    @if ($errors->any())
      <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
          <span class="text-red-800 font-medium">Please fix the following errors:</span>
        </div>
        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('driver.incidents.store') }}" enctype="multipart/form-data" class="space-y-8">
      @csrf

      <!-- Basic Information -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
          <div class="bg-blue-100 p-2 rounded-full mr-3">
            <i class="fas fa-info-circle text-blue-600"></i>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">Basic Information</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Incident Title *</label>
            <input name="title" value="{{ old('title') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Brief description of the issue">
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Detailed Description *</label>
            <textarea name="description" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe what happened, when it occurred, and any symptoms you noticed...">{{ old('description') }}</textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Issue Category *</label>
            <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Select Category</option>
              <option value="Engine" {{ old('category') === 'Engine' ? 'selected' : '' }}>🔧 Engine Issues</option>
              <option value="Transmission" {{ old('category') === 'Transmission' ? 'selected' : '' }}>⚙️ Transmission</option>
              <option value="Brakes" {{ old('category') === 'Brakes' ? 'selected' : '' }}>🛑 Brake System</option>
              <option value="Electrical" {{ old('category') === 'Electrical' ? 'selected' : '' }}>⚡ Electrical</option>
              <option value="Fuel System" {{ old('category') === 'Fuel System' ? 'selected' : '' }}>⛽ Fuel System</option>
              <option value="Cooling System" {{ old('category') === 'Cooling System' ? 'selected' : '' }}>🌡️ Cooling System</option>
              <option value="Tires" {{ old('category') === 'Tires' ? 'selected' : '' }}>🛞 Tires & Wheels</option>
              <option value="Suspension" {{ old('category') === 'Suspension' ? 'selected' : '' }}>🔩 Suspension</option>
              <option value="Body/Exterior" {{ old('category') === 'Body/Exterior' ? 'selected' : '' }}>🚛 Body/Exterior</option>
              <option value="Safety Equipment" {{ old('category') === 'Safety Equipment' ? 'selected' : '' }}>🦺 Safety Equipment</option>
              <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>❓ Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Severity Level *</label>
            <select name="severity" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="Low" {{ old('severity') === 'Low' ? 'selected' : '' }}>🟢 Low - Vehicle operational, minor issue</option>
              <option value="Medium" {{ old('severity') === 'Medium' ? 'selected' : '' }}>🟡 Medium - Needs attention soon</option>
              <option value="High" {{ old('severity') === 'High' ? 'selected' : '' }}>🟠 High - Stop and repair soon</option>
              <option value="Critical" {{ old('severity') === 'Critical' ? 'selected' : '' }}>🔴 Critical - Stop immediately</option>
            </select>
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle ID *</label>
            <input name="vehicle_identifier" value="{{ old('vehicle_identifier') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., TRK-001">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Odometer (km)</label>
            <input type="number" name="odometer" value="{{ old('odometer') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Current mileage">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fuel Level</label>
            <select name="fuel_level" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Select Level</option>
              <option value="Full" {{ old('fuel_level') === 'Full' ? 'selected' : '' }}>🟢 Full (75-100%)</option>
              <option value="Three Quarters" {{ old('fuel_level') === 'Three Quarters' ? 'selected' : '' }}>🟡 3/4 (50-75%)</option>
              <option value="Half" {{ old('fuel_level') === 'Half' ? 'selected' : '' }}>🟠 Half (25-50%)</option>
              <option value="Quarter" {{ old('fuel_level') === 'Quarter' ? 'selected' : '' }}>🔴 1/4 (0-25%)</option>
              <option value="Empty" {{ old('fuel_level') === 'Empty' ? 'selected' : '' }}>⚫ Empty</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Location & Technical Details -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
          <div class="bg-purple-100 p-2 rounded-full mr-3">
            <i class="fas fa-map-marker-alt text-purple-600"></i>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">Location & Technical Details</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Location</label>
            <input name="location_description" value="{{ old('location_description') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Highway A1, near Exit 15">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              GPS Coordinates
              <button type="button" onclick="getLocation()" class="ml-2 text-blue-600 hover:text-blue-800">
                <i class="fas fa-crosshairs"></i> Get Current
              </button>
            </label>
            <div class="grid grid-cols-2 gap-2">
              <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Latitude">
              <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Longitude">
            </div>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Diagnostic Trouble Codes (DTC)</label>
            <input name="dtc_codes" value="{{ old('dtc_codes') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., P0217, P0300 (comma-separated)">
            <p class="text-xs text-gray-500 mt-1">Enter any error codes displayed on the dashboard or diagnostic tool</p>
          </div>
        </div>
      </div>

      <!-- Photo Upload -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
          <div class="bg-yellow-100 p-2 rounded-full mr-3">
            <i class="fas fa-camera text-yellow-600"></i>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">Photo Evidence</h2>
          <span class="ml-3 text-sm text-gray-500">Optional but recommended</span>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photos</label>
            <input type="file" name="photos[]" multiple accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">You can select multiple photos. Supported formats: JPG, PNG, GIF (max 5MB each)</p>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
              <i class="fas fa-lightbulb text-blue-600 mr-2 mt-1"></i>
              <div class="text-sm text-blue-800">
                <strong>Photo Tips:</strong>
                <ul class="mt-1 list-disc list-inside space-y-1">
                  <li>Take clear photos of the damaged area or warning lights</li>
                  <li>Include photos of the dashboard if warning lights are on</li>
                  <li>Capture the vehicle's license plate for identification</li>
                  <li>Show the surrounding area if location is relevant</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Information -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
          <div class="bg-gray-100 p-2 rounded-full mr-3">
            <i class="fas fa-clipboard-list text-gray-600"></i>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">Additional Information</h2>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Weather Conditions</label>
            <select name="weather_conditions" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Select Conditions</option>
              <option value="Clear" {{ old('weather_conditions') === 'Clear' ? 'selected' : '' }}>☀️ Clear/Sunny</option>
              <option value="Cloudy" {{ old('weather_conditions') === 'Cloudy' ? 'selected' : '' }}>☁️ Cloudy</option>
              <option value="Rainy" {{ old('weather_conditions') === 'Rainy' ? 'selected' : '' }}>🌧️ Rainy</option>
              <option value="Foggy" {{ old('weather_conditions') === 'Foggy' ? 'selected' : '' }}>🌫️ Foggy</option>
              <option value="Snowy" {{ old('weather_conditions') === 'Snowy' ? 'selected' : '' }}>❄️ Snowy</option>
              <option value="Windy" {{ old('weather_conditions') === 'Windy' ? 'selected' : '' }}>💨 Windy</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Road Conditions</label>
            <select name="road_conditions" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Select Conditions</option>
              <option value="Good" {{ old('road_conditions') === 'Good' ? 'selected' : '' }}>✅ Good</option>
              <option value="Fair" {{ old('road_conditions') === 'Fair' ? 'selected' : '' }}>⚠️ Fair</option>
              <option value="Poor" {{ old('road_conditions') === 'Poor' ? 'selected' : '' }}>❌ Poor</option>
              <option value="Construction" {{ old('road_conditions') === 'Construction' ? 'selected' : '' }}>🚧 Construction Zone</option>
              <option value="Wet" {{ old('road_conditions') === 'Wet' ? 'selected' : '' }}>💧 Wet</option>
              <option value="Icy" {{ old('road_conditions') === 'Icy' ? 'selected' : '' }}>🧊 Icy</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
            <textarea name="additional_notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Any other relevant information...">{{ old('additional_notes') }}</textarea>
          </div>
        </div>
      </div>

      <!-- Submit Section -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-600">
            <i class="fas fa-info-circle mr-1"></i>
            All fields marked with * are required
          </div>
          <div class="flex gap-4">
            <a href="{{ route('driver.incidents.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
              <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all shadow-md">
              <i class="fas fa-paper-plane mr-2"></i>Submit Incident Report
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script>
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        document.querySelector('input[name="latitude"]').value = position.coords.latitude;
        document.querySelector('input[name="longitude"]').value = position.coords.longitude;
      }, function(error) {
        alert('Unable to get location: ' + error.message);
      });
    } else {
      alert('Geolocation is not supported by this browser.');
    }
  }

  // Auto-generate title based on category and severity
  document.querySelector('select[name="category"]').addEventListener('change', function() {
    const severity = document.querySelector('select[name="severity"]').value;
    const category = this.value;
    const titleField = document.querySelector('input[name="title"]');

    if (category && severity && !titleField.value) {
      titleField.value = `${severity} ${category} Issue`;
    }
  });

  document.querySelector('select[name="severity"]').addEventListener('change', function() {
    const category = document.querySelector('select[name="category"]').value;
    const severity = this.value;
    const titleField = document.querySelector('input[name="title"]');

    if (category && severity && !titleField.value) {
      titleField.value = `${severity} ${category} Issue`;
    }
  });
</script>
@endpush

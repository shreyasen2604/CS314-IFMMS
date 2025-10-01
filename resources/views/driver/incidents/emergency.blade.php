<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Emergency Report — IFMMS-ZAR</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-red-50 to-orange-100">
  @php
    // Get the driver's assigned vehicle
    $driverVehicle = \App\Models\Vehicle::where('driver_id', auth()->id())->first();
  @endphp
  
  <div class="max-w-4xl mx-auto p-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="bg-red-100 p-3 rounded-full">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Emergency Vehicle Report</h1>
            <p class="text-gray-600">Quick emergency reporting and detailed incident forms</p>
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

    <!-- Report Type Selection -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <div class="flex items-center mb-6">
        <div class="bg-blue-100 p-2 rounded-full mr-3">
          <i class="fas fa-clipboard-check text-blue-600"></i>
        </div>
        <h2 class="text-xl font-semibold text-gray-800">Select Report Type</h2>
      </div>
      
      <div class="grid md:grid-cols-2 gap-6">
        <button onclick="showQuickReport()" id="quickReportBtn" class="p-6 border-2 border-red-200 rounded-xl hover:border-red-400 hover:bg-red-50 transition-all text-left group">
          <div class="flex items-center mb-3">
            <div class="bg-red-100 p-2 rounded-full mr-3 group-hover:bg-red-200">
              <i class="fas fa-bolt text-red-600 text-lg"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Quick Emergency Report</h3>
          </div>
          <p class="text-gray-600 text-sm">Fast checkbox-based reporting for immediate emergencies</p>
          <div class="mt-3 text-red-600 text-sm font-medium">
            <i class="fas fa-clock mr-1"></i>~30 seconds
          </div>
        </button>

        <button onclick="showDetailedReport()" id="detailedReportBtn" class="p-6 border-2 border-blue-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-all text-left group">
          <div class="flex items-center mb-3">
            <div class="bg-blue-100 p-2 rounded-full mr-3 group-hover:bg-blue-200">
              <i class="fas fa-file-alt text-blue-600 text-lg"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Detailed Incident Report</h3>
          </div>
          <p class="text-gray-600 text-sm">Comprehensive reporting with photos and location data</p>
          <div class="mt-3 text-blue-600 text-sm font-medium">
            <i class="fas fa-clock mr-1"></i>~3-5 minutes
          </div>
        </button>
      </div>
    </div>

    <!-- Quick Emergency Report Form -->
    <div id="quickReportForm" class="hidden">
      <form method="POST" action="{{ route('driver.incidents.emergency.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="report_type" value="quick">

        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-red-100 p-2 rounded-full mr-3">
              <i class="fas fa-bolt text-red-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Quick Emergency Report</h2>
            <span class="ml-3 px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full">URGENT</span>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Vehicle ID *
                @if($driverVehicle)
                  <span class="ml-2 text-green-600 text-xs">
                    <i class="fas fa-check-circle"></i> Auto-filled
                  </span>
                @endif
              </label>
              <input name="vehicle_identifier" 
                     value="{{ old('vehicle_identifier', $driverVehicle ? $driverVehicle->vehicle_number : '') }}" 
                     required 
                     class="w-full px-4 py-3 border {{ $driverVehicle ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                     placeholder="{{ $driverVehicle ? '' : 'Enter vehicle ID manually (e.g., TRK-001)' }}"
                     {{ $driverVehicle ? 'readonly' : '' }}>
              @if($driverVehicle)
                <p class="text-xs text-green-600 mt-1">
                  {{ $driverVehicle->make }} {{ $driverVehicle->model }} ({{ $driverVehicle->license_plate }})
                </p>
              @else
                <p class="text-xs text-amber-600 mt-1">
                  <i class="fas fa-info-circle"></i> No vehicle assigned. Please enter vehicle ID manually.
                </p>
              @endif
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Current Location
                <span id="quickLocationStatus" class="ml-2 text-xs text-gray-500"></span>
              </label>
              <div class="flex">
                <input name="location_description" 
                       id="quickLocationDescription"
                       value="{{ old('location_description') }}" 
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                       placeholder="Click GPS button to auto-detect">
                <button type="button" onclick="getQuickLocation()" class="px-4 py-3 bg-red-600 text-white rounded-r-lg hover:bg-red-700 transition-colors" title="Get GPS Location">
                  <i class="fas fa-crosshairs"></i>
                </button>
              </div>
              <input type="hidden" name="latitude" id="quickLatitude">
              <input type="hidden" name="longitude" id="quickLongitude">
            </div>
          </div>

          <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-4">Emergency Type (Select all that apply) *</label>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
              <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="emergency_types[]" value="fuel_low" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <div class="flex items-center">
                  <i class="fas fa-gas-pump text-orange-500 mr-2"></i>
                  <span class="text-sm font-medium">Fuel Low</span>
                </div>
              </label>

              <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="emergency_types[]" value="engine_trouble" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <div class="flex items-center">
                  <i class="fas fa-cog text-red-500 mr-2"></i>
                  <span class="text-sm font-medium">Engine Trouble</span>
                </div>
              </label>

              <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="emergency_types[]" value="electrical_problem" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <div class="flex items-center">
                  <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                  <span class="text-sm font-medium">Electrical Problem</span>
                </div>
              </label>

              <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="emergency_types[]" value="oil_low" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <div class="flex items-center">
                  <i class="fas fa-oil-can text-black mr-2"></i>
                  <span class="text-sm font-medium">Oil Low</span>
                </div>
              </label>

              <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="emergency_types[]" value="tire_issues" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <div class="flex items-center">
                  <i class="fas fa-circle text-gray-600 mr-2"></i>
                  <span class="text-sm font-medium">Tire Issues</span>
                </div>
              </label>

              <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="emergency_types[]" value="overheating" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <div class="flex items-center">
                  <i class="fas fa-thermometer-full text-red-500 mr-2"></i>
                  <span class="text-sm font-medium">Overheating</span>
                </div>
              </label>
            </div>
          </div>

          <div class="mt-6">
            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <input type="checkbox" name="emergency_types[]" value="other" id="otherCheckbox" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
              <div class="flex items-center">
                <i class="fas fa-question-circle text-gray-500 mr-2"></i>
                <span class="text-sm font-medium">Other</span>
              </div>
            </label>
            <div id="otherTextDiv" class="hidden mt-3">
              <input name="other_description" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Please describe the emergency...">
            </div>
          </div>

          <div class="mt-6">
            <label class="flex items-center">
              <input type="checkbox" name="needs_assistance" value="1" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
              <span class="text-sm font-medium text-gray-700">I need immediate roadside assistance</span>
            </label>
          </div>

          <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
            <button type="button" onclick="hideAllForms()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
              <i class="fas fa-arrow-left mr-2"></i>Back
            </button>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all shadow-md">
              <i class="fas fa-paper-plane mr-2"></i>Submit Emergency Report
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Detailed Incident Report Form -->
    <div id="detailedReportForm" class="hidden">
      <form method="POST" action="{{ route('driver.incidents.emergency.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <input type="hidden" name="report_type" value="detailed">

        <!-- Basic Information -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-blue-100 p-2 rounded-full mr-3">
              <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Vehicle & Incident Information</h2>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Vehicle ID *
                @if($driverVehicle)
                  <span class="ml-2 text-green-600 text-xs">
                    <i class="fas fa-check-circle"></i> Auto-filled
                  </span>
                @endif
              </label>
              <input name="vehicle_identifier" 
                     value="{{ old('vehicle_identifier', $driverVehicle ? $driverVehicle->vehicle_number : '') }}" 
                     required 
                     class="w-full px-4 py-3 border {{ $driverVehicle ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                     placeholder="{{ $driverVehicle ? '' : 'Enter vehicle ID manually (e.g., TRK-001)' }}"
                     {{ $driverVehicle ? 'readonly' : '' }}>
              @if($driverVehicle)
                <p class="text-xs text-green-600 mt-1">
                  {{ $driverVehicle->make }} {{ $driverVehicle->model }} ({{ $driverVehicle->license_plate }})
                </p>
              @else
                <p class="text-xs text-amber-600 mt-1">
                  <i class="fas fa-info-circle"></i> No vehicle assigned. Please enter vehicle ID manually.
                </p>
              @endif
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Incident Type *</label>
              <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Select Incident Type</option>
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
              <label class="block text-sm font-medium text-gray-700 mb-2">Date & Time *</label>
              <input type="datetime-local" name="incident_datetime" value="{{ old('incident_datetime', now()->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Location *
                <button type="button" onclick="getDetailedLocation()" class="ml-2 text-blue-600 hover:text-blue-800">
                  <i class="fas fa-crosshairs"></i> GPS
                </button>
              </label>
              <input name="location_description" value="{{ old('location_description') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Highway A1, near Exit 15">
              <input type="hidden" name="latitude" id="detailedLatitude">
              <input type="hidden" name="longitude" id="detailedLongitude">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
              <textarea name="description" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe what happened, when it occurred, and any symptoms you noticed...">{{ old('description') }}</textarea>
            </div>
          </div>
        </div>

        <!-- Severity & Assistance -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-orange-100 p-2 rounded-full mr-3">
              <i class="fas fa-exclamation-triangle text-orange-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Severity & Assistance</h2>
          </div>

          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-4">Severity Level *</label>
              <div class="grid md:grid-cols-2 gap-4">
                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="radio" name="severity" value="P4" {{ old('severity') === 'P4' ? 'checked' : '' }} class="mr-3 h-4 w-4 text-green-600 focus:ring-green-500">
                  <div>
                    <div class="flex items-center">
                      <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                      <span class="font-medium">Low Priority</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Vehicle operational, minor issue</p>
                  </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="radio" name="severity" value="P3" {{ old('severity') === 'P3' ? 'checked' : '' }} class="mr-3 h-4 w-4 text-yellow-600 focus:ring-yellow-500">
                  <div>
                    <div class="flex items-center">
                      <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                      <span class="font-medium">Medium Priority</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Needs attention soon</p>
                  </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="radio" name="severity" value="P2" {{ old('severity') === 'P2' ? 'checked' : '' }} class="mr-3 h-4 w-4 text-orange-600 focus:ring-orange-500">
                  <div>
                    <div class="flex items-center">
                      <span class="w-3 h-3 bg-orange-500 rounded-full mr-2"></span>
                      <span class="font-medium">High Priority</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Stop and repair soon</p>
                  </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="radio" name="severity" value="P1" {{ old('severity') === 'P1' ? 'checked' : '' }} class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500">
                  <div>
                    <div class="flex items-center">
                      <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                      <span class="font-medium">Critical Priority</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Stop immediately</p>
                  </div>
                </label>
              </div>
            </div>

            <div>
              <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <input type="checkbox" name="needs_assistance" value="1" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <div class="flex items-center">
                  <i class="fas fa-hands-helping text-blue-500 mr-2"></i>
                  <span class="text-sm font-medium">I need immediate roadside assistance</span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- File Upload -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center mb-6">
            <div class="bg-purple-100 p-2 rounded-full mr-3">
              <i class="fas fa-camera text-purple-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Photo Evidence</h2>
            <span class="ml-3 text-sm text-gray-500">Optional but recommended</span>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photos</label>
            <input type="file" name="photos[]" multiple accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">You can select multiple photos. Supported formats: JPG, PNG, GIF (max 5MB each)</p>
          </div>
        </div>

        <div class="flex justify-between items-center">
          <button type="button" onclick="hideAllForms()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
          </button>
          <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-md">
            <i class="fas fa-paper-plane mr-2"></i>Submit Detailed Report
          </button>
        </div>
      </form>
    </div>

    <!-- Success Confirmation (Hidden by default) -->
    <div id="successConfirmation" class="hidden">
      <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="mb-6">
          <div class="bg-green-100 p-4 rounded-full w-20 h-20 mx-auto mb-4">
            <i class="fas fa-check-circle text-green-600 text-4xl"></i>
          </div>
          <h2 class="text-2xl font-bold text-gray-800 mb-2">Report Submitted Successfully!</h2>
          <p class="text-gray-600">Your emergency report has been received and is being processed.</p>
        </div>

        <div class="bg-gray-50 rounded-xl p-6 mb-6">
          <div class="grid md:grid-cols-3 gap-6 text-center">
            <div>
              <div class="text-2xl font-bold text-blue-600" id="reportId">ER-2024-001</div>
              <div class="text-sm text-gray-600">Report ID</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-orange-600" id="responseTime">15-30 min</div>
              <div class="text-sm text-gray-600">Est. Response Time</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-green-600">Active</div>
              <div class="text-sm text-gray-600">Status</div>
            </div>
          </div>
        </div>

        <div class="flex justify-center space-x-4">
          <a href="{{ route('driver.incidents.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-list mr-2"></i>View All Reports
          </a>
          <button onclick="location.reload()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-plus mr-2"></i>New Report
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showQuickReport() {
      hideAllForms();
      document.getElementById('quickReportForm').classList.remove('hidden');
      document.getElementById('quickReportBtn').classList.add('border-red-400', 'bg-red-50');
      document.getElementById('detailedReportBtn').classList.remove('border-blue-400', 'bg-blue-50');
    }

    function showDetailedReport() {
      hideAllForms();
      document.getElementById('detailedReportForm').classList.remove('hidden');
      document.getElementById('detailedReportBtn').classList.add('border-blue-400', 'bg-blue-50');
      document.getElementById('quickReportBtn').classList.remove('border-red-400', 'bg-red-50');
    }

    function hideAllForms() {
      document.getElementById('quickReportForm').classList.add('hidden');
      document.getElementById('detailedReportForm').classList.add('hidden');
      document.getElementById('successConfirmation').classList.add('hidden');
      document.getElementById('quickReportBtn').classList.remove('border-red-400', 'bg-red-50');
      document.getElementById('detailedReportBtn').classList.remove('border-blue-400', 'bg-blue-50');
    }

    function showSuccess(reportId, responseTime) {
      hideAllForms();
      document.getElementById('reportId').textContent = reportId;
      document.getElementById('responseTime').textContent = responseTime;
      document.getElementById('successConfirmation').classList.remove('hidden');
    }

    function getQuickLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          document.getElementById('quickLatitude').value = position.coords.latitude;
          document.getElementById('quickLongitude').value = position.coords.longitude;
          // Optional: Update location description with coordinates
          const locationField = document.querySelector('#quickReportForm input[name="location_description"]');
          if (!locationField.value) {
            locationField.value = `GPS: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
          }
        }, function(error) {
          alert('Unable to get location: ' + error.message);
        });
      } else {
        alert('Geolocation is not supported by this browser.');
      }
    }

    function getDetailedLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          document.getElementById('detailedLatitude').value = position.coords.latitude;
          document.getElementById('detailedLongitude').value = position.coords.longitude;
          // Optional: Update location description with coordinates
          const locationField = document.querySelector('#detailedReportForm input[name="location_description"]');
          if (!locationField.value) {
            locationField.value = `GPS: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
          }
        }, function(error) {
          alert('Unable to get location: ' + error.message);
        });
      } else {
        alert('Geolocation is not supported by this browser.');
      }
    }

    // Show/hide other text input
    document.getElementById('otherCheckbox').addEventListener('change', function() {
      const otherDiv = document.getElementById('otherTextDiv');
      if (this.checked) {
        otherDiv.classList.remove('hidden');
      } else {
        otherDiv.classList.add('hidden');
      }
    });

    // Auto-generate title for detailed report
    document.querySelector('#detailedReportForm select[name="category"]').addEventListener('change', function() {
      const severity = document.querySelector('#detailedReportForm input[name="severity"]:checked');
      const category = this.value;
      
      if (category && severity) {
        // This could be used to auto-populate a title field if needed
        console.log(`${severity.value} ${category} Issue`);
      }
    });

    // Check if we should show success (for demo purposes)
    @if(session('success'))
      showSuccess('{{ session('report_id', 'ER-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)) }}', '{{ session('response_time', '15-30 min') }}');
    @endif
  </script>
</body>
</html>
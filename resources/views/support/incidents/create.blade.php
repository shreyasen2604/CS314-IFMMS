@extends('layouts.app')

@section('title', 'Report Incident - IFMMS')

@section('content')

@php
    $role = strtolower(auth()->user()->role ?? '');

    $dashMap = [
        'driver'     => 'driver.dashboard',
        'admin'      => 'dashboard',          
        'technician' => 'dashboard',          
    ];

    $target  = $dashMap[$role] ?? 'dashboard';
    $backUrl = \Illuminate\Support\Facades\Route::has($target) ? route($target) : url('/'); 
@endphp

<div class="container-fluid py-4 content-blur">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-danger bg-opacity-10 me-3">
                                <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 fw-bold">Report New Incident</h1>
                                <p class="text-muted mb-0">Document and report a fleet incident</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                        @if (strtolower(auth()->user()->role) === 'driver')
                            <button type="button"
                                    class="btn btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#quickEmergencyModal">
                                <i class="fas fa-bolt me-2"></i> Quick Emergency Reporting
                            </button>
                        @endif


                        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                    </div>
                </div>
            </div>

            @php
            $driverVehicle = \App\Models\Vehicle::where('driver_id', auth()->id())->first();
            @endphp

            <!-- Quick Emergency Modal -->
            <div class="modal fade" id="quickEmergencyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-bolt me-2"></i>Quick Emergency Report</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="quickEmergencyForm" data-store-url="{{ route('driver.incidents.emergency.store') }}">
                    @csrf
                    <input type="hidden" name="report_type" value="quick">
                    <input type="hidden" name="latitude"  id="qeLat">
                    <input type="hidden" name="longitude" id="qeLng">

                    <div class="modal-body">
                    <div id="qeAlert" class="alert alert-danger d-none mb-3"></div>
                    <div id="qeSuccess" class="alert alert-success d-none mb-3">
                        <strong>Submitted!</strong> Report <span id="qeReportId"></span>.
                        ETA: <span id="qeResponseTime"></span>.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vehicle ID <span class="text-danger">*</span></label>
                        <input name="vehicle_identifier"
                            class="form-control {{ $driverVehicle ? 'is-valid' : '' }}"
                            value="{{ $driverVehicle?->vehicle_number }}"
                            placeholder="e.g., TRK-001" required {{ $driverVehicle ? 'readonly' : '' }}>
                        @if($driverVehicle)
                        <div class="form-text text-success">Auto-filled from your assigned vehicle.</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Location</label>
                        <div class="input-group">
                        <input name="location_description" id="qeLoc" class="form-control"
                                placeholder="Tap GPS to fill automatically">
                        <button type="button" class="btn btn-danger" id="qeGpsBtn">
                            <i class="fas fa-crosshairs me-1"></i> GPS
                        </button>
                        </div>
                        <div class="form-text" id="qeLocStatus"></div>
                    </div>

                    @php
                        $opts = [
                        'fuel_low' => 'Fuel Low',
                        'engine_trouble' => 'Engine Trouble',
                        'electrical_problem' => 'Electrical Problem',
                        'oil_low' => 'Oil Low',
                        'tire_issues' => 'Tire Issues',
                        'overheating' => 'Overheating',
                        ];
                    @endphp

                    <div class="mb-2">
                        <label class="form-label d-block">Emergency Type <span class="text-danger">*</span></label>
                        <div class="row g-2">
                        @foreach($opts as $val => $label)
                            <div class="col-6">
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="emergency_types[]" value="{{ $val }}">
                                <span class="form-check-label">{{ $label }}</span>
                            </label>
                            </div>
                        @endforeach

                        <div class="col-12">
                            <label class="form-check">
                            <input class="form-check-input" type="checkbox" id="qeOtherChk" name="emergency_types[]" value="other">
                            <span class="form-check-label">Other</span>
                            </label>
                            <input class="form-control mt-2 d-none" id="qeOtherTxt" name="other_description"
                                placeholder="Describe the emergency">
                        </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="needs_assistance" value="1">
                        <span class="form-check-label">I need immediate roadside assistance</span>
                        </label>
                    </div>
                    </div>

                    <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="qeSpinner"></span>
                        Submit
                    </button>
                    </div>
                </form>
                </div>
            </div>
            </div>
            <!-- /Quick Emergency Modal -->


            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('support.incidents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Incident Information -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Incident Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Incident Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="accident" {{ old('type') == 'accident' ? 'selected' : '' }}>🚗 Accident</option>
                                    <option value="breakdown" {{ old('type') == 'breakdown' ? 'selected' : '' }}>🔧 Breakdown</option>
                                    <option value="theft" {{ old('type') == 'theft' ? 'selected' : '' }}>🚨 Theft</option>
                                    <option value="vandalism" {{ old('type') == 'vandalism' ? 'selected' : '' }}>💔 Vandalism</option>
                                    <option value="traffic_violation" {{ old('type') == 'traffic_violation' ? 'selected' : '' }}>🚦 Traffic Violation</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>📋 Other</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Severity <span class="text-danger">*</span></label>
                                <select name="severity" class="form-select @error('severity') is-invalid @enderror" required>
                                    <option value="">Select Severity</option>
                                    <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}>🟢 Minor</option>
                                    <option value="moderate" {{ old('severity') == 'moderate' ? 'selected' : '' }}>🟡 Moderate</option>
                                    <option value="major" {{ old('severity') == 'major' ? 'selected' : '' }}>🟠 Major</option>
                                    <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                                </select>
                                @error('severity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Incident Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="incident_date" class="form-control @error('incident_date') is-invalid @enderror" 
                                       value="{{ old('incident_date', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('incident_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Vehicle</label>
                                <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                                    <option value="">Select Vehicle (if applicable)</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Driver</label>
                                <select name="driver_id" class="form-select @error('driver_id') is-invalid @enderror">
                                    <option value="">Select Driver (if applicable)</option>
                                    @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} - {{ $driver->email }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('driver_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                       value="{{ old('location') }}" placeholder="Enter incident location" required>
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Provide detailed description of the incident..." required>{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Immediate Action Taken</label>
                                <textarea name="immediate_action_taken" rows="3" class="form-control @error('immediate_action_taken') is-invalid @enderror" 
                                          placeholder="Describe any immediate actions taken...">{{ old('immediate_action_taken') }}</textarea>
                                @error('immediate_action_taken')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- External Reports -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">External Reports & Documentation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="police_report_filed" class="form-check-input" id="policeReport" 
                                           {{ old('police_report_filed') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="policeReport">
                                        Police Report Filed
                                    </label>
                                </div>
                                <input type="text" name="police_report_number" class="form-control" 
                                       placeholder="Police Report Number" value="{{ old('police_report_number') }}"
                                       id="policeReportNumber" {{ old('police_report_filed') ? '' : 'disabled' }}>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="insurance_notified" class="form-check-input" id="insuranceNotified" 
                                           {{ old('insurance_notified') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="insuranceNotified">
                                        Insurance Notified
                                    </label>
                                </div>
                                <input type="text" name="insurance_claim_number" class="form-control" 
                                       placeholder="Insurance Claim Number" value="{{ old('insurance_claim_number') }}"
                                       id="insuranceClaimNumber" {{ old('insurance_notified') ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Attachments</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Photos</label>
                                <input type="file" name="photos[]" class="form-control @error('photos.*') is-invalid @enderror" 
                                       multiple accept="image/*">
                                <small class="form-text text-muted">Upload incident photos (max 5MB each)</small>
                                @error('photos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Documents</label>
                                <input type="file" name="documents[]" class="form-control @error('documents.*') is-invalid @enderror" 
                                       multiple accept=".pdf,.doc,.docx">
                                <small class="form-text text-muted">Upload related documents (max 10MB each)</small>
                                @error('documents.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden GPS Coordinates -->
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <!-- Submit Buttons -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="location.href='{{ $backUrl }}'">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-2"></i>Submit Incident Report
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;

    body.modal-open #pageWrap { filter: blur(4px); transition: filter .2s ease; }
    .modal-backdrop.show { backdrop-filter: blur(2px); background: rgba(0,0,0,.2); }


}
</style>

<script>
// Toggle police report number field
document.getElementById('policeReport').addEventListener('change', function() {
    document.getElementById('policeReportNumber').disabled = !this.checked;
});

// Toggle insurance claim number field
document.getElementById('insuranceNotified').addEventListener('change', function() {
    document.getElementById('insuranceClaimNumber').disabled = !this.checked;
});

// Get user's location if available
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById('latitude').value = position.coords.latitude;
        document.getElementById('longitude').value = position.coords.longitude;
    });
}
</script>

<script>
(() => {
  const form   = document.getElementById('quickEmergencyForm');
  if (!form) return;

  const alertB = document.getElementById('qeAlert');
  const okB    = document.getElementById('qeSuccess');
  const spin   = document.getElementById('qeSpinner');
  const gpsBtn = document.getElementById('qeGpsBtn');
  const loc    = document.getElementById('qeLoc');
  const locSt  = document.getElementById('qeLocStatus');
  const lat    = document.getElementById('qeLat');
  const lng    = document.getElementById('qeLng');
  const otherChk = document.getElementById('qeOtherChk');
  const otherTxt = document.getElementById('qeOtherTxt');

  // Show/hide "Other" text field
  otherChk?.addEventListener('change', () => {
    otherTxt.classList.toggle('d-none', !otherChk.checked);
  });

  // GPS helper
  gpsBtn?.addEventListener('click', () => {
    if (!navigator.geolocation) { locSt.textContent = 'Geolocation not supported.'; return; }
    locSt.textContent = 'Getting GPS...';
    navigator.geolocation.getCurrentPosition((pos)=>{
      lat.value = pos.coords.latitude; lng.value = pos.coords.longitude;
      if(!loc.value) loc.value = `GPS: ${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)}`;
      locSt.textContent = 'GPS captured.';
    }, (err)=>{ locSt.textContent = 'Unable to get location: ' + err.message; });
  });

  // Submit via AJAX (stay on page)
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    alertB.classList.add('d-none'); okB.classList.add('d-none');
    spin.classList.remove('d-none');

    const url = form.dataset.storeUrl;
    const fd  = new FormData(form);

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: fd
      });

      const data = await res.json().catch(()=> ({}));

      if (!res.ok) {
        const msg = data.message || 'Please check the form.';
        const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : '';
        alertB.innerHTML = '<strong>Error:</strong> ' + msg + (errs ? '<br>' + errs : '');
        alertB.classList.remove('d-none');
        spin.classList.add('d-none');
        return;
      }

      document.getElementById('qeReportId').textContent    = data.report_id || ('#' + (data.id ?? ''));
      document.getElementById('qeResponseTime').textContent = data.response_time || '—';
      okB.classList.remove('d-none');
      spin.classList.add('d-none');

      // Auto-close after a short delay
      setTimeout(()=> {
        const modalEl = document.getElementById('quickEmergencyModal');
        const modal   = bootstrap.Modal.getInstance(modalEl);
        modal?.hide();
        form.reset();
        otherTxt.classList.add('d-none');
        locSt.textContent = '';
      }, 1800);

    } catch (err) {
      alertB.textContent = 'Network error. Please try again.';
      alertB.classList.remove('d-none');
      spin.classList.add('d-none');
    }
  });
})();
</script>


@endsection
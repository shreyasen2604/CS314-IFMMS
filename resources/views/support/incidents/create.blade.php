@extends('layouts.app')

@section('title', 'Report Incident - IFMMS')

@section('content')
<div class="container-fluid py-4">
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
                        <a href="{{ route('support.incidents.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>

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

                            <div class="col-md-6">
                                <label class="form-label">Estimated Damage Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="estimated_damage_cost" class="form-control @error('estimated_damage_cost') is-invalid @enderror" 
                                           value="{{ old('estimated_damage_cost') }}" step="0.01" placeholder="0.00">
                                </div>
                                @error('estimated_damage_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                            <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
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
@endsection
@extends('layouts.app')

@section('title', 'Create Service Request - IFMMS')

@section('content')

@php
    $backUrl = auth()->user()->role === 'Driver'
        ? route('driver.dashboard')      
        : route('support.service-requests.index');
@endphp


<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-primary bg-opacity-10 me-3">
                                <i class="fas fa-plus-circle text-primary fs-4"></i>
                            </div>
                            <div>
                                <h1 class="h3 mb-1 fw-bold">Create Service Request</h1>
                                <p class="text-muted mb-0">Submit a new service or support request</p>
                            </div>
                        </div>
                        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
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
            <form action="{{ route('support.service-requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Request Details</h5>
                        
                        <div class="row g-3">
                            <!-- Category -->
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    <option value="breakdown" {{ old('category') == 'breakdown' ? 'selected' : '' }}>
                                        🚨 Breakdown
                                    </option>
                                    <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>
                                        🔧 Maintenance
                                    </option>
                                    <option value="inspection" {{ old('category') == 'inspection' ? 'selected' : '' }}>
                                        🔍 Inspection
                                    </option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>
                                        📋 Other
                                    </option>
                                </select>
                                @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        🟢 Low - Can wait
                                    </option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                        🟡 Medium - Soon as possible
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        🟠 High - Urgent
                                    </option>
                                    <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>
                                        🔴 Critical - Immediate attention
                                    </option>
                                </select>
                                @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- VEHICLE (role-aware) --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Vehicle
                                    @if($mode === 'admin') <span class="text-danger">*</span> @endif
                                </label>

                                @if($mode === 'driver')
                                    @if($assignedVehicle)
                                        <input type="text" class="form-control" 
                                            value="{{ $assignedVehicle->registration_number }} - {{ $assignedVehicle->make }} {{ $assignedVehicle->model }}"
                                            readonly>
                                        <input type="hidden" name="vehicle_id" value="{{ $assignedVehicle->id }}">
                                    @else
                                        <input type="text" class="form-control is-invalid" value="No vehicle assigned" readonly>
                                        <div class="invalid-feedback">Please contact an administrator to assign a vehicle to your account.</div>
                                    @endif
                                @else
                                    <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                                {{ $vehicle->registration_number }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            {{-- LOCATION (structured -> still saved to your existing `location` column) --}}
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <div class="d-flex gap-2">
                                    <select id="location_type" class="form-select" style="max-width: 220px;">
                                        <option value="" selected disabled>Select</option>
                                        <option value="Depot" @selected(str_starts_with(old('location'), 'Depot'))>Depot</option>
                                        <option value="Workshop" @selected(str_starts_with(old('location'), 'Workshop'))>Workshop</option>
                                        <option value="On Road" @selected(str_starts_with(old('location'), 'On Road'))>On Road</option>
                                    </select>
                                    <input id="location_note" type="text" class="form-control"
                                        placeholder="e.g., Queens Rd near Navua"
                                        value="{{ old('location') && str_contains(old('location'), ':') ? trim(explode(':', old('location'), 2)[1]) : '' }}">
                                </div>
                                {{-- Hidden actual 'location' input that your controller expects --}}
                                <input type="hidden" name="location" id="location" value="{{ old('location') }}">
                                @error('location')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div class="col-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                                       value="{{ old('subject') }}" placeholder="Brief description of the issue" required>
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Provide detailed information about your request..." required>{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Please include as much detail as possible to help us resolve your request quickly.
                                </small>
                            </div>

                            <!-- Attachments -->
                            <div class="col-12">
                                <label class="form-label">Attachments (Optional)</label>
                                <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" 
                                       multiple accept="image/*,.pdf,.doc,.docx">
                                @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    You can attach multiple files (images, PDFs, documents). Max 10MB per file.
                                </small>
                            </div>

                            <!-- GPS Coordinates (Hidden) -->
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="location.href='{{ $backUrl }}'">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
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
(function(){
    const typeEl = document.getElementById('location_type');
    const noteEl = document.getElementById('location_note');
    const hiddenEl = document.getElementById('location');

    function syncLocation() {
        const t = typeEl?.value || '';
        const n = noteEl?.value?.trim() || '';
        hiddenEl.value = t ? (n ? `${t}: ${n}` : t) : (n || '');
    }

    typeEl?.addEventListener('change', syncLocation);
    noteEl?.addEventListener('input', syncLocation);

    // initialize on load
    syncLocation();
})();
</script>

<script>
// Auto-save form data
let formData = {};
const form = document.querySelector('form');
const inputs = form.querySelectorAll('input, select, textarea');

// Load saved data
const savedData = localStorage.getItem('serviceRequestDraft');
if (savedData) {
    formData = JSON.parse(savedData);
    inputs.forEach(input => {
        if (formData[input.name] && input.type !== 'file') {
            input.value = formData[input.name];
        }
    });
}

// Save data on change
inputs.forEach(input => {
    input.addEventListener('change', function() {
        if (input.type !== 'file') {
            formData[input.name] = input.value;
            localStorage.setItem('serviceRequestDraft', JSON.stringify(formData));
        }
    });
});

// Clear saved data on submit
form.addEventListener('submit', function() {
    localStorage.removeItem('serviceRequestDraft');
});
</script>
@endsection

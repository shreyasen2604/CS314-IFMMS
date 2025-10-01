@extends('layouts.app')

@section('title', 'Create New Route')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Route</h3>
                    <a href="{{ route('maintenance.routes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Routes
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenance.routes.store') }}" method="POST" id="routeForm">
                        @csrf
                        
                        <!-- Basic Route Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_name') is-invalid @enderror" 
                                           id="route_name" name="route_name" value="{{ old('route_name') }}" required>
                                    @error('route_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_code" class="form-label">Route Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_code') is-invalid @enderror" 
                                           id="route_code" name="route_code" value="{{ old('route_code') }}" required>
                                    @error('route_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_location" class="form-label">Start Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('start_location') is-invalid @enderror" 
                                           id="start_location" name="start_location" value="{{ old('start_location') }}" required>
                                    @error('start_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_location" class="form-label">End Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('end_location') is-invalid @enderror" 
                                           id="end_location" name="end_location" value="{{ old('end_location') }}" required>
                                    @error('end_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="route_type" class="form-label">Route Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('route_type') is-invalid @enderror" 
                                            id="route_type" name="route_type" required>
                                        <option value="">Select Type</option>
                                        <option value="delivery" {{ old('route_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                        <option value="pickup" {{ old('route_type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                        <option value="service" {{ old('route_type') == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="maintenance" {{ old('route_type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="emergency" {{ old('route_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                    </select>
                                    @error('route_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="total_distance" class="form-label">Total Distance (km) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" class="form-control @error('total_distance') is-invalid @enderror" 
                                           id="total_distance" name="total_distance" value="{{ old('total_distance') }}" required>
                                    @error('total_distance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration') }}" required>
                                    @error('estimated_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Schedule Information -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                           id="start_time" name="start_time" value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" name="end_time" value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fuel_cost_estimate" class="form-label">Fuel Cost Estimate</label>
                                    <input type="number" step="0.01" class="form-control @error('fuel_cost_estimate') is-invalid @enderror" 
                                           id="fuel_cost_estimate" name="fuel_cost_estimate" value="{{ old('fuel_cost_estimate') }}">
                                    @error('fuel_cost_estimate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="special_instructions" class="form-label">Special Instructions</label>
                            <textarea class="form-control @error('special_instructions') is-invalid @enderror" 
                                      id="special_instructions" name="special_instructions" rows="3">{{ old('special_instructions') }}</textarea>
                            @error('special_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Checkpoints Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Route Checkpoints <span class="text-danger">*</span></h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addCheckpoint">
                                    <i class="fas fa-plus"></i> Add Checkpoint
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="checkpointsContainer">
                                    <!-- Checkpoints will be added here dynamically -->
                                </div>
                                @error('checkpoints')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('maintenance.routes.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Route
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Checkpoint Template -->
<template id="checkpointTemplate">
    <div class="checkpoint-item border rounded p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Checkpoint <span class="checkpoint-number"></span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-checkpoint">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Checkpoint Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="checkpoints[][name]" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="checkpoints[][type]" required>
                        <option value="">Select Type</option>
                        <option value="pickup">Pickup</option>
                        <option value="delivery">Delivery</option>
                        <option value="rest_stop">Rest Stop</option>
                        <option value="fuel_station">Fuel Station</option>
                        <option value="inspection">Inspection</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Address <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="checkpoints[][address]" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Latitude <span class="text-danger">*</span></label>
                    <input type="number" step="any" class="form-control" name="checkpoints[][latitude]" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Longitude <span class="text-danger">*</span></label>
                    <input type="number" step="any" class="form-control" name="checkpoints[][longitude]" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="checkpoints[][duration]" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Contact Info</label>
                    <input type="text" class="form-control" name="checkpoints[][contact_info]">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Special Instructions</label>
                    <input type="text" class="form-control" name="checkpoints[][instructions]">
                </div>
            </div>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="checkpoints[][mandatory]" value="1" checked>
            <label class="form-check-label">Mandatory Checkpoint</label>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let checkpointCount = 0;

    // Add checkpoint
    $('#addCheckpoint').click(function() {
        addCheckpoint();
    });

    // Remove checkpoint
    $(document).on('click', '.remove-checkpoint', function() {
        $(this).closest('.checkpoint-item').remove();
        updateCheckpointNumbers();
    });

    function addCheckpoint() {
        checkpointCount++;
        const template = document.getElementById('checkpointTemplate');
        const clone = template.content.cloneNode(true);
        
        // Update checkpoint number
        clone.querySelector('.checkpoint-number').textContent = checkpointCount;
        
        // Update input names with proper indexing
        const inputs = clone.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace('[]', `[${checkpointCount - 1}]`);
            }
        });
        
        document.getElementById('checkpointsContainer').appendChild(clone);
    }

    function updateCheckpointNumbers() {
        $('.checkpoint-item').each(function(index) {
            $(this).find('.checkpoint-number').text(index + 1);
            
            // Update input names
            $(this).find('input, select').each(function() {
                if (this.name) {
                    this.name = this.name.replace(/\[\d+\]/, `[${index}]`);
                }
            });
        });
        checkpointCount = $('.checkpoint-item').length;
    }

    // Add initial checkpoint
    addCheckpoint();

    // Form validation
    $('#routeForm').submit(function(e) {
        if ($('.checkpoint-item').length === 0) {
            e.preventDefault();
            alert('Please add at least one checkpoint.');
            return false;
        }
    });
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Edit Route - ' . $route->route_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Route: {{ $route->route_name }}</h3>
                    <div>
                        <a href="{{ route('routes.show', $route) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye"></i> View Route
                        </a>
                        <a href="{{ route('maintenance.routes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Routes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('routes.update', $route) }}" method="POST" id="routeForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Route Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_name" class="form-label">Route Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_name') is-invalid @enderror" 
                                           id="route_name" name="route_name" value="{{ old('route_name', $route->route_name) }}" required>
                                    @error('route_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="route_code" class="form-label">Route Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('route_code') is-invalid @enderror" 
                                           id="route_code" name="route_code" value="{{ old('route_code', $route->route_code) }}" required>
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
                                           id="start_location" name="start_location" value="{{ old('start_location', $route->start_location) }}" required>
                                    @error('start_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_location" class="form-label">End Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('end_location') is-invalid @enderror" 
                                           id="end_location" name="end_location" value="{{ old('end_location', $route->end_location) }}" required>
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
                                        <option value="delivery" {{ old('route_type', $route->route_type) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                        <option value="pickup" {{ old('route_type', $route->route_type) == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                        <option value="service" {{ old('route_type', $route->route_type) == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="maintenance" {{ old('route_type', $route->route_type) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="emergency" {{ old('route_type', $route->route_type) == 'emergency' ? 'selected' : '' }}>Emergency</option>
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
                                        <option value="low" {{ old('priority', $route->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $route->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $route->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority', $route->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('status', $route->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $route->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="under_review" {{ old('status', $route->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_distance" class="form-label">Total Distance (km) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" class="form-control @error('total_distance') is-invalid @enderror" 
                                           id="total_distance" name="total_distance" value="{{ old('total_distance', $route->total_distance) }}" required>
                                    @error('total_distance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                           id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $route->estimated_duration) }}" required>
                                    @error('estimated_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $route->description) }}</textarea>
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
                                           id="start_time" name="start_time" value="{{ old('start_time', $route->start_time) }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                           id="end_time" name="end_time" value="{{ old('end_time', $route->end_time) }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fuel_cost_estimate" class="form-label">Fuel Cost Estimate</label>
                                    <input type="number" step="0.01" class="form-control @error('fuel_cost_estimate') is-invalid @enderror" 
                                           id="fuel_cost_estimate" name="fuel_cost_estimate" value="{{ old('fuel_cost_estimate', $route->fuel_cost_estimate) }}">
                                    @error('fuel_cost_estimate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="special_instructions" class="form-label">Special Instructions</label>
                            <textarea class="form-control @error('special_instructions') is-invalid @enderror" 
                                      id="special_instructions" name="special_instructions" rows="3">{{ old('special_instructions', $route->special_instructions) }}</textarea>
                            @error('special_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Checkpoints Display -->
                        @if($route->checkpoints->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Current Checkpoints ({{ $route->checkpoints->count() }})</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Address</th>
                                                <th>Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($route->checkpoints->sortBy('sequence_order') as $checkpoint)
                                            <tr>
                                                <td><span class="badge bg-primary">{{ $checkpoint->sequence_order }}</span></td>
                                                <td>{{ $checkpoint->checkpoint_name }}</td>
                                                <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $checkpoint->checkpoint_type)) }}</span></td>
                                                <td>{{ Str::limit($checkpoint->address, 50) }}</td>
                                                <td>{{ $checkpoint->estimated_duration }} min</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> To modify checkpoints, you'll need to create a new route or contact an administrator.
                                    Checkpoint modifications require careful consideration due to existing assignments.
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('routes.show', $route) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Route
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('#routeForm').submit(function(e) {
        // Add any custom validation here if needed
    });
});
</script>
@endpush
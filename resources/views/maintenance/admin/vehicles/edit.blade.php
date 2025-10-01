@extends('layouts.app')

@section('title', 'Edit Vehicle - IFMMS-ZAR')
@section('page-title', 'Edit Vehicle')

@section('content')
<div class="container-fluid p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-edit text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Edit Vehicle</h1>
                        <p class="text-gray-600 mt-1">Update vehicle information for {{ isset($vehicle) ? $vehicle->vehicle_number : 'Vehicle' }}</p>
                    </div>
                </div>
                <a href="{{ route('maintenance.vehicles.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('maintenance.vehicles.update', isset($vehicle) ? $vehicle->id : 1) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Vehicle Information -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Vehicle Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Number <span class="text-red-500">*</span></label>
                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number', isset($vehicle) ? $vehicle->vehicle_number : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('vehicle_number') border-red-500 @enderror" 
                               placeholder="e.g., TRK-001" required>
                        @error('vehicle_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">VIN <span class="text-red-500">*</span></label>
                        <input type="text" name="vin" value="{{ old('vin', isset($vehicle) ? $vehicle->vin : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('vin') border-red-500 @enderror" 
                               placeholder="Vehicle Identification Number" required>
                        @error('vin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Make <span class="text-red-500">*</span></label>
                        <select name="make" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('make') border-red-500 @enderror" required>
                            <option value="">Select Make</option>
                            @php
                                $makes = ['Ford', 'Toyota', 'Mercedes', 'Volvo', 'Chevrolet', 'Nissan', 'Honda', 'BMW'];
                                $currentMake = old('make', isset($vehicle) ? $vehicle->make : '');
                            @endphp
                            @foreach($makes as $make)
                                <option value="{{ $make }}" {{ $currentMake == $make ? 'selected' : '' }}>{{ $make }}</option>
                            @endforeach
                        </select>
                        @error('make')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model <span class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model', isset($vehicle) ? $vehicle->model : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('model') border-red-500 @enderror" 
                               placeholder="e.g., F-150" required>
                        @error('model')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
                        <input type="number" name="year" value="{{ old('year', isset($vehicle) ? $vehicle->year : '') }}" min="1990" max="{{ date('Y') + 1 }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('year') border-red-500 @enderror" 
                               placeholder="{{ date('Y') }}" required>
                        @error('year')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">License Plate <span class="text-red-500">*</span></label>
                        <input type="text" name="license_plate" value="{{ old('license_plate', isset($vehicle) ? $vehicle->license_plate : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('license_plate') border-red-500 @enderror" 
                               placeholder="e.g., ABC-1234" required>
                        @error('license_plate')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Technical Specifications -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Technical Specifications</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fuel Type <span class="text-red-500">*</span></label>
                        <select name="fuel_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('fuel_type') border-red-500 @enderror" required>
                            <option value="">Select Fuel Type</option>
                            @php
                                $fuelTypes = ['gasoline' => 'Gasoline', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid'];
                                $currentFuel = old('fuel_type', isset($vehicle) ? $vehicle->fuel_type : '');
                            @endphp
                            @foreach($fuelTypes as $value => $label)
                                <option value="{{ $value }}" {{ $currentFuel == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('fuel_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Mileage (km)</label>
                        <input type="number" name="mileage" value="{{ old('mileage', isset($vehicle) ? $vehicle->mileage : 0) }}" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="0">
                    </div>
                </div>
            </div>

            <!-- Assignment & Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Assignment & Status</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @php
                                $statuses = ['active' => 'Active', 'maintenance' => 'In Maintenance', 'reserved' => 'Reserved', 'retired' => 'Retired'];
                                $currentStatus = old('status', isset($vehicle) ? $vehicle->status : 'active');
                            @endphp
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $currentStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Driver</label>
                        <select name="driver_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Unassigned</option>
                            @if(isset($drivers))
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id', isset($vehicle) ? $vehicle->driver_id : '') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} ({{ $driver->email }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <!-- Maintenance Settings -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Maintenance Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Maintenance Date</label>
                        <input type="date" name="last_maintenance_date" value="{{ old('last_maintenance_date', isset($vehicle) && $vehicle->last_maintenance_date ? $vehicle->last_maintenance_date : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Maintenance Due</label>
                        <input type="date" name="next_maintenance_due" value="{{ old('next_maintenance_due', isset($vehicle) && $vehicle->next_maintenance_due ? $vehicle->next_maintenance_due : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Health Score (0-100)</label>
                        <input type="number" name="health_score" value="{{ old('health_score', isset($vehicle) ? $vehicle->health_score : 100) }}" min="0" max="100" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="100">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between">
                <button type="button" onclick="confirmDelete()" 
                        class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Delete Vehicle
                </button>
                
                <div class="flex space-x-3">
                    <a href="{{ route('maintenance.vehicles.index') }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md">
                        <i class="fas fa-save mr-2"></i>Update Vehicle
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="deleteForm" action="{{ route('maintenance.vehicles.index') }}/{{ isset($vehicle) ? $vehicle->id : 1 }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }
    
    // Auto-format inputs
    document.querySelector('input[name="license_plate"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
    
    document.querySelector('input[name="vin"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
    
    document.querySelector('input[name="vehicle_number"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Add New Vehicle - IFMMS-ZAR')
@section('page-title', 'Add New Vehicle')

@section('content')
<div class="container-fluid p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-plus-circle text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Add New Vehicle</h1>
                        <p class="text-gray-600 mt-1">Register a new vehicle to the fleet</p>
                    </div>
                </div>
                <a href="{{ route('maintenance.vehicles.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('maintenance.vehicles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Vehicle Information -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Vehicle Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Number <span class="text-red-500">*</span></label>
                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('vehicle_number') border-red-500 @enderror" 
                               placeholder="e.g., TRK-001" required>
                        @error('vehicle_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">VIN <span class="text-red-500">*</span></label>
                        <input type="text" name="vin" value="{{ old('vin') }}" 
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
                            <option value="Ford" {{ old('make') == 'Ford' ? 'selected' : '' }}>Ford</option>
                            <option value="Toyota" {{ old('make') == 'Toyota' ? 'selected' : '' }}>Toyota</option>
                            <option value="Mercedes" {{ old('make') == 'Mercedes' ? 'selected' : '' }}>Mercedes</option>
                            <option value="Volvo" {{ old('make') == 'Volvo' ? 'selected' : '' }}>Volvo</option>
                            <option value="Chevrolet" {{ old('make') == 'Chevrolet' ? 'selected' : '' }}>Chevrolet</option>
                            <option value="Nissan" {{ old('make') == 'Nissan' ? 'selected' : '' }}>Nissan</option>
                            <option value="Honda" {{ old('make') == 'Honda' ? 'selected' : '' }}>Honda</option>
                            <option value="BMW" {{ old('make') == 'BMW' ? 'selected' : '' }}>BMW</option>
                        </select>
                        @error('make')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model <span class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('model') border-red-500 @enderror" 
                               placeholder="e.g., F-150" required>
                        @error('model')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
                        <input type="number" name="year" value="{{ old('year') }}" min="1990" max="{{ date('Y') + 1 }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('year') border-red-500 @enderror" 
                               placeholder="{{ date('Y') }}" required>
                        @error('year')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">License Plate <span class="text-red-500">*</span></label>
                        <input type="text" name="license_plate" value="{{ old('license_plate') }}" 
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
                            <option value="gasoline" {{ old('fuel_type') == 'gasoline' ? 'selected' : '' }}>Gasoline</option>
                            <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                            <option value="hybrid" {{ old('fuel_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('fuel_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Engine Type</label>
                        <input type="text" name="engine_type" value="{{ old('engine_type') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="e.g., V6 3.5L">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transmission</label>
                        <select name="transmission" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Transmission</option>
                            <option value="manual" {{ old('transmission') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="automatic" {{ old('transmission') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                            <option value="cvt" {{ old('transmission') == 'cvt' ? 'selected' : '' }}>CVT</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="text" name="color" value="{{ old('color') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="e.g., White">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Mileage (km)</label>
                        <input type="number" name="mileage" value="{{ old('mileage', 0) }}" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seating Capacity</label>
                        <input type="number" name="seating_capacity" value="{{ old('seating_capacity') }}" min="1" max="100" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="e.g., 5">
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
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>In Maintenance</option>
                            <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Driver</label>
                        <select name="driver_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Unassigned</option>
                            @if(isset($drivers))
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} ({{ $driver->email }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Department</option>
                            <option value="Logistics" {{ old('department') == 'Logistics' ? 'selected' : '' }}>Logistics</option>
                            <option value="Delivery" {{ old('department') == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                            <option value="Maintenance" {{ old('department') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="Admin" {{ old('department') == 'Admin' ? 'selected' : '' }}>Administration</option>
                            <option value="Sales" {{ old('department') == 'Sales' ? 'selected' : '' }}>Sales</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" value="{{ old('location') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="e.g., Depot A">
                    </div>
                </div>
            </div>

            <!-- Purchase & Insurance Information -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Purchase & Insurance Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price</label>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" min="0" step="0.01" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Provider</label>
                        <input type="text" name="insurance_provider" value="{{ old('insurance_provider') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Insurance company name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Policy Number</label>
                        <input type="text" name="insurance_policy_number" value="{{ old('insurance_policy_number') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Policy number">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Expiry Date</label>
                        <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registration Expiry Date</label>
                        <input type="date" name="registration_expiry" value="{{ old('registration_expiry') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Maintenance Settings -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Maintenance Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Maintenance Date</label>
                        <input type="date" name="last_maintenance_date" value="{{ old('last_maintenance_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Maintenance Due</label>
                        <input type="date" name="next_maintenance_due" value="{{ old('next_maintenance_due') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Health Score (0-100)</label>
                        <input type="number" name="health_score" value="{{ old('health_score', 100) }}" min="0" max="100" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="100">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service Interval (km)</label>
                        <input type="number" name="service_interval_km" value="{{ old('service_interval_km', 5000) }}" min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="5000">
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b">Additional Information</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                              placeholder="Any additional information about the vehicle...">{{ old('notes') }}</textarea>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Image</label>
                    <input type="file" name="image" accept="image/*" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, GIF (Max: 2MB)</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('maintenance.vehicles.index') }}" 
                   class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="reset" 
                        class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Reset Form
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md">
                    <i class="fas fa-save mr-2"></i>Save Vehicle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    // Auto-format license plate
    document.querySelector('input[name="license_plate"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
    
    // Auto-format VIN
    document.querySelector('input[name="vin"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
    
    // Auto-format vehicle number
    document.querySelector('input[name="vehicle_number"]').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
    
    // Preview image before upload
    document.querySelector('input[name="image"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.size > 2097152) { // 2MB
            alert('File size must be less than 2MB');
            this.value = '';
        }
    });
</script>
@endpush
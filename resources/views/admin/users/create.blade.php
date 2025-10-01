@extends('layouts.app')

@section('title', 'Add User - IFMMS-ZAR')
@section('page-title', 'Add New User')

@section('content')
<div class="container-fluid p-6">
  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
          <div class="bg-blue-100 p-3 rounded-full">
            <i class="fas fa-user-plus text-blue-600 text-xl"></i>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Add New User</h1>
            <p class="text-gray-600">Create a new user account with role and vehicle assignment</p>
          </div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
          <i class="fas fa-arrow-left mr-2"></i>Back to Users
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

    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-8">
      @csrf
      
      <!-- Basic Information -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
          <div class="bg-green-100 p-2 rounded-full mr-3">
            <i class="fas fa-info-circle text-green-600"></i>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">Basic Information</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter full name">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter email address">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
            <select name="role" id="roleSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              @foreach ($roles as $r)
                <option value="{{ $r }}" @selected(old('role')===$r)>
                  @if($r === 'Admin') 👑 {{ $r }}
                  @elseif($r === 'Driver') 🚛 {{ $r }}
                  @elseif($r === 'Technician') 🔧 {{ $r }}
                  @else {{ $r }}
                  @endif
                </option>
              @endforeach
            </select>
          </div>

          <div id="vehicleSection" class="{{ old('role') === 'Driver' ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Vehicle</label>
            <select name="vehicle_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Select a vehicle (optional)</option>
              @foreach ($vehicles as $vehicle)
                <option value="{{ $vehicle->vehicle_number }}" @selected(old('vehicle_id') === $vehicle->vehicle_number)>
                  {{ $vehicle->vehicle_number }} - {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }})
                </option>
              @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Only available vehicles are shown</p>
          </div>
        </div>
      </div>

      <!-- Profile Picture -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
          <div class="bg-purple-100 p-2 rounded-full mr-3">
            <i class="fas fa-camera text-purple-600"></i>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">Profile Picture</h2>
          <span class="ml-3 text-sm text-gray-500">Optional</span>
        </div>

        <div class="flex items-center space-x-6">
          <div class="flex-shrink-0">
            <img id="profilePreview" class="h-20 w-20 rounded-full object-cover border-4 border-gray-200" src="https://ui-avatars.com/api/?name=User&background=6b7280&color=fff" alt="Profile preview">
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Profile Picture</label>
            <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">Supported formats: JPG, PNG, GIF (max 2MB)</p>
          </div>
        </div>
      </div>

      <!-- Security -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
          <div class="bg-red-100 p-2 rounded-full mr-3">
            <i class="fas fa-lock text-red-600"></i>
          </div>
          <h2 class="text-xl font-semibold text-gray-800">Security</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
            <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Enter password (min 8 characters)">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
            <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Confirm password">
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
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
              <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md">
              <i class="fas fa-user-plus mr-2"></i>Create User
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <script>
    // Show/hide vehicle section based on role
    document.getElementById('roleSelect').addEventListener('change', function() {
      const vehicleSection = document.getElementById('vehicleSection');
      if (this.value === 'Driver') {
        vehicleSection.classList.remove('hidden');
      } else {
        vehicleSection.classList.add('hidden');
        // Clear vehicle selection if not a driver
        document.querySelector('select[name="vehicle_id"]').value = '';
      }
    });

    // Profile picture preview
    document.getElementById('profilePictureInput').addEventListener('change', function(event) {
      const file = event.target.files[0];
      const preview = document.getElementById('profilePreview');
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });

    // Update preview based on name input
    document.querySelector('input[name="name"]').addEventListener('input', function() {
      const preview = document.getElementById('profilePreview');
      const fileInput = document.getElementById('profilePictureInput');
      
      // Only update if no file is selected
      if (!fileInput.files.length) {
        const name = this.value || 'User';
        const role = document.getElementById('roleSelect').value;
        let bgColor = '6b7280';
        
        if (role === 'Admin') bgColor = 'dc2626';
        else if (role === 'Driver') bgColor = '2563eb';
        else if (role === 'Technician') bgColor = '059669';
        
        preview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=${bgColor}&color=fff`;
      }
    });

    // Update preview based on role change
    document.getElementById('roleSelect').addEventListener('change', function() {
      const preview = document.getElementById('profilePreview');
      const fileInput = document.getElementById('profilePictureInput');
      const nameInput = document.querySelector('input[name="name"]');
      
      // Only update if no file is selected
      if (!fileInput.files.length) {
        const name = nameInput.value || 'User';
        const role = this.value;
        let bgColor = '6b7280';
        
        if (role === 'Admin') bgColor = 'dc2626';
        else if (role === 'Driver') bgColor = '2563eb';
        else if (role === 'Technician') bgColor = '059669';
        
        preview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=${bgColor}&color=fff`;
      }
    });
  </script>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

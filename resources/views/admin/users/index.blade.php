@extends('layouts.app')

@section('title', 'Users - IFMMS-ZAR')
@section('page-title', 'User Management')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <!-- Page Header -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-8">
              <div class="d-flex align-items-center">
                <div class="icon-circle bg-primary bg-opacity-10 me-3">
                  <i class="fas fa-users text-primary fs-4"></i>
                </div>
                <div>
                  <h1 class="h3 mb-1 fw-bold">User Management</h1>
                  <p class="text-muted mb-0">Manage system users, roles, and vehicle assignments</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
              <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
              </a>
              <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Add New User
              </a>
            </div>
          </div>
        </div>
      </div>

    <!-- Alert Messages -->
      @if (session('ok'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          <span>{{ session('ok') }}</span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      
      @if (session('err'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>
          <span>{{ session('err') }}</span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

    <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-2">Total Users</h6>
                  <h3 class="mb-0">{{ $users->total() ?? 0 }}</h3>
                </div>
                <div class="icon-shape bg-primary bg-opacity-10 rounded-circle p-3">
                  <i class="fas fa-users text-primary"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-2">Drivers</h6>
                  <h3 class="mb-0">{{ $users->where('role', 'Driver')->count() }}</h3>
                </div>
                <div class="icon-shape bg-info bg-opacity-10 rounded-circle p-3">
                  <i class="fas fa-truck text-info"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-2">Technicians</h6>
                  <h3 class="mb-0">{{ $users->where('role', 'Technician')->count() }}</h3>
                </div>
                <div class="icon-shape bg-success bg-opacity-10 rounded-circle p-3">
                  <i class="fas fa-wrench text-success"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="text-muted mb-2">Admins</h6>
                  <h3 class="mb-0">{{ $users->where('role', 'Admin')->count() }}</h3>
                </div>
                <div class="icon-shape bg-danger bg-opacity-10 rounded-circle p-3">
                  <i class="fas fa-crown text-danger"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
          <div class="row align-items-center">
            <div class="col-md-6">
              <h5 class="mb-0 fw-semibold">System Users</h5>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                  <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search users..." id="searchUsers">
              </div>
            </div>
          </div>
        </div>
        
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="px-4 py-3 text-uppercase small fw-semibold text-muted">User</th>
                  <th class="px-4 py-3 text-uppercase small fw-semibold text-muted">Contact</th>
                  <th class="px-4 py-3 text-uppercase small fw-semibold text-muted">Role</th>
                  <th class="px-4 py-3 text-uppercase small fw-semibold text-muted">Vehicle</th>
                  <th class="px-4 py-3 text-uppercase small fw-semibold text-muted">Joined</th>
                  <th class="px-4 py-3 text-uppercase small fw-semibold text-muted text-end">Actions</th>
                </tr>
              </thead>

          <tbody>
                @forelse ($users as $u)
                  <tr class="border-bottom user-row">
                    <td class="px-4 py-3">
                      <div class="d-flex align-items-center">
                        <div class="avatar-wrapper me-3">
                          <img class="rounded-circle" src="{{ $u->profile_picture_url }}" alt="{{ $u->name }}" width="45" height="45" style="object-fit: cover;">
                        </div>
                        <div>
                          <h6 class="mb-0 fw-semibold">{{ $u->name }}</h6>
                          <small class="text-muted">ID: #{{ str_pad($u->id, 4, '0', STR_PAD_LEFT) }}</small>
                        </div>
                      </div>
                    </td>
                    
                    <td class="px-4 py-3">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <span>{{ $u->email }}</span>
                      </div>
                    </td>
                    
                    <td class="px-4 py-3">
                      @if($u->role === 'Admin')
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                          <i class="fas fa-crown me-1"></i>Admin
                        </span>
                      @elseif($u->role === 'Driver')
                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                          <i class="fas fa-truck me-1"></i>Driver
                        </span>
                      @elseif($u->role === 'Technician')
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                          <i class="fas fa-wrench me-1"></i>Technician
                        </span>
                      @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                          {{ $u->role }}
                        </span>
                      @endif
                    </td>
                    
                    <td class="px-4 py-3">
                      @if($u->role === 'Driver')
                        @if($u->vehicle_id)
                          <div class="d-flex align-items-center">
                            <i class="fas fa-car text-primary me-2"></i>
                            <div>
                              <span class="fw-semibold">{{ $u->vehicle_id }}</span>
                              @if($u->vehicle)
                                <small class="d-block text-muted">{{ $u->vehicle->make }} {{ $u->vehicle->model }}</small>
                              @endif
                            </div>
                          </div>
                        @else
                          <span class="text-muted">
                            <i class="fas fa-minus me-1"></i>Not assigned
                          </span>
                        @endif
                      @else
                        <span class="text-muted">
                          <i class="fas fa-minus me-1"></i>N/A
                        </span>
                      @endif
                    </td>
                    
                    <td class="px-4 py-3">
                      <div>
                        <span class="d-block">{{ $u->created_at->format('M d, Y') }}</span>
                        <small class="text-muted">{{ $u->created_at->format('h:i A') }}</small>
                      </div>
                    </td>
                    
                    <td class="px-4 py-3 text-end">
                      <div class="btn-group" role="group">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit User">
                          <i class="fas fa-edit"></i>
                        </a>
                        @if(auth()->id() !== $u->id)
                          <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete User">
                              <i class="fas fa-trash"></i>
                            </button>
                          </form>
                        @else
                          <button class="btn btn-sm btn-outline-secondary" disabled data-bs-toggle="tooltip" title="Current User">
                            <i class="fas fa-lock"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-5">
                      <div class="empty-state">
                        <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="mb-2">No Users Found</h5>
                        <p class="text-muted mb-4">Get started by creating your first user account.</p>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                          <i class="fas fa-user-plus me-2"></i>Add First User
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      <!-- Pagination -->
        @if(isset($users) && method_exists($users, 'links'))
          <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} entries
              </div>
              <div>
                {{ $users->links('pagination::bootstrap-5') }}
              </div>
            </div>
          </div>
        @endif
      </div>
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

.icon-shape {
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-wrapper {
  position: relative;
  display: inline-block;
}

.avatar-wrapper img {
  border: 3px solid #f8f9fa;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-row {
  transition: all 0.3s ease;
}

.user-row:hover {
  background-color: #f8f9fa;
}

.empty-state {
  padding: 2rem;
}

.badge {
  font-weight: 500;
  font-size: 0.75rem;
}

.btn-group .btn {
  border-radius: 0.25rem;
  margin: 0 2px;
}

.table > :not(caption) > * > * {
  vertical-align: middle;
}

.card {
  border-radius: 0.5rem;
}

.card-header {
  border-bottom: 1px solid #e9ecef;
}

.form-control:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}
</style>

<script>
// Search functionality
document.getElementById('searchUsers').addEventListener('keyup', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const userRows = document.querySelectorAll('.user-row');
  
  userRows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endsection

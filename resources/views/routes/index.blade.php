@extends('layouts.app')

@section('title', 'Route Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Route Management</h3>
                    <a href="{{ route('maintenance.routes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Route
                    </a>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['total_routes'] }}</h4>
                                            <p class="mb-0">Total Routes</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-route fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['active_routes'] }}</h4>
                                            <p class="mb-0">Active Routes</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ number_format($stats['total_distance'], 0) }} km</h4>
                                            <p class="mb-0">Total Distance</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-road fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ number_format($stats['avg_duration'], 0) }} min</h4>
                                            <p class="mb-0">Avg Duration</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" action="{{ route('maintenance.routes.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search routes..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="route_type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="delivery" {{ request('route_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                        <option value="pickup" {{ request('route_type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                        <option value="service" {{ request('route_type') == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="maintenance" {{ request('route_type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="emergency" {{ request('route_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="priority" class="form-select">
                                        <option value="">All Priorities</option>
                                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('maintenance.routes.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                    <a href="{{ route('maintenance.routes-export') }}" class="btn btn-outline-success">
                                        <i class="fas fa-download"></i> Export
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Routes Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Route Code</th>
                                    <th>Route Name</th>
                                    <th>Type</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Distance</th>
                                    <th>Duration</th>
                                    <th>Checkpoints</th>
                                    <th>Assignments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $route)
                                <tr>
                                    <td>
                                        <strong>{{ $route->route_code }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $route->route_name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($route->start_location, 20) }} → 
                                                {{ Str::limit($route->end_location, 20) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($route->route_type) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = [
                                                'low' => 'success',
                                                'medium' => 'warning',
                                                'high' => 'danger',
                                                'urgent' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $priorityColors[$route->priority] ?? 'secondary' }}">
                                            {{ ucfirst($route->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'under_review' => 'warning'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$route->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $route->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($route->total_distance, 1) }} km</td>
                                    <td>{{ $route->estimated_duration }} min</td>
                                    <td>
                                        <span class="badge bg-info">{{ $route->checkpoints->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $route->assignments->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('maintenance.routes.show', $route) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('maintenance.routes.edit', $route) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit Route">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('maintenance.routes.optimize', $route) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-info" 
                                                        title="Optimize Route">
                                                    <i class="fas fa-route"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('maintenance.routes.destroy', $route) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this route?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        title="Delete Route">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-route fa-3x mb-3"></i>
                                            <h5>No routes found</h5>
                                            <p>Create your first route to get started.</p>
                                            <a href="{{ route('maintenance.routes.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Create Route
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($routes->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $routes->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('select[name="status"], select[name="route_type"], select[name="priority"]').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
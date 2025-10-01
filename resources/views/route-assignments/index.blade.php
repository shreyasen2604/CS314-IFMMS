@extends('layouts.app')

@section('title', 'Route Assignments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Route Assignments</h3>
                    <div>
                        <a href="{{ route('maintenance.route-assignments.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> New Assignment
                        </a>
                        <a href="{{ route('maintenance.route-assignments.calendar') }}" class="btn btn-info">
                            <i class="fas fa-calendar"></i> Calendar View
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['total_assignments'] }}</h4>
                                            <p class="mb-0">Total Assignments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clipboard-list fa-2x"></i>
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
                                            <h4>{{ $stats['today_assignments'] }}</h4>
                                            <p class="mb-0">Today's Assignments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-day fa-2x"></i>
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
                                            <h4>{{ $stats['completed_today'] }}</h4>
                                            <p class="mb-0">Completed Today</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
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
                                            <h4>{{ $stats['in_progress'] }}</h4>
                                            <p class="mb-0">In Progress</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-spinner fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" action="{{ route('maintenance.route-assignments.index') }}" class="row g-3">
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="driver_id" class="form-select">
                                        <option value="">All Drivers</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" 
                                           placeholder="From Date" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" 
                                           placeholder="To Date" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="route_type" class="form-select">
                                        <option value="">All Route Types</option>
                                        <option value="delivery" {{ request('route_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                        <option value="pickup" {{ request('route_type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                        <option value="service" {{ request('route_type') == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="maintenance" {{ request('route_type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="emergency" {{ request('route_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('maintenance.route-assignments.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Assignments Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Route</th>
                                    <th>Driver</th>
                                    <th>Vehicle</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->assignment_date->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assignment->assignment_date->format('l') }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->route->route_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assignment->route->route_code }}</small>
                                            <br>
                                            <span class="badge bg-secondary">{{ ucfirst($assignment->route->route_type) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($assignment->driver)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    @if($assignment->driver->profile_picture)
                                                        <img src="{{ asset('storage/' . $assignment->driver->profile_picture) }}" 
                                                             class="rounded-circle" width="30" height="30" alt="Avatar">
                                                    @else
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 30px; height: 30px;">
                                                            <span class="text-white fw-bold small">{{ substr($assignment->driver->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $assignment->driver->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $assignment->driver->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->vehicle)
                                            <div>
                                                <strong>{{ $assignment->vehicle->license_plate }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $assignment->vehicle->vehicle_number }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No vehicle</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->scheduled_start_time }} - {{ $assignment->scheduled_end_time }}</strong>
                                            @if($assignment->actual_start_time)
                                                <br>
                                                <small class="text-success">
                                                    Started: {{ $assignment->actual_start_time }}
                                                </small>
                                            @endif
                                            @if($assignment->actual_end_time)
                                                <br>
                                                <small class="text-success">
                                                    Ended: {{ $assignment->actual_end_time }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'assigned' => 'primary',
                                                'in_progress' => 'warning',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$assignment->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assignment->checkpointVisits->count() > 0)
                                            @php
                                                $totalCheckpoints = $assignment->checkpointVisits->count();
                                                $completedCheckpoints = $assignment->checkpointVisits->where('status', 'completed')->count();
                                                $progress = $totalCheckpoints > 0 ? ($completedCheckpoints / $totalCheckpoints) * 100 : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $progress }}%" 
                                                     aria-valuenow="{{ $progress }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ number_format($progress, 0) }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $completedCheckpoints }}/{{ $totalCheckpoints }} checkpoints</small>
                                        @else
                                            <span class="text-muted">No checkpoints</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('maintenance.route-assignments.show', $assignment) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($assignment->status !== 'completed')
                                                <a href="{{ route('maintenance.route-assignments.edit', $assignment) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit Assignment">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($assignment->status === 'assigned')
                                                <button type="button" class="btn btn-sm btn-outline-success start-assignment" 
                                                        data-id="{{ $assignment->id }}" title="Start Assignment">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                            @if($assignment->status === 'in_progress')
                                                <a href="{{ route('maintenance.route-assignments.tracking', $assignment) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Track Progress">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </a>
                                            @endif
                                            @if($assignment->status !== 'completed' && $assignment->status !== 'in_progress')
                                                <form action="{{ route('maintenance.route-assignments.destroy', $assignment) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Delete Assignment">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <h5>No assignments found</h5>
                                            <p>Create your first route assignment to get started.</p>
                                            <a href="{{ route('maintenance.route-assignments.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Create Assignment
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($assignments->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $assignments->appends(request()->query())->links() }}
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
    $('select[name="status"], select[name="driver_id"], select[name="route_type"]').change(function() {
        $(this).closest('form').submit();
    });

    // Start assignment
    $('.start-assignment').click(function() {
        const assignmentId = $(this).data('id');
        
        if (confirm('Are you sure you want to start this assignment?')) {
            $.post(`/maintenance/route-assignments/${assignmentId}/start`, {
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                location.reload();
            })
            .fail(function(xhr) {
                alert('Error: ' + xhr.responseJSON.error);
            });
        }
    });
});
</script>
@endpush
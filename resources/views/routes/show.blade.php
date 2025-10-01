@extends('layouts.app')

@section('title', 'Route Details - ' . $route->route_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Route Details: {{ $route->route_name }}</h3>
                    <div>
                        <a href="{{ route('routes.edit', $route) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit Route
                        </a>
                        <a href="{{ route('maintenance.routes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Routes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Route Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Route Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Route Code:</strong> {{ $route->route_code }}</p>
                                            <p><strong>Route Name:</strong> {{ $route->route_name }}</p>
                                            <p><strong>Type:</strong> 
                                                <span class="badge bg-secondary">{{ ucfirst($route->route_type) }}</span>
                                            </p>
                                            <p><strong>Priority:</strong> 
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
                                            </p>
                                            <p><strong>Status:</strong> 
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
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Distance:</strong> {{ number_format($route->total_distance, 1) }} km</p>
                                            <p><strong>Duration:</strong> {{ $route->estimated_duration }} minutes</p>
                                            <p><strong>Start Location:</strong> {{ $route->start_location }}</p>
                                            <p><strong>End Location:</strong> {{ $route->end_location }}</p>
                                            <p><strong>Created By:</strong> {{ $route->creator->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @if($route->description)
                                    <div class="row">
                                        <div class="col-12">
                                            <p><strong>Description:</strong></p>
                                            <p class="text-muted">{{ $route->description }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($route->special_instructions)
                                    <div class="row">
                                        <div class="col-12">
                                            <p><strong>Special Instructions:</strong></p>
                                            <p class="text-muted">{{ $route->special_instructions }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Route Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Assignments:</span>
                                            <strong>{{ $stats['total_assignments'] }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Completed:</span>
                                            <strong>{{ $stats['completed_assignments'] }}</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Checkpoints:</span>
                                            <strong>{{ $stats['total_checkpoints'] }}</strong>
                                        </div>
                                    </div>
                                    @if($stats['average_completion_time'])
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Avg Completion:</span>
                                            <strong>{{ number_format($stats['average_completion_time'], 0) }} min</strong>
                                        </div>
                                    </div>
                                    @endif
                                    @if($stats['efficiency_rating'])
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Efficiency Rating:</span>
                                            <strong>{{ number_format($stats['efficiency_rating'], 1) }}%</strong>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checkpoints -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Route Checkpoints ({{ $route->checkpoints->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($route->checkpoints->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Address</th>
                                            <th>Duration</th>
                                            <th>Mandatory</th>
                                            <th>Contact</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($route->checkpoints->sortBy('sequence_order') as $checkpoint)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $checkpoint->sequence_order }}</span>
                                            </td>
                                            <td>{{ $checkpoint->checkpoint_name }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $checkpoint->checkpoint_type)) }}</span>
                                            </td>
                                            <td>{{ $checkpoint->address }}</td>
                                            <td>{{ $checkpoint->estimated_duration }} min</td>
                                            <td>
                                                @if($checkpoint->is_mandatory)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>{{ $checkpoint->contact_info ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No checkpoints defined</h5>
                                <p class="text-muted">Add checkpoints to this route to define stops along the way.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Assignments -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Assignments ({{ $recentAssignments->count() }})</h5>
                            <a href="{{ route('route-assignments.create') }}?route_id={{ $route->id }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> New Assignment
                            </a>
                        </div>
                        <div class="card-body">
                            @if($recentAssignments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Driver</th>
                                            <th>Vehicle</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentAssignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->assignment_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($assignment->driver)
                                                    {{ $assignment->driver->name }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($assignment->vehicle)
                                                    {{ $assignment->vehicle->license_plate }}
                                                @else
                                                    <span class="text-muted">No vehicle</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'in_progress' => 'info',
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
                                                        $totalCheckpoints = $route->checkpoints->count();
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
                                                @else
                                                    <span class="text-muted">Not started</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('route-assignments.show', $assignment) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No assignments yet</h5>
                                <p class="text-muted">This route hasn't been assigned to any drivers yet.</p>
                                <a href="{{ route('route-assignments.create') }}?route_id={{ $route->id }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Assignment
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
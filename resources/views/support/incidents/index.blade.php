@extends('layouts.app')

@section('title', 'Incident Reports - IFMMS')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-danger bg-opacity-10 me-3">
                                    <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                                </div>
                                <div>
                                    <h1 class="h3 mb-1 fw-bold">Incident Reports</h1>
                                    <p class="text-muted mb-0">Track and manage fleet incidents</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="{{ route('support.incidents.create') }}" class="btn btn-danger">
                                <i class="fas fa-plus me-2"></i>Report Incident
                            </a>
                            <a href="{{ route('support.incidents.export') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-download me-2"></i>Export
                            </a>
                            <a href="{{ route('support.incident-statistics') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-chart-bar me-2"></i>Statistics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Incidents</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="icon-shape bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clipboard-list text-primary"></i>
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
                            <h6 class="text-muted mb-2">Active</h6>
                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-warning"></i>
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
                            <h6 class="text-muted mb-2">Critical</h6>
                            <h3 class="mb-0">{{ $stats['critical'] }}</h3>
                        </div>
                        <div class="icon-shape bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-fire text-danger"></i>
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
                            <h6 class="text-muted mb-2">This Month</h6>
                            <h3 class="mb-0">{{ $stats['this_month'] }}</h3>
                        </div>
                        <div class="icon-shape bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('support.incidents.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all">All Status</option>
                        <option value="reported" {{ request('status') == 'reported' ? 'selected' : '' }}>Reported</option>
                        <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="all">All Types</option>
                        <option value="accident" {{ request('type') == 'accident' ? 'selected' : '' }}>Accident</option>
                        <option value="breakdown" {{ request('type') == 'breakdown' ? 'selected' : '' }}>Breakdown</option>
                        <option value="theft" {{ request('type') == 'theft' ? 'selected' : '' }}>Theft</option>
                        <option value="vandalism" {{ request('type') == 'vandalism' ? 'selected' : '' }}>Vandalism</option>
                        <option value="traffic_violation" {{ request('type') == 'traffic_violation' ? 'selected' : '' }}>Traffic Violation</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Severity</label>
                    <select name="severity" class="form-select">
                        <option value="all">All Severities</option>
                        <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                        <option value="moderate" {{ request('severity') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                        <option value="major" {{ request('severity') == 'major' ? 'selected' : '' }}>Major</option>
                        <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Incident #</th>
                            <th class="px-4 py-3">Date/Time</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Vehicle</th>
                            <th class="px-4 py-3">Driver</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Severity</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $incident)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-semibold">{{ $incident->incident_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $incident->incident_date->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $incident->incident_date->format('h:i A') }}</small>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $incident->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($incident->vehicle)
                                    <div>{{ $incident->vehicle->registration_number }}</div>
                                    <small class="text-muted">{{ $incident->vehicle->make }} {{ $incident->vehicle->model }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($incident->driver)
                                    {{ $incident->driver->name }}
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-truncate" style="max-width: 150px;" title="{{ $incident->location }}">
                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                    {{ $incident->location }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $incident->severity_color }} bg-opacity-10 text-{{ $incident->severity_color }}">
                                    {{ ucfirst($incident->severity) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $incident->status_color }} bg-opacity-10 text-{{ $incident->status_color }}">
                                    {{ ucfirst($incident->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('support.incidents.show', $incident) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-check text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="mb-2">No Incidents Found</h5>
                                    <p class="text-muted mb-4">No incidents match your current filters.</p>
                                    <a href="{{ route('support.incidents.create') }}" class="btn btn-danger">
                                        <i class="fas fa-plus me-2"></i>Report New Incident
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($incidents->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $incidents->firstItem() }} to {{ $incidents->lastItem() }} 
                    of {{ $incidents->total() }} entries
                </div>
                <div>
                    {{ $incidents->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
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
</style>
@endsection
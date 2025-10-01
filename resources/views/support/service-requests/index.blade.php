@extends('layouts.app')

@section('title', 'Service Requests - IFMMS')

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
                                <div class="icon-circle bg-primary bg-opacity-10 me-3">
                                    <i class="fas fa-headset text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h1 class="h3 mb-1 fw-bold">Service Requests</h1>
                                    <p class="text-muted mb-0">Manage and track service support tickets</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="{{ route('support.service-requests.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>New Request
                            </a>
                            <a href="{{ route('support.statistics') }}" class="btn btn-outline-secondary">
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
                            <h6 class="text-muted mb-2">Total Requests</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="icon-shape bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-ticket-alt text-info"></i>
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
                            <h6 class="text-muted mb-2">Open</h6>
                            <h3 class="mb-0">{{ $stats['open'] }}</h3>
                        </div>
                        <div class="icon-shape bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-exclamation-circle text-danger"></i>
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
                            <h6 class="text-muted mb-2">In Progress</h6>
                            <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-spinner text-warning"></i>
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
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('support.service-requests.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="all">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="all">All Categories</option>
                        <option value="breakdown" {{ request('category') == 'breakdown' ? 'selected' : '' }}>Breakdown</option>
                        <option value="maintenance" {{ request('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="inspection" {{ request('category') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Ticket #, subject..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Service Requests Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Ticket #</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Vehicle</th>
                            <th class="px-4 py-3">Requester</th>
                            <th class="px-4 py-3">Priority</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Assigned To</th>
                            <th class="px-4 py-3">Created</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRequests as $request)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-semibold">{{ $request->ticket_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <span class="fw-semibold">{{ Str::limit($request->subject, 30) }}</span>
                                    <small class="d-block text-muted">{{ $request->category }}</small>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($request->vehicle)
                                    <span>{{ $request->vehicle->registration_number }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $request->requester->profile_picture_url ?? asset('images/default-avatar.png') }}" 
                                         class="rounded-circle me-2" width="32" height="32">
                                    <span>{{ $request->requester->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $request->priority_color }} bg-opacity-10 text-{{ $request->priority_color }}">
                                    {{ ucfirst($request->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $request->status_color }} bg-opacity-10 text-{{ $request->status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($request->assignee)
                                    <span>{{ $request->assignee->name }}</span>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <small>{{ $request->created_at->format('M d, Y') }}</small>
                                <small class="d-block text-muted">{{ $request->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('support.service-requests.show', $request) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="mb-2">No Service Requests Found</h5>
                                    <p class="text-muted mb-4">Create your first service request to get started.</p>
                                    <a href="{{ route('support.service-requests.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Create Request
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($serviceRequests->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $serviceRequests->firstItem() }} to {{ $serviceRequests->lastItem() }} 
                    of {{ $serviceRequests->total() }} entries
                </div>
                <div>
                    {{ $serviceRequests->links('pagination::bootstrap-5') }}
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
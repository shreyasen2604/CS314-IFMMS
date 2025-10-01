@extends('layouts.app')

@section('title', 'Work Orders - IFMMS-ZAR')

@section('content')
<div class="work-orders-container">
    <!-- Page Header with Stats -->
    <div class="page-header-enhanced">
        <div class="header-content">
            <div class="header-title-section">
                <h1 class="page-title-large">
                    <i class="fas fa-tools text-primary me-3"></i>
                    Work Order Management
                </h1>
                <p class="page-subtitle-enhanced">Manage your assigned tasks and maintenance work orders</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline-primary" onclick="refreshWorkOrders()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter me-2"></i>Advanced Filter
                </button>
            </div>
        </div>
        
        <!-- Quick Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $mine->total() ?? 0 }}</div>
                    <div class="stat-label">Active Tasks</div>
                </div>
            </div>
            
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $unassigned->total() ?? 0 }}</div>
                    <div class="stat-label">Available Tasks</div>
                </div>
            </div>
            
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $completedToday ?? 0 }}</div>
                    <div class="stat-label">Completed Today</div>
                </div>
            </div>
            
            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $urgentCount ?? 0 }}</div>
                    <div class="stat-label">Urgent Tasks</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Filter Tabs -->
    <div class="filter-tabs-container">
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterWorkOrders('all')">
                All Work Orders
                <span class="tab-badge">{{ ($mine->total() ?? 0) + ($unassigned->total() ?? 0) }}</span>
            </button>
            <button class="filter-tab" onclick="filterWorkOrders('assigned')">
                My Assignments
                <span class="tab-badge badge-primary">{{ $mine->total() ?? 0 }}</span>
            </button>
            <button class="filter-tab" onclick="filterWorkOrders('available')">
                Available
                <span class="tab-badge badge-warning">{{ $unassigned->total() ?? 0 }}</span>
            </button>
            <button class="filter-tab" onclick="filterWorkOrders('urgent')">
                Urgent
                <span class="tab-badge badge-danger">{{ $urgentCount ?? 0 }}</span>
            </button>
        </div>
        
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="form-control search-input" placeholder="Search work orders..." id="quickSearch">
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="work-orders-grid">
        <!-- Available Work Orders -->
        <div class="work-section" id="availableSection">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-inbox me-2"></i>Available Work Orders
                </h2>
                <span class="section-count">{{ $unassigned->count() }} items</span>
            </div>
            
            <div class="work-cards-container">
                @forelse ($unassigned as $work)
                <div class="work-card" data-priority="{{ strtolower($work->severity ?? 'low') }}">
                    <div class="work-card-header">
                        <div class="work-id">#WO-{{ str_pad($work->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <span class="priority-badge priority-{{ strtolower($work->severity ?? 'low') }}">
                            {{ ucfirst($work->severity ?? 'Normal') }}
                        </span>
                    </div>
                    
                    <div class="work-card-body">
                        <h3 class="work-title">{{ $work->title }}</h3>
                        <p class="work-description">{{ Str::limit($work->description ?? 'No description available', 100) }}</p>
                        
                        <div class="work-meta">
                            <div class="meta-item">
                                <i class="fas fa-truck"></i>
                                <span>{{ $work->vehicle_id ?? 'N/A' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $work->reporter?->name ?? 'System' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $work->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        <div class="work-tags">
                            @if($work->type)
                            <span class="work-tag">{{ $work->type }}</span>
                            @endif
                            @if($work->category)
                            <span class="work-tag">{{ $work->category }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="work-card-footer">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewWorkOrder({{ $work->id }})">
                            <i class="fas fa-eye me-1"></i>View Details
                        </button>
                        <form method="POST" action="{{ route('technician.incidents.claim', $work) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-hand-paper me-1"></i>Claim Task
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4>No Available Work Orders</h4>
                    <p class="text-muted">All work orders have been assigned. Check back later.</p>
                </div>
                @endforelse
            </div>
            
            @if($unassigned->hasPages())
            <div class="pagination-container">
                {{ $unassigned->links() }}
            </div>
            @endif
        </div>

        <!-- My Assigned Work Orders -->
        <div class="work-section" id="assignedSection">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tasks me-2"></i>My Active Work Orders
                </h2>
                <span class="section-count">{{ $mine->count() }} items</span>
            </div>
            
            <div class="work-cards-container">
                @forelse ($mine as $work)
                <div class="work-card assigned" data-status="{{ strtolower($work->status) }}">
                    <div class="work-card-header">
                        <div class="work-id">#WO-{{ str_pad($work->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="header-badges">
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $work->status)) }}">
                                {{ $work->status }}
                            </span>
                            <span class="priority-badge priority-{{ strtolower($work->severity ?? 'low') }}">
                                {{ ucfirst($work->severity ?? 'Normal') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="work-card-body">
                        <h3 class="work-title">{{ $work->title }}</h3>
                        <p class="work-description">{{ Str::limit($work->description ?? 'No description available', 100) }}</p>
                        
                        <!-- Progress Bar -->
                        @php
                            $progress = match(strtolower($work->status)) {
                                'pending' => 10,
                                'in progress' => 50,
                                'testing' => 75,
                                'resolved' => 100,
                                default => 0
                            };
                        @endphp
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="progress-label">{{ $progress }}% Complete</span>
                        </div>
                        
                        <div class="work-meta">
                            <div class="meta-item">
                                <i class="fas fa-truck"></i>
                                <span>{{ $work->vehicle_id ?? 'N/A' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-stopwatch"></i>
                                <span>{{ $work->updated_at->diffForHumans() }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $work->reporter?->name ?? 'System' }}</span>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="quick-actions">
                            @if($work->status !== 'Resolved')
                            <button class="action-btn action-update" onclick="updateStatus({{ $work->id }})">
                                <i class="fas fa-edit"></i>
                                Update Status
                            </button>
                            @endif
                            <button class="action-btn action-log" onclick="addWorkLog({{ $work->id }})">
                                <i class="fas fa-clipboard-check"></i>
                                Add Log
                            </button>
                            <button class="action-btn action-parts" onclick="requestParts({{ $work->id }})">
                                <i class="fas fa-cog"></i>
                                Request Parts
                            </button>
                        </div>
                    </div>
                    
                    <div class="work-card-footer">
                        <a href="{{ route('technician.incidents.show', $work) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-wrench me-1"></i>Work on Task
                        </a>
                        @if($work->status === 'Resolved')
                        <button class="btn btn-sm btn-success" onclick="closeWorkOrder({{ $work->id }})">
                            <i class="fas fa-check me-1"></i>Close Order
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                    <h4>No Active Work Orders</h4>
                    <p class="text-muted">You don't have any assigned work orders. Check available tasks to claim new work.</p>
                    <button class="btn btn-primary mt-3" onclick="scrollToAvailable()">
                        <i class="fas fa-arrow-up me-2"></i>View Available Tasks
                    </button>
                </div>
                @endforelse
            </div>
            
            @if($mine->hasPages())
            <div class="pagination-container">
                {{ $mine->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Advanced Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-filter me-2"></i>Advanced Filters
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('technician.incidents.index') }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Search Keyword</label>
                            <input type="text" name="q" class="form-control" value="{{ request('q') }}" 
                                   placeholder="Search title, description, vehicle...">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach ($statuses ?? ['Pending', 'In Progress', 'Resolved'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ $status }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Priority Level</label>
                            <select name="severity" class="form-select">
                                <option value="">All Priorities</option>
                                @foreach ($severities ?? ['Low', 'Medium', 'High', 'Critical'] as $severity)
                                <option value="{{ $severity }}" @selected(request('severity') === $severity)>
                                    {{ $severity }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Vehicle ID</label>
                            <input type="text" name="vehicle" class="form-control" 
                                   value="{{ request('vehicle') }}" placeholder="e.g., TRK-001">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Date From</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Date To</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Reporter</label>
                            <select name="reporter" class="form-select">
                                <option value="">All Reporters</option>
                                @foreach ($reporters ?? [] as $reporter)
                                <option value="{{ $reporter->id }}" @selected(request('reporter') == $reporter->id)>
                                    {{ $reporter->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Items Per Page</label>
                            <select name="per_page" class="form-select">
                                @foreach ([10, 25, 50, 100] as $n)
                                <option value="{{ $n }}" @selected((int)request('per_page', 10) === $n)>
                                    {{ $n }} items
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('technician.incidents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Reset Filters
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Work Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select class="form-select" id="newStatus">
                            <option value="In Progress">In Progress</option>
                            <option value="Testing">Testing</option>
                            <option value="Resolved">Resolved</option>
                            <option value="On Hold">On Hold</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" rows="3" id="statusNotes" 
                                  placeholder="Add any relevant notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStatusUpdate()">
                    <i class="fas fa-save me-2"></i>Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Work Orders Styles */
.work-orders-container {
    padding: 1.5rem;
    background: #f8f9fa;
    min-height: calc(100vh - var(--navbar-height));
}

.page-header-enhanced {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-title-large {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
}

.page-subtitle-enhanced {
    color: #6b7280;
    margin-top: 0.5rem;
    font-size: 1.1rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.stat-card.stat-primary {
    border-color: #4ade80;
    background: linear-gradient(135deg, rgba(74, 222, 128, 0.05) 0%, rgba(74, 222, 128, 0.1) 100%);
}

.stat-card.stat-warning {
    border-color: #fbbf24;
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.05) 0%, rgba(251, 191, 36, 0.1) 100%);
}

.stat-card.stat-success {
    border-color: #22c55e;
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(34, 197, 94, 0.1) 100%);
}

.stat-card.stat-danger {
    border-color: #ef4444;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(239, 68, 68, 0.1) 100%);
}

.stat-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    margin-right: 1.5rem;
    font-size: 1.5rem;
}

.stat-primary .stat-icon {
    background: #4ade80;
    color: white;
}

.stat-warning .stat-icon {
    background: #fbbf24;
    color: white;
}

.stat-success .stat-icon {
    background: #22c55e;
    color: white;
}

.stat-danger .stat-icon {
    background: #ef4444;
    color: white;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Filter Tabs */
.filter-tabs-container {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
}

.filter-tab {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-tab:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.filter-tab.active {
    background: var(--primary-gradient);
    color: white;
    border-color: transparent;
}

.tab-badge {
    padding: 0.25rem 0.5rem;
    background: #f3f4f6;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.filter-tab.active .tab-badge {
    background: rgba(255,255,255,0.3);
}

.search-box {
    position: relative;
    width: 300px;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.search-input {
    padding-left: 2.5rem;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

.search-input:focus {
    border-color: #4ade80;
    box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

/* Work Orders Grid */
.work-orders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
    gap: 2rem;
}

.work-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
}

.section-count {
    padding: 0.25rem 0.75rem;
    background: #f3f4f6;
    border-radius: 20px;
    font-size: 0.875rem;
    color: #6b7280;
}

/* Work Cards */
.work-cards-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 600px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.work-cards-container::-webkit-scrollbar {
    width: 6px;
}

.work-cards-container::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.work-cards-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.work-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.3s ease;
}

.work-card:hover {
    border-color: #4ade80;
    box-shadow: 0 4px 12px rgba(74, 222, 128, 0.15);
    transform: translateY(-2px);
}

.work-card.assigned {
    border-left: 4px solid #4ade80;
}

.work-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.work-id {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.875rem;
}

.header-badges {
    display: flex;
    gap: 0.5rem;
}

.priority-badge, .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-low {
    background: #dbeafe;
    color: #1e40af;
}

.priority-medium {
    background: #fed7aa;
    color: #c2410c;
}

.priority-high {
    background: #fecaca;
    color: #dc2626;
}

.priority-critical {
    background: #dc2626;
    color: white;
}

.status-pending {
    background: #f3f4f6;
    color: #6b7280;
}

.status-in-progress {
    background: #dbeafe;
    color: #1e40af;
}

.status-resolved {
    background: #d1fae5;
    color: #065f46;
}

.work-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.work-description {
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.work-meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.meta-item i {
    color: #9ca3af;
}

.work-tags {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.work-tag {
    padding: 0.25rem 0.75rem;
    background: #f3f4f6;
    border-radius: 6px;
    font-size: 0.75rem;
    color: #4b5563;
}

/* Progress Container */
.progress-container {
    margin: 1rem 0;
}

.progress {
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-bar {
    height: 100%;
    background: var(--primary-gradient);
    transition: width 0.3s ease;
}

.progress-label {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    gap: 0.5rem;
    margin: 1rem 0;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #4b5563;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

.action-update {
    border-color: #60a5fa;
    color: #2563eb;
}

.action-log {
    border-color: #a78bfa;
    color: #7c3aed;
}

.action-parts {
    border-color: #fbbf24;
    color: #d97706;
}

.work-card-footer {
    display: flex;
    gap: 0.75rem;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}

.empty-state h4 {
    color: #6b7280;
    margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .work-orders-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-tabs-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .filter-tabs {
        width: 100%;
        overflow-x: auto;
    }
    
    .search-box {
        width: 100%;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .header-actions {
        width: 100%;
    }
    
    .header-actions .btn {
        flex: 1;
    }
}
</style>

@endsection

@push('scripts')
<script>
// Quick Search Functionality
document.getElementById('quickSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const workCards = document.querySelectorAll('.work-card');
    
    workCards.forEach(card => {
        const title = card.querySelector('.work-title').textContent.toLowerCase();
        const description = card.querySelector('.work-description').textContent.toLowerCase();
        const id = card.querySelector('.work-id').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm) || id.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Filter Work Orders
function filterWorkOrders(type) {
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Show/hide sections based on filter
    const availableSection = document.getElementById('availableSection');
    const assignedSection = document.getElementById('assignedSection');
    
    switch(type) {
        case 'all':
            availableSection.style.display = 'block';
            assignedSection.style.display = 'block';
            break;
        case 'assigned':
            availableSection.style.display = 'none';
            assignedSection.style.display = 'block';
            break;
        case 'available':
            availableSection.style.display = 'block';
            assignedSection.style.display = 'none';
            break;
        case 'urgent':
            // Filter to show only urgent items
            document.querySelectorAll('.work-card').forEach(card => {
                const priority = card.dataset.priority;
                if (priority === 'high' || priority === 'critical') {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            break;
    }
}

// View Work Order Details
function viewWorkOrder(id) {
    // In a real application, this would open a modal or navigate to details page
    console.log('Viewing work order:', id);
}

// Update Status
function updateStatus(id) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
    // Store the work order ID for later use
    document.getElementById('statusModal').dataset.workOrderId = id;
}

// Save Status Update
function saveStatusUpdate() {
    const workOrderId = document.getElementById('statusModal').dataset.workOrderId;
    const newStatus = document.getElementById('newStatus').value;
    const notes = document.getElementById('statusNotes').value;
    
    // In a real application, this would make an AJAX call to update the status
    console.log('Updating work order', workOrderId, 'to status:', newStatus, 'with notes:', notes);
    
    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
    
    // Show success message
    showNotification('Status updated successfully', 'success');
}

// Add Work Log
function addWorkLog(id) {
    // In a real application, this would open a modal to add work log
    console.log('Adding work log for:', id);
}

// Request Parts
function requestParts(id) {
    // In a real application, this would open a parts request form
    console.log('Requesting parts for:', id);
}

// Close Work Order
function closeWorkOrder(id) {
    if (confirm('Are you sure you want to close this work order?')) {
        // In a real application, this would make an AJAX call
        console.log('Closing work order:', id);
        showNotification('Work order closed successfully', 'success');
    }
}

// Scroll to Available Tasks
function scrollToAvailable() {
    document.getElementById('availableSection').scrollIntoView({ behavior: 'smooth' });
}

// Refresh Work Orders
function refreshWorkOrders() {
    location.reload();
}

// Show Notification
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endpush
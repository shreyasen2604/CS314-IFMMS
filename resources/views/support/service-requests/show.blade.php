@extends('layouts.app')

@section('title', 'Service Request #' . $serviceRequest->ticket_number . ' - IFMMS')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Header -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h3 mb-2 fw-bold">{{ $serviceRequest->subject }}</h1>
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted">Ticket #{{ $serviceRequest->ticket_number }}</span>
                                <span class="badge bg-{{ $serviceRequest->priority_color }} bg-opacity-10 text-{{ $serviceRequest->priority_color }}">
                                    {{ ucfirst($serviceRequest->priority) }} Priority
                                </span>
                                <span class="badge bg-{{ $serviceRequest->status_color }} bg-opacity-10 text-{{ $serviceRequest->status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('support.service-requests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Request Details -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Request Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p>{{ $serviceRequest->description }}</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Category</h6>
                            <p class="mb-0">
                                <i class="fas fa-tag me-2"></i>{{ ucfirst($serviceRequest->category) }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Location</h6>
                            <p class="mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ $serviceRequest->location ?: 'Not specified' }}
                            </p>
                        </div>
                        @if($serviceRequest->vehicle)
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Vehicle</h6>
                            <p class="mb-0">
                                <i class="fas fa-truck me-2"></i>
                                {{ $serviceRequest->vehicle->registration_number }} - 
                                {{ $serviceRequest->vehicle->make }} {{ $serviceRequest->vehicle->model }}
                            </p>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Created</h6>
                            <p class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                {{ $serviceRequest->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Comments & Updates</h5>
                </div>
                <div class="card-body">
                    <!-- Comment Form -->
                    <form action="{{ route('support.service-requests.comment', $serviceRequest) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea name="comment" rows="3" class="form-control" placeholder="Add a comment..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <input type="file" name="attachments[]" class="form-control form-control-sm d-inline-block w-auto" multiple>
                                @if(auth()->user()->role !== 'Driver')
                                <div class="form-check form-check-inline ms-3">
                                    <input type="checkbox" name="is_internal" class="form-check-input" id="internalComment">
                                    <label class="form-check-label" for="internalComment">Internal Note</label>
                                </div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-comment me-1"></i>Post Comment
                            </button>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div class="comments-timeline">
                        @forelse($serviceRequest->comments as $comment)
                            @if(!$comment->is_internal || auth()->user()->role !== 'Driver')
                            <div class="comment-item d-flex mb-3">
                                <img src="{{ $comment->user->profile_picture_url ?? asset('images/default-avatar.png') }}" 
                                     class="rounded-circle me-3" width="40" height="40">
                                <div class="flex-grow-1">
                                    <div class="comment-bubble {{ $comment->is_internal ? 'bg-warning bg-opacity-10' : 'bg-light' }} p-3 rounded">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <strong>{{ $comment->user->name }}</strong>
                                                @if($comment->is_internal)
                                                <span class="badge bg-warning text-dark ms-2">Internal</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                        <p class="text-muted text-center">No comments yet. Be the first to comment!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
<div class="col-lg-4">
    <!-- People -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">People</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <h6 class="text-muted mb-2">Requester</h6>
                <div class="d-flex align-items-center">
                    <img src="{{ $serviceRequest->requester->profile_picture_url ?? asset('images/default-avatar.png') }}"
                         class="rounded-circle me-2" width="32" height="32">
                    <div>
                        <div>{{ $serviceRequest->requester->name }}</div>
                        <small class="text-muted">{{ $serviceRequest->requester->email }}</small>
                    </div>
                </div>
            </div>

            <div>
                <h6 class="text-muted mb-2">Assigned To</h6>
                @if($serviceRequest->assignee)
                    <div class="d-flex align-items-center">
                        <img src="{{ $serviceRequest->assignee->profile_picture_url ?? asset('images/default-avatar.png') }}"
                             class="rounded-circle me-2" width="32" height="32">
                        <div>
                            <div>{{ $serviceRequest->assignee->name }}</div>
                            <small class="text-muted">{{ $serviceRequest->assignee->role }}</small>
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0">Unassigned</p>
                @endif
            </div>

            @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Technician')
                <hr>
                <form action="{{ route('support.service-requests.assign', $serviceRequest) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Assign Technician</label>
                        <select name="technician_id" class="form-select form-select-sm">
                            <option value="">Select Technician</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $serviceRequest->assigned_to == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Update Assignment</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Actions (admins & technicians only) --}}
    @if(in_array(strtolower(auth()->user()->role), ['admin','technician']))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                {{-- Update Status --}}
                <form action="{{ route('support.service-requests.update', $serviceRequest) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="open"       @selected($serviceRequest->status=='open')>Open</option>
                            <option value="in_progress"@selected($serviceRequest->status=='in_progress')>In Progress</option>
                            <option value="pending"    @selected($serviceRequest->status=='pending')>Pending</option>
                            <option value="resolved"   @selected($serviceRequest->status=='resolved')>Resolved</option>
                            <option value="closed"     @selected($serviceRequest->status=='closed')>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success w-100">Update Status</button>
                </form>

                {{-- Update Priority --}}
                <form action="{{ route('support.service-requests.priority', $serviceRequest) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Priority</label>
                        <select name="priority" class="form-select form-select-sm">
                            <option value="low"      @selected($serviceRequest->priority=='low')>Low</option>
                            <option value="medium"   @selected($serviceRequest->priority=='medium')>Medium</option>
                            <option value="high"     @selected($serviceRequest->priority=='high')>High</option>
                            <option value="critical" @selected($serviceRequest->priority=='critical')>Critical</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-warning w-100">Update Priority</button>
                </form>

                @if($serviceRequest->status !== 'closed')
                    <form action="{{ route('support.service-requests.close', $serviceRequest) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to close this request?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger w-100">
                            <i class="fas fa-times me-1"></i>Close Request
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif
        <!-- Timeline -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline-simple">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <strong>Request Created</strong>
                                <small class="d-block text-muted">{{ $serviceRequest->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                        @if($serviceRequest->resolution_time)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <strong>Resolved</strong>
                                <small class="d-block text-muted">{{ $serviceRequest->resolution_time->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
    </div>
</div>

<style>
.comment-bubble {
    position: relative;
}

.timeline-simple {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child):after {
    content: '';
    position: absolute;
    left: -21px;
    top: 20px;
    height: calc(100% - 5px);
    width: 2px;
    background: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
</style>
@endsection
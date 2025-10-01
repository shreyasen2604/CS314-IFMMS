@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-bullhorn"></i> Announcements
                </h1>
                @if(auth()->user()->role === 'Admin')
                    <a href="{{ route('communication.create-announcement') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Announcement
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="row">
        <div class="col-12">
            @if($announcements->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No active announcements at this time</p>
                    </div>
                </div>
            @else
                @foreach($announcements as $announcement)
                    <div class="card mb-3 
                        @if($announcement->type === 'alert') border-danger
                        @elseif($announcement->type === 'warning') border-warning
                        @elseif($announcement->type === 'success') border-success
                        @else border-info
                        @endif">
                        <div class="card-header 
                            @if($announcement->type === 'alert') bg-danger text-white
                            @elseif($announcement->type === 'warning') bg-warning text-dark
                            @elseif($announcement->type === 'success') bg-success text-white
                            @else bg-info text-white
                            @endif">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    @if($announcement->type === 'alert')
                                        <i class="fas fa-exclamation-triangle"></i>
                                    @elseif($announcement->type === 'warning')
                                        <i class="fas fa-exclamation-circle"></i>
                                    @elseif($announcement->type === 'success')
                                        <i class="fas fa-check-circle"></i>
                                    @else
                                        <i class="fas fa-info-circle"></i>
                                    @endif
                                    {{ $announcement->title }}
                                </h5>
                                <div>
                                    <span class="badge bg-light text-dark">
                                        {{ ucfirst($announcement->target_audience) }}
                                    </span>
                                    @if($announcement->views_count > 0)
                                        <span class="badge bg-light text-dark ms-1">
                                            <i class="fas fa-eye"></i> {{ $announcement->views_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                            
                            @if($announcement->attachments && count($announcement->attachments) > 0)
                                <div class="mb-3">
                                    <strong>Attachments:</strong>
                                    @foreach($announcement->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                           class="btn btn-sm btn-outline-secondary ms-2" 
                                           target="_blank" download>
                                            <i class="fas fa-download"></i> {{ $attachment['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Posted by {{ $announcement->creator->name }} 
                                    on {{ $announcement->created_at->format('F d, Y H:i') }}
                                    @if($announcement->publish_at && $announcement->publish_at->isFuture())
                                        <span class="badge bg-warning text-dark ms-1">
                                            Scheduled for {{ $announcement->publish_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                    @if($announcement->expire_at)
                                        <span class="badge bg-secondary ms-1">
                                            Expires {{ $announcement->expire_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                </small>
                                <a href="{{ route('communication.view-announcement', $announcement->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
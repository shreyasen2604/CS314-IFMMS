@extends('layouts.app')

@section('title', 'View Announcement')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-bullhorn"></i> View Announcement
                </h1>
                <a href="{{ route('communication.announcements') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Announcements
                </a>
            </div>
        </div>
    </div>

    <!-- Announcement Details -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card 
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
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-0">
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
                            </h4>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark">
                                {{ ucfirst($announcement->type) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Metadata -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block">
                                <strong>Posted by:</strong> {{ $announcement->creator->name }}
                            </small>
                            <small class="text-muted d-block">
                                <strong>Date:</strong> {{ $announcement->created_at->format('F d, Y H:i') }}
                            </small>
                            <small class="text-muted d-block">
                                <strong>Target Audience:</strong> 
                                <span class="badge bg-secondary">{{ ucfirst($announcement->target_audience) }}</span>
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            @if($announcement->publish_at && $announcement->publish_at->isFuture())
                                <small class="text-muted d-block">
                                    <strong>Scheduled for:</strong> {{ $announcement->publish_at->format('M d, Y H:i') }}
                                </small>
                            @endif
                            @if($announcement->expire_at)
                                <small class="text-muted d-block">
                                    <strong>Expires:</strong> {{ $announcement->expire_at->format('M d, Y H:i') }}
                                    @if($announcement->expire_at->isPast())
                                        <span class="badge bg-danger">Expired</span>
                                    @elseif($announcement->expire_at->diffInDays(now()) <= 3)
                                        <span class="badge bg-warning">Expiring Soon</span>
                                    @endif
                                </small>
                            @endif
                            <small class="text-muted d-block">
                                <strong>Views:</strong> 
                                <i class="fas fa-eye"></i> {{ $announcement->views_count }}
                            </small>
                        </div>
                    </div>

                    <hr>

                    <!-- Content -->
                    <div class="announcement-content mb-4">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>

                    <!-- Attachments -->
                    @if($announcement->attachments && count($announcement->attachments) > 0)
                        <hr>
                        <div class="attachments mb-4">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-paperclip"></i> Attachments ({{ count($announcement->attachments) }})
                            </h6>
                            <div class="row">
                                @foreach($announcement->attachments as $attachment)
                                    <div class="col-md-6 mb-2">
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                           class="btn btn-outline-secondary btn-block w-100" 
                                           target="_blank" download>
                                            <i class="fas fa-download me-2"></i>
                                            {{ $attachment['name'] }}
                                            <small class="d-block text-muted">
                                                {{ formatFileSize($attachment['size'] ?? 0) }}
                                            </small>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        
                        <button type="button" class="btn btn-outline-primary" onclick="shareAnnouncement()">
                            <i class="fas fa-share"></i> Share
                        </button>
                        
                        @if(auth()->user()->role === 'Admin' && auth()->id() === $announcement->created_by)
                            <a href="{{ route('communication.edit-announcement', $announcement->id) }}" 
                               class="btn btn-outline-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="deleteAnnouncement({{ $announcement->id }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        @endif
                    </div>

                    <!-- Status Information -->
                    @if(!$announcement->isCurrentlyActive())
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> 
                            @if($announcement->publish_at && $announcement->publish_at->isFuture())
                                This announcement is scheduled to be published on {{ $announcement->publish_at->format('M d, Y H:i') }}.
                            @elseif($announcement->expire_at && $announcement->expire_at->isPast())
                                This announcement has expired on {{ $announcement->expire_at->format('M d, Y H:i') }}.
                            @else
                                This announcement is currently inactive.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function shareAnnouncement() {
    const url = window.location.href;
    const title = '{{ $announcement->title }}';
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: 'Check out this announcement',
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Announcement link copied to clipboard!');
        });
    }
}

function deleteAnnouncement(announcementId) {
    if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/communication/announcements/${announcementId}/delete`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@php
function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
@endphp

<style>
@media print {
    .btn, .navbar, .sidebar {
        display: none !important;
    }
    .card {
        border: 1px solid #000 !important;
    }
}
</style>
@endsection
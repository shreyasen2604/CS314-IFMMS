@extends('layouts.app')

@section('title', 'Communication Hub')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">
                <i class="fas fa-comments"></i> Communication Hub
            </h1>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Unread Messages</h6>
                            <h3 class="mb-0">{{ $unreadMessages }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Active Announcements</h6>
                            <h3 class="mb-0">{{ $announcements->count() }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-bullhorn fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Notifications</h6>
                            <h3 class="mb-0">{{ $notifications->count() }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Preferences</h6>
                            <h3 class="mb-0">
                                <i class="fas fa-check-circle"></i> Active
                            </h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-cog fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Messages Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-inbox"></i> Recent Messages
                    </h5>
                    <div>
                        <a href="{{ route('communication.compose') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-plus"></i> Compose
                        </a>
                        <a href="{{ route('communication.messages') }}" class="btn btn-sm btn-light">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentMessages->isEmpty())
                        <p class="text-muted text-center py-3">No messages yet</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentMessages as $message)
                                <a href="{{ route('communication.view-message', $message->id) }}" 
                                   class="list-group-item list-group-item-action {{ !$message->is_read ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div class="mb-1">
                                            <strong>{{ $message->sender ? $message->sender->name : 'System' }}</strong>
                                            @if(!$message->is_read)
                                                <span class="badge bg-primary">New</span>
                                            @endif
                                            @if($message->priority === 'urgent')
                                                <span class="badge bg-danger">Urgent</span>
                                            @elseif($message->priority === 'high')
                                                <span class="badge bg-warning">High</span>
                                            @endif
                                        </div>
                                        <small>{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $message->subject }}</p>
                                    <small class="text-muted">{{ Str::limit($message->body, 100) }}</small>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Announcements Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bullhorn"></i> Recent Announcements
                    </h5>
                    <div>
                        @if(auth()->user()->role === 'Admin')
                            <a href="{{ route('communication.create-announcement') }}" class="btn btn-sm btn-light">
                                <i class="fas fa-plus"></i> Create
                            </a>
                        @endif
                        <a href="{{ route('communication.announcements') }}" class="btn btn-sm btn-light">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($announcements->isEmpty())
                        <p class="text-muted text-center py-3">No active announcements</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($announcements as $announcement)
                                <a href="{{ route('communication.view-announcement', $announcement->id) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            @if($announcement->type === 'alert')
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            @elseif($announcement->type === 'warning')
                                                <i class="fas fa-exclamation-circle text-warning"></i>
                                            @elseif($announcement->type === 'success')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-info-circle text-info"></i>
                                            @endif
                                            {{ $announcement->title }}
                                        </h6>
                                        <small>{{ $announcement->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-muted">{{ Str::limit($announcement->content, 100) }}</p>
                                    <small>
                                        <span class="badge bg-secondary">{{ ucfirst($announcement->target_audience) }}</span>
                                        <span class="text-muted">by {{ $announcement->creator->name }}</span>
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications & Preferences -->
    <div class="row">
        <!-- Recent Notifications -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-bell"></i> Recent Notifications
                    </h5>
                </div>
                <div class="card-body">
                    @if($notifications->isEmpty())
                        <p class="text-muted text-center py-3">No new notifications</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            @if($notification->type === 'App\Notifications\NewMessageNotification')
                                                <i class="fas fa-envelope text-primary"></i>
                                                <strong>New Message</strong> from {{ $notification->data['sender_name'] }}
                                            @elseif($notification->type === 'App\Notifications\AnnouncementNotification')
                                                <i class="fas fa-bullhorn text-info"></i>
                                                <strong>New Announcement:</strong> {{ $notification->data['title'] }}
                                            @else
                                                <i class="fas fa-bell text-warning"></i>
                                                {{ $notification->data['message'] ?? 'New notification' }}
                                            @endif
                                        </div>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if(isset($notification->data['preview']))
                                        <p class="mb-0 mt-1 text-muted small">{{ $notification->data['preview'] }}</p>
                                    @endif
                                    <button class="btn btn-sm btn-link p-0 mt-1" onclick="markAsRead('{{ $notification->id }}')">
                                        Mark as read
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                                Mark all as read
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog"></i> Notification Preferences
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('communication.update-preferences') }}" method="POST">
                        @csrf
                        <h6 class="mb-3">Notification Channels</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="email_notifications" 
                                   id="email_notifications" {{ $preferences->email_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                <i class="fas fa-envelope"></i> Email Notifications
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="sms_notifications" 
                                   id="sms_notifications" {{ $preferences->sms_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_notifications">
                                <i class="fas fa-sms"></i> SMS Notifications
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="push_notifications" 
                                   id="push_notifications" {{ $preferences->push_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="push_notifications">
                                <i class="fas fa-mobile-alt"></i> Push Notifications
                            </label>
                        </div>

                        <h6 class="mb-3">Notification Types</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="message_notifications" 
                                   id="message_notifications" {{ $preferences->message_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="message_notifications">
                                Messages
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="announcement_notifications" 
                                   id="announcement_notifications" {{ $preferences->announcement_notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="announcement_notifications">
                                Announcements
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="maintenance_alerts" 
                                   id="maintenance_alerts" {{ $preferences->maintenance_alerts ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_alerts">
                                Maintenance Alerts
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="incident_updates" 
                                   id="incident_updates" {{ $preferences->incident_updates ? 'checked' : '' }}>
                            <label class="form-check-label" for="incident_updates">
                                Incident Updates
                            </label>
                        </div>

                        <h6 class="mb-3">Quiet Hours</h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="quiet_hours_start" class="form-label">Start Time</label>
                                <input type="time" class="form-control" name="quiet_hours_start" 
                                       value="{{ $preferences->quiet_hours['start'] ?? '' }}">
                            </div>
                            <div class="col-6">
                                <label for="quiet_hours_end" class="form-label">End Time</label>
                                <input type="time" class="form-control" name="quiet_hours_end" 
                                       value="{{ $preferences->quiet_hours['end'] ?? '' }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch('{{ route("communication.mark-notifications-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('{{ route("communication.mark-notifications-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Update notification badge
setInterval(function() {
    fetch('{{ route("communication.notification-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.textContent = data.total;
                badge.style.display = data.total > 0 ? 'inline' : 'none';
            }
        });
}, 30000); // Check every 30 seconds
</script>
@endpush
@endsection
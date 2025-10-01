@extends('layouts.app')

@section('title', 'View Message')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-envelope-open"></i> View Message
                </h1>
                <a href="{{ route('communication.messages') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Messages
                </a>
            </div>
        </div>
    </div>

    <!-- Message Details -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1">{{ $message->subject }}</h5>
                            <div class="text-muted">
                                <small>
                                    <strong>From:</strong> {{ $message->sender ? $message->sender->name : 'System' }}
                                    @if($message->sender)
                                        &lt;{{ $message->sender->email }}&gt;
                                    @endif
                                </small>
                                <br>
                                <small>
                                    <strong>To:</strong> 
                                    @if($message->receiver)
                                        {{ $message->receiver->name }} &lt;{{ $message->receiver->email }}&gt;
                                    @else
                                        All Users
                                    @endif
                                </small>
                                <br>
                                <small>
                                    <strong>Date:</strong> {{ $message->created_at->format('F d, Y H:i') }}
                                    ({{ $message->created_at->diffForHumans() }})
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($message->priority === 'urgent')
                                <span class="badge bg-danger">Urgent</span>
                            @elseif($message->priority === 'high')
                                <span class="badge bg-warning">High Priority</span>
                            @elseif($message->priority === 'low')
                                <span class="badge bg-secondary">Low Priority</span>
                            @else
                                <span class="badge bg-info">Normal</span>
                            @endif
                            
                            @if($message->type === 'broadcast')
                                <span class="badge bg-primary">Broadcast</span>
                            @elseif($message->type === 'system')
                                <span class="badge bg-dark">System</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Message Body -->
                    <div class="message-content mb-4">
                        {!! nl2br(e($message->body)) !!}
                    </div>

                    <!-- Attachments -->
                    @if($message->attachments && count($message->attachments) > 0)
                        <div class="attachments mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-paperclip"></i> Attachments ({{ count($message->attachments) }})
                            </h6>
                            <div class="list-group">
                                @foreach($message->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                       class="list-group-item list-group-item-action" 
                                       target="_blank" download>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file me-2"></i>
                                                {{ $attachment['name'] }}
                                            </div>
                                            <small class="text-muted">
                                                {{ formatFileSize($attachment['size'] ?? 0) }}
                                            </small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mb-4">
                        @if($message->sender_id !== auth()->id())
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        
                        @if($message->sender_id === auth()->id())
                            <button type="button" class="btn btn-outline-danger" onclick="deleteMessage({{ $message->id }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-secondary" onclick="archiveMessage({{ $message->id }})">
                                <i class="fas fa-archive"></i> Archive
                            </button>
                        @endif
                    </div>

                    <!-- Replies Thread -->
                    @if($message->replies->count() > 0 || $message->parent)
                        <div class="replies-section">
                            <h6 class="mb-3">
                                <i class="fas fa-comments"></i> Conversation Thread
                            </h6>
                            
                            @if($message->parent)
                                <div class="alert alert-info">
                                    <small>This is a reply to: 
                                        <a href="{{ route('communication.view-message', $message->parent->id) }}">
                                            {{ $message->parent->subject }}
                                        </a>
                                    </small>
                                </div>
                            @endif

                            @foreach($message->replies as $reply)
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <strong>{{ $reply->sender->name }}</strong>
                                            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0">{!! nl2br(e($reply->body)) !!}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('communication.reply-message', $message->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reply to Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Replying to:</label>
                        <div class="border rounded p-2 bg-light">
                            <strong>{{ $message->sender ? $message->sender->name : 'System' }}</strong><br>
                            Subject: {{ $message->subject }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reply-body" class="form-label">Your Reply <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply-body" name="body" rows="6" required 
                                  placeholder="Type your reply here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function archiveMessage(messageId) {
    if (confirm('Are you sure you want to archive this message?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/communication/messages/${messageId}/archive`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteMessage(messageId) {
    if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/communication/messages/${messageId}/delete`;
        
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
@endsection
@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-envelope"></i> Messages
                </h1>
                <div>
                    <a href="{{ route('communication.compose') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Compose Message
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="btn-group" role="group">
                                <a href="{{ route('communication.messages', ['type' => 'inbox']) }}" 
                                   class="btn {{ $type === 'inbox' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-inbox"></i> Inbox
                                </a>
                                <a href="{{ route('communication.messages', ['type' => 'sent']) }}" 
                                   class="btn {{ $type === 'sent' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-paper-plane"></i> Sent
                                </a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <form method="GET" action="{{ route('communication.messages') }}" class="d-flex gap-2">
                                <input type="hidden" name="type" value="{{ $type }}">
                                
                                <select name="priority" class="form-select" style="width: auto;">
                                    <option value="">All Priorities</option>
                                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                                
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="unread" id="unread" 
                                           {{ request('unread') ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="unread">
                                        Unread Only
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                
                                @if(request()->hasAny(['priority', 'unread']))
                                    <a href="{{ route('communication.messages', ['type' => $type]) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($messages->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No messages found</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="30"></th>
                                        <th>{{ $type === 'sent' ? 'To' : 'From' }}</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Date</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($messages as $message)
                                        <tr class="{{ !$message->is_read && $type === 'inbox' ? 'table-active' : '' }}">
                                            <td>
                                                @if(!$message->is_read && $type === 'inbox')
                                                    <i class="fas fa-circle text-primary" title="Unread"></i>
                                                @else
                                                    <i class="far fa-circle text-muted"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if($type === 'sent')
                                                    {{ $message->receiver ? $message->receiver->name : 'All Users' }}
                                                @else
                                                    {{ $message->sender ? $message->sender->name : 'System' }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('communication.view-message', $message->id) }}" 
                                                   class="text-decoration-none">
                                                    {{ $message->subject }}
                                                    @if($message->attachments && count($message->attachments) > 0)
                                                        <i class="fas fa-paperclip text-muted ms-1"></i>
                                                    @endif
                                                    @if($message->replies->count() > 0)
                                                        <span class="badge bg-secondary ms-1">
                                                            {{ $message->replies->count() }} {{ Str::plural('reply', $message->replies->count()) }}
                                                        </span>
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                                @if($message->priority === 'urgent')
                                                    <span class="badge bg-danger">Urgent</span>
                                                @elseif($message->priority === 'high')
                                                    <span class="badge bg-warning">High</span>
                                                @elseif($message->priority === 'low')
                                                    <span class="badge bg-secondary">Low</span>
                                                @else
                                                    <span class="badge bg-info">Normal</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $message->created_at->format('M d, Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('communication.view-message', $message->id) }}" 
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($type === 'inbox')
                                                        <button type="button" class="btn btn-outline-secondary" 
                                                                onclick="archiveMessage({{ $message->id }})" title="Archive">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                    @endif
                                                    @if($message->sender_id === auth()->id())
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteMessage({{ $message->id }})" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $messages->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
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
@endsection
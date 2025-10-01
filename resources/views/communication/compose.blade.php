@extends('layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-edit"></i> Compose Message
                </h1>
                <a href="{{ route('communication.messages') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Messages
                </a>
            </div>
        </div>
    </div>

    <!-- Compose Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('communication.send-message') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Message Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Message Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="direct" {{ old('type') === 'direct' ? 'selected' : '' }}>Direct Message</option>
                                @if(auth()->user()->role === 'Admin')
                                    <option value="broadcast" {{ old('type') === 'broadcast' ? 'selected' : '' }}>Broadcast to All</option>
                                    <option value="system" {{ old('type') === 'system' ? 'selected' : '' }}>System Message</option>
                                @endif
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Recipient -->
                        <div class="mb-3" id="recipient-field">
                            <label for="receiver_id" class="form-label">To <span class="text-danger">*</span></label>
                            <select class="form-select @error('receiver_id') is-invalid @enderror" 
                                    id="receiver_id" name="receiver_id">
                                <option value="">Select recipient...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role }}) - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('receiver_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Start typing to search for users</small>
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" 
                                   placeholder="Enter message subject" required maxlength="255">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Body -->
                        <div class="mb-3">
                            <label for="body" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" name="body" rows="8" 
                                      placeholder="Type your message here..." required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-4">
                            <label for="attachments" class="form-label">Attachments (Optional)</label>
                            <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                   id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif">
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                You can attach multiple files. Max file size: 10MB per file. 
                                Allowed formats: PDF, Word, Excel, Images
                            </small>
                            <div id="file-list" class="mt-2"></div>
                        </div>

                        <!-- Priority Alert -->
                        <div class="alert alert-warning d-none" id="priority-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>High Priority Message:</strong> This message will be marked as high priority and the recipient will receive immediate notification.
                        </div>

                        <div class="alert alert-danger d-none" id="urgent-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <strong>Urgent Message:</strong> This message will trigger immediate notifications across all channels for the recipient.
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <div>
                                <button type="button" class="btn btn-secondary me-2" onclick="previewMessage()">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="preview-content">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.querySelector('form').submit()">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Handle message type change
document.getElementById('type').addEventListener('change', function() {
    const recipientField = document.getElementById('recipient-field');
    const receiverSelect = document.getElementById('receiver_id');
    
    if (this.value === 'broadcast' || this.value === 'system') {
        recipientField.style.display = 'none';
        receiverSelect.removeAttribute('required');
    } else {
        recipientField.style.display = 'block';
        receiverSelect.setAttribute('required', 'required');
    }
});

// Handle priority change
document.getElementById('priority').addEventListener('change', function() {
    const priorityAlert = document.getElementById('priority-alert');
    const urgentAlert = document.getElementById('urgent-alert');
    
    priorityAlert.classList.add('d-none');
    urgentAlert.classList.add('d-none');
    
    if (this.value === 'high') {
        priorityAlert.classList.remove('d-none');
    } else if (this.value === 'urgent') {
        urgentAlert.classList.remove('d-none');
    }
});

// Handle file selection
document.getElementById('attachments').addEventListener('change', function() {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';
    
    if (this.files.length > 0) {
        const list = document.createElement('ul');
        list.className = 'list-unstyled';
        
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            const li = document.createElement('li');
            li.innerHTML = `<i class="fas fa-file"></i> ${file.name} (${formatFileSize(file.size)})`;
            list.appendChild(li);
        }
        
        fileList.appendChild(list);
    }
});

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Preview message
function previewMessage() {
    const type = document.getElementById('type').value;
    const receiver = document.getElementById('receiver_id');
    const priority = document.getElementById('priority').value;
    const subject = document.getElementById('subject').value;
    const body = document.getElementById('body').value;
    
    if (!subject || !body) {
        alert('Please fill in the subject and message body before previewing.');
        return;
    }
    
    let recipientText = 'All Users';
    if (type === 'direct' && receiver.value) {
        recipientText = receiver.options[receiver.selectedIndex].text;
    }
    
    const priorityBadge = {
        'low': '<span class="badge bg-secondary">Low</span>',
        'normal': '<span class="badge bg-info">Normal</span>',
        'high': '<span class="badge bg-warning">High</span>',
        'urgent': '<span class="badge bg-danger">Urgent</span>'
    };
    
    const previewHtml = `
        <div class="mb-3">
            <strong>Type:</strong> ${type.charAt(0).toUpperCase() + type.slice(1)} Message
        </div>
        <div class="mb-3">
            <strong>To:</strong> ${recipientText}
        </div>
        <div class="mb-3">
            <strong>Priority:</strong> ${priorityBadge[priority]}
        </div>
        <div class="mb-3">
            <strong>Subject:</strong> ${subject}
        </div>
        <div class="mb-3">
            <strong>Message:</strong>
            <div class="border rounded p-3 bg-light">
                ${body.replace(/\n/g, '<br>')}
            </div>
        </div>
    `;
    
    document.getElementById('preview-content').innerHTML = previewHtml;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

// Save as draft (placeholder function)
function saveDraft() {
    alert('Draft functionality will be implemented in a future update.');
}

// Initialize Select2 for better user selection (if available)
if (typeof $ !== 'undefined' && $.fn.select2) {
    $('#receiver_id').select2({
        placeholder: 'Select or search for a recipient',
        allowClear: true
    });
}
</script>
@endpush
@endsection
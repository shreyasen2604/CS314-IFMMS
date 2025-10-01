@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-bullhorn"></i> Create Announcement
                </h1>
                <a href="{{ route('communication.announcements') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Announcements
                </a>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('communication.store-announcement') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Enter announcement title" required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select type...</option>
                                <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>
                                    ℹ️ Information
                                </option>
                                <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>
                                    ⚠️ Warning
                                </option>
                                <option value="alert" {{ old('type') === 'alert' ? 'selected' : '' }}>
                                    🚨 Alert
                                </option>
                                <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>
                                    ✅ Success
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Choose the appropriate type based on the announcement's importance and nature
                            </small>
                        </div>

                        <!-- Target Audience -->
                        <div class="mb-3">
                            <label for="target_audience" class="form-label">Target Audience <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_audience') is-invalid @enderror" 
                                    id="target_audience" name="target_audience" required>
                                <option value="">Select audience...</option>
                                <option value="all" {{ old('target_audience') === 'all' ? 'selected' : '' }}>
                                    All Users
                                </option>
                                <option value="drivers" {{ old('target_audience') === 'drivers' ? 'selected' : '' }}>
                                    Drivers Only
                                </option>
                                <option value="technicians" {{ old('target_audience') === 'technicians' ? 'selected' : '' }}>
                                    Technicians Only
                                </option>
                                <option value="admins" {{ old('target_audience') === 'admins' ? 'selected' : '' }}>
                                    Administrators Only
                                </option>
                            </select>
                            @error('target_audience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="8" 
                                      placeholder="Enter announcement content..." required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Scheduling -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="publish_at" class="form-label">Publish Date (Optional)</label>
                                <input type="datetime-local" class="form-control @error('publish_at') is-invalid @enderror" 
                                       id="publish_at" name="publish_at" value="{{ old('publish_at') }}">
                                @error('publish_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Leave empty to publish immediately
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label for="expire_at" class="form-label">Expiry Date (Optional)</label>
                                <input type="datetime-local" class="form-control @error('expire_at') is-invalid @enderror" 
                                       id="expire_at" name="expire_at" value="{{ old('expire_at') }}">
                                @error('expire_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Leave empty for no expiration
                                </small>
                            </div>
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-4">
                            <label for="attachments" class="form-label">Attachments (Optional)</label>
                            <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                   id="attachments" name="attachments[]" multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif">
                            @error('attachments.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                You can attach multiple files. Max file size: 10MB per file. 
                                Allowed formats: PDF, Word, Excel, Images
                            </small>
                            <div id="file-list" class="mt-2"></div>
                        </div>

                        <!-- Type-specific alerts -->
                        <div class="alert alert-info d-none" id="info-alert">
                            <i class="fas fa-info-circle"></i>
                            <strong>Information Announcement:</strong> This will be displayed as a general information notice.
                        </div>

                        <div class="alert alert-warning d-none" id="warning-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning Announcement:</strong> This will be highlighted as a warning that requires attention.
                        </div>

                        <div class="alert alert-danger d-none" id="alert-alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <strong>Alert Announcement:</strong> This will be prominently displayed as a critical alert.
                        </div>

                        <div class="alert alert-success d-none" id="success-alert">
                            <i class="fas fa-check-circle"></i>
                            <strong>Success Announcement:</strong> This will be displayed as a positive update or achievement.
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <div>
                                <button type="button" class="btn btn-secondary me-2" onclick="previewAnnouncement()">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-bullhorn"></i> Publish Announcement
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
                <h5 class="modal-title">Announcement Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="preview-content">
                <!-- Preview content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.querySelector('form').submit()">
                    <i class="fas fa-bullhorn"></i> Publish Announcement
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Handle type change
document.getElementById('type').addEventListener('change', function() {
    // Hide all alerts
    document.querySelectorAll('.alert').forEach(alert => alert.classList.add('d-none'));
    
    // Show relevant alert
    if (this.value) {
        const alertId = this.value + '-alert';
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.classList.remove('d-none');
        }
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

// Preview announcement
function previewAnnouncement() {
    const title = document.getElementById('title').value;
    const type = document.getElementById('type').value;
    const audience = document.getElementById('target_audience').value;
    const content = document.getElementById('content').value;
    const publishAt = document.getElementById('publish_at').value;
    const expireAt = document.getElementById('expire_at').value;
    
    if (!title || !type || !audience || !content) {
        alert('Please fill in all required fields before previewing.');
        return;
    }
    
    const typeIcons = {
        'info': '<i class="fas fa-info-circle text-info"></i>',
        'warning': '<i class="fas fa-exclamation-triangle text-warning"></i>',
        'alert': '<i class="fas fa-exclamation-circle text-danger"></i>',
        'success': '<i class="fas fa-check-circle text-success"></i>'
    };
    
    const typeColors = {
        'info': 'info',
        'warning': 'warning',
        'alert': 'danger',
        'success': 'success'
    };
    
    const audienceText = {
        'all': 'All Users',
        'drivers': 'Drivers Only',
        'technicians': 'Technicians Only',
        'admins': 'Administrators Only'
    };
    
    let scheduleInfo = '';
    if (publishAt) {
        scheduleInfo += `<div class="mb-2"><strong>Publish Date:</strong> ${new Date(publishAt).toLocaleString()}</div>`;
    }
    if (expireAt) {
        scheduleInfo += `<div class="mb-2"><strong>Expiry Date:</strong> ${new Date(expireAt).toLocaleString()}</div>`;
    }
    
    const previewHtml = `
        <div class="card border-${typeColors[type]}">
            <div class="card-header bg-${typeColors[type]} text-${type === 'warning' ? 'dark' : 'white'}">
                <h5 class="mb-0">
                    ${typeIcons[type]} ${title}
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-secondary">${audienceText[audience]}</span>
                    <span class="badge bg-${typeColors[type]}">${type.toUpperCase()}</span>
                </div>
                ${scheduleInfo}
                <div class="mb-3">
                    ${content.replace(/\n/g, '<br>')}
                </div>
                <small class="text-muted">
                    Posted by {{ auth()->user()->name }} - Just now
                </small>
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

// Validate dates
document.getElementById('expire_at').addEventListener('change', function() {
    const publishAt = document.getElementById('publish_at').value;
    if (publishAt && this.value && new Date(this.value) <= new Date(publishAt)) {
        alert('Expiry date must be after the publish date.');
        this.value = '';
    }
});
</script>
@endpush
@endsection
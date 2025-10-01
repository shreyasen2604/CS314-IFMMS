@extends('layouts.app')

@section('title', 'Help & Support - IFMMS-ZAR')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="fas fa-life-ring text-primary me-2"></i>
            Help & Support Center
        </h1>
        <p class="page-subtitle">Find tutorials, documentation, and get assistance with IFMMS-ZAR</p>
    </div>

    <!-- Quick Search -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control form-control-lg" placeholder="Search for help articles, tutorials, or FAQs...">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Categories -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-video fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Video Tutorials</h5>
                    <p class="card-text">Watch step-by-step video guides</p>
                    <a href="#tutorials" class="btn btn-outline-primary">View Tutorials</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-book fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">Documentation</h5>
                    <p class="card-text">Read detailed guides and manuals</p>
                    <a href="#documentation" class="btn btn-outline-info">Browse Docs</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-question-circle fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">FAQs</h5>
                    <p class="card-text">Find answers to common questions</p>
                    <a href="#faqs" class="btn btn-outline-warning">View FAQs</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-headset fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">Contact Support</h5>
                    <p class="card-text">Get help from our support team</p>
                    <a href="#contact" class="btn btn-outline-success">Contact Us</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Tutorials Section -->
    <div class="row mb-4" id="tutorials">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-video me-2"></i>Video Tutorials
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->role === 'Technician' || auth()->user()->role === 'Admin')
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Work Order Management</h6>
                                    <p class="card-text small">Learn how to manage work orders efficiently</p>
                                    <span class="badge bg-primary">Technician</span>
                                    <span class="badge bg-secondary">12:30</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Diagnostic Tools Guide</h6>
                                    <p class="card-text small">Using diagnostic features and reports</p>
                                    <span class="badge bg-primary">Technician</span>
                                    <span class="badge bg-secondary">9:20</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Parts Management</h6>
                                    <p class="card-text small">Request and manage parts inventory</p>
                                    <span class="badge bg-primary">Technician</span>
                                    <span class="badge bg-secondary">7:45</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Getting Started</h6>
                                    <p class="card-text small">Introduction to IFMMS-ZAR system</p>
                                    <span class="badge bg-success">All Users</span>
                                    <span class="badge bg-secondary">5:30</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Dashboard Overview</h6>
                                    <p class="card-text small">Navigate your personalized dashboard</p>
                                    <span class="badge bg-success">All Users</span>
                                    <span class="badge bg-secondary">8:15</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation Section -->
    <div class="row mb-4" id="documentation">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>Documentation
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <h6 class="text-primary">Quick Start</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Getting Started Guide</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>First Time Login</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Dashboard Navigation</a></li>
                            </ul>
                        </div>
                        @if(auth()->user()->role === 'Technician' || auth()->user()->role === 'Admin')
                        <div class="col-md-6 col-lg-3">
                            <h6 class="text-primary">Technician Guides</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Work Order Manual</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Maintenance Procedures</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Safety Guidelines</a></li>
                            </ul>
                        </div>
                        @endif
                        @if(auth()->user()->role === 'Driver' || auth()->user()->role === 'Admin')
                        <div class="col-md-6 col-lg-3">
                            <h6 class="text-primary">Driver Resources</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Driver Handbook</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Vehicle Inspection</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Incident Reporting</a></li>
                            </ul>
                        </div>
                        @endif
                        @if(auth()->user()->role === 'Admin')
                        <div class="col-md-6 col-lg-3">
                            <h6 class="text-primary">Admin Guides</h6>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>Admin Manual</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>User Management</a></li>
                                <li><a href="#" class="text-decoration-none"><i class="fas fa-file-pdf text-danger me-2"></i>System Configuration</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQs Section -->
    <div class="row mb-4" id="faqs">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>Frequently Asked Questions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I reset my password?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    To reset your password, click on the "Forgot Password" link on the login page. Enter your email address, and you'll receive instructions to reset your password.
                                </div>
                            </div>
                        </div>
                        
                        @if(auth()->user()->role === 'Technician' || auth()->user()->role === 'Admin')
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How do I claim a work order?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Navigate to the Work Orders page, find an available task in the "Available Work Orders" section, and click the "Claim Task" button. The work order will be assigned to you immediately.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How do I request parts for a repair?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Within a work order, click on "Request Parts" button. Fill in the parts request form with part numbers, quantities, and justification. The request will be sent to the parts department for approval.
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    What browsers are supported?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    IFMMS-ZAR supports the latest versions of Chrome, Firefox, Safari, and Edge. For the best experience, we recommend using Chrome or Firefox.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support Section -->
    <div class="row" id="contact">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-headset me-2"></i>Contact Support
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                                <h5>Live Chat</h5>
                                <p class="text-muted">Chat with our support team</p>
                                <p class="text-success"><i class="fas fa-circle"></i> Available Now</p>
                                <button class="btn btn-primary" onclick="alert('Live chat feature coming soon!')">Start Chat</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-envelope fa-3x text-info mb-3"></i>
                                <h5>Email Support</h5>
                                <p class="text-muted">Send us an email</p>
                                <p><strong>support@ifmms-zar.com</strong></p>
                                <a href="mailto:support@ifmms-zar.com" class="btn btn-info">Send Email</a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-phone fa-3x text-success mb-3"></i>
                                <h5>Phone Support</h5>
                                <p class="text-muted">Call our helpdesk</p>
                                <p><strong>+1 (555) 123-4567</strong></p>
                                <p class="text-muted small">Mon-Fri, 8AM-6PM EST</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-ticket-alt fa-3x text-warning mb-3"></i>
                                <h5>Submit Ticket</h5>
                                <p class="text-muted">Create a support ticket</p>
                                <p class="text-muted small">Response within 24 hours</p>
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ticketModal">Create Ticket</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Support Ticket Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" required>
                                <option value="">Select Category</option>
                                <option>Technical Issue</option>
                                <option>Account Problem</option>
                                <option>Training Request</option>
                                <option>Feature Request</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <select class="form-select" required>
                                <option>Low</option>
                                <option selected>Medium</option>
                                <option>High</option>
                                <option>Urgent</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Attachments</label>
                            <input type="file" class="form-control" multiple>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

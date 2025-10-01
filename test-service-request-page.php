<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ServiceRequest;

try {
    echo "Testing Service Request with Comments...\n";
    echo "========================================\n\n";
    
    // Get a service request with all relationships
    $serviceRequest = ServiceRequest::with(['vehicle', 'requester', 'assignee', 'comments.user'])->first();
    
    if ($serviceRequest) {
        echo "Service Request Found:\n";
        echo "  - ID: {$serviceRequest->id}\n";
        echo "  - Ticket: {$serviceRequest->ticket_number}\n";
        echo "  - Status: {$serviceRequest->status}\n";
        echo "  - Priority: {$serviceRequest->priority}\n";
        echo "  - Comments: {$serviceRequest->comments->count()}\n";
        
        if ($serviceRequest->comments->count() > 0) {
            echo "\nComments:\n";
            foreach ($serviceRequest->comments as $comment) {
                echo "  - By {$comment->user->name}: " . substr($comment->comment, 0, 50) . "...\n";
            }
        }
        
        echo "\n✅ All relationships loaded successfully!\n";
        echo "The service request page should work now.\n";
        
    } else {
        echo "No service requests found in the database.\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "SQL Error Details: " . $e->getMessage() . "\n";
}
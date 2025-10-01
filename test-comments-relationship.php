<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ServiceRequest;
use App\Models\SupportComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Testing Support Comments Relationship...\n";
    echo "=====================================\n\n";
    
    // Check table structure
    echo "1. Checking table structure:\n";
    $columns = Schema::getColumnListing('support_comments');
    echo "   Columns in support_comments table: " . implode(', ', $columns) . "\n\n";
    
    // Check if there are any service requests
    echo "2. Checking ServiceRequest data:\n";
    $serviceRequest = ServiceRequest::first();
    if ($serviceRequest) {
        echo "   Found ServiceRequest ID: {$serviceRequest->id}\n";
        echo "   Ticket Number: {$serviceRequest->ticket_number}\n\n";
        
        // Test the relationship
        echo "3. Testing comments relationship:\n";
        try {
            // Try to access comments without eager loading
            $comments = $serviceRequest->comments;
            echo "   ✅ Comments relationship works! Found " . $comments->count() . " comments.\n";
            
            // Try to access with eager loading
            echo "\n4. Testing eager loading:\n";
            $sr = ServiceRequest::with('comments')->find($serviceRequest->id);
            echo "   ✅ Eager loading works!\n";
            
            // Try with nested eager loading
            echo "\n5. Testing nested eager loading:\n";
            $sr = ServiceRequest::with('comments.user')->find($serviceRequest->id);
            echo "   ✅ Nested eager loading works!\n";
            
        } catch (\Exception $e) {
            echo "   ❌ Error accessing comments: " . $e->getMessage() . "\n";
            echo "   SQL: " . DB::getQueryLog()[count(DB::getQueryLog())-1]['query'] ?? 'N/A' . "\n";
        }
        
        // Create a test comment
        echo "\n6. Creating a test comment:\n";
        try {
            $comment = $serviceRequest->comments()->create([
                'user_id' => 1, // Assuming user ID 1 exists
                'comment' => 'Test comment created at ' . now(),
                'is_internal' => false,
                'attachments' => null
            ]);
            echo "   ✅ Comment created successfully with ID: {$comment->id}\n";
            
            // Try to retrieve it
            $retrievedComment = SupportComment::find($comment->id);
            echo "   ✅ Comment retrieved: " . substr($retrievedComment->comment, 0, 50) . "...\n";
            
            // Check the morphable relationship
            echo "\n7. Checking morphable relationship:\n";
            echo "   Commentable Type: {$retrievedComment->commentable_type}\n";
            echo "   Commentable ID: {$retrievedComment->commentable_id}\n";
            
            // Test reverse relationship
            $parent = $retrievedComment->commentable;
            echo "   ✅ Reverse relationship works! Parent ticket: {$parent->ticket_number}\n";
            
        } catch (\Exception $e) {
            echo "   ❌ Error creating comment: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "   No ServiceRequest found. Creating one...\n";
        $serviceRequest = ServiceRequest::create([
            'ticket_number' => 'SR-TEST-' . rand(1000, 9999),
            'requester_id' => 1,
            'category' => 'maintenance',
            'priority' => 'medium',
            'status' => 'open',
            'subject' => 'Test Service Request',
            'description' => 'This is a test service request'
        ]);
        echo "   Created ServiceRequest ID: {$serviceRequest->id}\n";
        echo "   Please run this script again to test the relationships.\n";
    }
    
    // Check for any raw SQL issues
    echo "\n8. Testing raw SQL query:\n";
    $result = DB::select("
        SELECT * FROM support_comments 
        WHERE commentable_type = ? 
        AND commentable_id = ?
        LIMIT 1
    ", ['App\Models\ServiceRequest', $serviceRequest->id]);
    
    echo "   ✅ Raw SQL query works! Found " . count($result) . " records.\n";
    
    echo "\n=====================================\n";
    echo "✅ All tests completed!\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
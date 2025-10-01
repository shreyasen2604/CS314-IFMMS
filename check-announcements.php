<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Announcement;

echo "=== Checking Announcements Table ===\n\n";

// Check if table exists
if (Schema::hasTable('announcements')) {
    echo "✅ Announcements table exists\n\n";
    
    // Get table structure
    $columns = Schema::getColumnListing('announcements');
    echo "Table columns: " . implode(', ', $columns) . "\n\n";
    
    // Count records
    $totalCount = Announcement::count();
    echo "Total announcements in database: $totalCount\n";
    
    // Count active announcements
    $activeCount = Announcement::active()->count();
    echo "Active announcements: $activeCount\n\n";
    
    // Show all announcements with details
    if ($totalCount > 0) {
        echo "=== All Announcements ===\n";
        $announcements = Announcement::with('creator')->get();
        foreach ($announcements as $announcement) {
            echo "ID: {$announcement->id}\n";
            echo "Title: {$announcement->title}\n";
            echo "Type: {$announcement->type}\n";
            echo "Target Audience: {$announcement->target_audience}\n";
            echo "Is Active: " . ($announcement->is_active ? 'Yes' : 'No') . "\n";
            echo "Created By: " . ($announcement->creator ? $announcement->creator->name : 'Unknown') . "\n";
            echo "Created At: {$announcement->created_at}\n";
            echo "Publish At: " . ($announcement->publish_at ?? 'Not set') . "\n";
            echo "Expire At: " . ($announcement->expire_at ?? 'Not set') . "\n";
            echo "Is Currently Active: " . ($announcement->isCurrentlyActive() ? 'Yes' : 'No') . "\n";
            echo "---\n";
        }
        
        // Test the active scope
        echo "\n=== Testing Active Scope ===\n";
        $activeAnnouncements = Announcement::active()->get();
        echo "Active announcements query result count: " . $activeAnnouncements->count() . "\n";
        
        if ($activeAnnouncements->count() > 0) {
            foreach ($activeAnnouncements as $announcement) {
                echo "- {$announcement->title} (ID: {$announcement->id})\n";
            }
        }
        
        // Test for specific roles
        echo "\n=== Testing Role-based Queries ===\n";
        $roles = ['Admin', 'Driver', 'Technician'];
        foreach ($roles as $role) {
            $count = Announcement::active()->forAudience($role)->count();
            echo "$role can see: $count announcements\n";
        }
        
    } else {
        echo "No announcements found in the database.\n";
        echo "\nCreating a sample announcement...\n";
        
        // Create a sample announcement
        $announcement = Announcement::create([
            'title' => 'Welcome to the Communication Module',
            'content' => 'This is a test announcement to verify the system is working correctly. This announcement is visible to all users.',
            'created_by' => 1, // Assuming admin user ID is 1
            'type' => 'info',
            'target_audience' => 'all',
            'is_active' => true,
            'publish_at' => now(),
            'expire_at' => null,
            'attachments' => [],
            'views_count' => 0
        ]);
        
        echo "✅ Sample announcement created with ID: {$announcement->id}\n";
    }
    
} else {
    echo "❌ Announcements table does not exist!\n";
    echo "Please run: php artisan migrate\n";
}

echo "\n=== End of Check ===\n";
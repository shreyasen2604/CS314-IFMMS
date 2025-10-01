<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Announcement;
use Carbon\Carbon;

echo "=== Fixing Announcements ===\n\n";

// Get all announcements
$announcements = Announcement::all();

echo "Found " . $announcements->count() . " announcements\n\n";

foreach ($announcements as $announcement) {
    echo "Processing Announcement ID: {$announcement->id} - {$announcement->title}\n";
    
    // Check current dates
    echo "  Current publish_at: " . ($announcement->publish_at ? $announcement->publish_at->format('Y-m-d H:i:s') : 'null') . "\n";
    echo "  Current expire_at: " . ($announcement->expire_at ? $announcement->expire_at->format('Y-m-d H:i:s') : 'null') . "\n";
    
    // Fix dates - set them to reasonable values
    $updates = [];
    
    // If publish_at is in the future or past year 2025, set it to now or null
    if ($announcement->publish_at) {
        if ($announcement->publish_at->year >= 2025) {
            // Set to created_at date or now
            $updates['publish_at'] = $announcement->created_at ?? now();
            echo "  -> Fixing publish_at to: " . $updates['publish_at'] . "\n";
        }
    }
    
    // If expire_at is set, make sure it's in the future
    if ($announcement->expire_at) {
        if ($announcement->expire_at->year >= 2025 || $announcement->expire_at->isPast()) {
            // Set expiry to 30 days from now
            $updates['expire_at'] = now()->addDays(30);
            echo "  -> Fixing expire_at to: " . $updates['expire_at'] . "\n";
        }
    }
    
    // Ensure is_active is true
    if (!$announcement->is_active) {
        $updates['is_active'] = true;
        echo "  -> Setting is_active to true\n";
    }
    
    // Apply updates if any
    if (!empty($updates)) {
        $announcement->update($updates);
        echo "  ✅ Announcement updated\n";
    } else {
        echo "  ℹ️ No updates needed\n";
    }
    
    echo "\n";
}

// Create a new test announcement that will definitely be active
echo "=== Creating a new active announcement ===\n";

$newAnnouncement = Announcement::create([
    'title' => 'System Maintenance Notice',
    'content' => 'This is an important announcement regarding upcoming system maintenance. The system will undergo scheduled maintenance to improve performance and add new features. Please save your work regularly.',
    'created_by' => 1, // Admin user
    'type' => 'info',
    'target_audience' => 'all',
    'is_active' => true,
    'publish_at' => now()->subHour(), // Published 1 hour ago
    'expire_at' => now()->addDays(7), // Expires in 7 days
    'views_count' => 0
]);

echo "✅ Created new announcement: {$newAnnouncement->title}\n";
echo "  - Publish at: " . $newAnnouncement->publish_at->format('Y-m-d H:i:s') . "\n";
echo "  - Expire at: " . $newAnnouncement->expire_at->format('Y-m-d H:i:s') . "\n";
echo "  - Is active: " . ($newAnnouncement->is_active ? 'Yes' : 'No') . "\n";
echo "  - Is currently active: " . ($newAnnouncement->isCurrentlyActive() ? 'Yes' : 'No') . "\n";

echo "\n=== Verifying Active Announcements ===\n";

$activeCount = Announcement::active()->count();
echo "Active announcements count: $activeCount\n\n";

$activeAnnouncements = Announcement::active()->get();
foreach ($activeAnnouncements as $announcement) {
    echo "- {$announcement->title} (ID: {$announcement->id})\n";
    echo "  Type: {$announcement->type}, Audience: {$announcement->target_audience}\n";
    echo "  Publish: " . ($announcement->publish_at ? $announcement->publish_at->format('Y-m-d H:i') : 'immediately') . "\n";
    echo "  Expire: " . ($announcement->expire_at ? $announcement->expire_at->format('Y-m-d H:i') : 'never') . "\n";
}

if ($activeCount == 0) {
    echo "\n⚠️ Still no active announcements. Checking the issue...\n\n";
    
    // Debug the query
    $query = Announcement::where('is_active', true);
    echo "Announcements with is_active = true: " . $query->count() . "\n";
    
    $query = Announcement::where('is_active', true)
        ->where(function($q) {
            $q->whereNull('publish_at')
              ->orWhere('publish_at', '<=', now());
        });
    echo "After publish_at filter: " . $query->count() . "\n";
    
    $query = Announcement::where('is_active', true)
        ->where(function($q) {
            $q->whereNull('publish_at')
              ->orWhere('publish_at', '<=', now());
        })
        ->where(function($q) {
            $q->whereNull('expire_at')
              ->orWhere('expire_at', '>', now());
        });
    echo "After expire_at filter: " . $query->count() . "\n";
}

echo "\n✅ Fix complete!\n";
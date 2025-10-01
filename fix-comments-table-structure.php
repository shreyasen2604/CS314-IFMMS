<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Fixing support_comments table structure...\n";
    echo "==========================================\n\n";
    
    // First, backup existing data if any
    $hasData = false;
    $backupData = [];
    
    if (Schema::hasTable('support_comments')) {
        $backupData = DB::table('support_comments')->get()->toArray();
        $hasData = count($backupData) > 0;
        
        if ($hasData) {
            echo "Found " . count($backupData) . " existing comments. Backing up...\n";
        }
        
        // Drop the table
        echo "Dropping existing support_comments table...\n";
        Schema::dropIfExists('support_comments');
    }
    
    // Create the table with correct structure
    echo "Creating support_comments table with correct structure...\n";
    Schema::create('support_comments', function (Blueprint $table) {
        $table->id();
        $table->string('commentable_type');
        $table->unsignedBigInteger('commentable_id');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->text('comment');
        $table->boolean('is_internal')->default(false);
        $table->json('attachments')->nullable();
        $table->timestamps();
        
        // Add index for morphable relationship
        $table->index(['commentable_type', 'commentable_id'], 'commentable_index');
    });
    
    echo "Table created successfully.\n";
    
    // Restore data if we had any
    if ($hasData) {
        echo "Restoring backed up data...\n";
        foreach ($backupData as $row) {
            // Convert object to array
            $rowArray = (array) $row;
            DB::table('support_comments')->insert($rowArray);
        }
        echo "Data restored successfully.\n";
    }
    
    // Verify the structure
    echo "\nVerifying table structure:\n";
    $columns = Schema::getColumnListing('support_comments');
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    // Check indexes
    $indexes = DB::select("SHOW INDEX FROM support_comments");
    echo "\nIndexes:\n";
    foreach ($indexes as $index) {
        echo "  - {$index->Key_name} on {$index->Column_name}\n";
    }
    
    // Update migrations table
    $migrationName = '2024_01_15_create_support_comments_table';
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    
    if (!$exists) {
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "\nMigration record added.\n";
    }
    
    // Test the relationship
    echo "\nTesting relationship...\n";
    $serviceRequest = \App\Models\ServiceRequest::first();
    if ($serviceRequest) {
        try {
            $count = $serviceRequest->comments()->count();
            echo "✅ Relationship test successful! Found {$count} comments.\n";
            
            // Test eager loading
            $sr = \App\Models\ServiceRequest::with('comments.user')->find($serviceRequest->id);
            echo "✅ Eager loading test successful!\n";
            
        } catch (\Exception $e) {
            echo "❌ Relationship test failed: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n==========================================\n";
    echo "✅ Table structure fixed successfully!\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
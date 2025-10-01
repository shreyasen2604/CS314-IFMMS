<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

try {
    // Check if the table exists
    if (Schema::hasTable('support_comments')) {
        echo "Table 'support_comments' already exists.\n";
        
        // Check if all required columns exist
        $columns = Schema::getColumnListing('support_comments');
        $requiredColumns = ['id', 'commentable_type', 'commentable_id', 'user_id', 'comment', 'is_internal', 'attachments', 'created_at', 'updated_at'];
        
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "All required columns exist.\n";
        } else {
            echo "Missing columns: " . implode(', ', $missingColumns) . "\n";
            
            // Add missing columns
            Schema::table('support_comments', function (Blueprint $table) use ($missingColumns) {
                if (in_array('commentable_type', $missingColumns) || in_array('commentable_id', $missingColumns)) {
                    $table->morphs('commentable');
                }
                if (in_array('user_id', $missingColumns)) {
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                }
                if (in_array('comment', $missingColumns)) {
                    $table->text('comment');
                }
                if (in_array('is_internal', $missingColumns)) {
                    $table->boolean('is_internal')->default(false);
                }
                if (in_array('attachments', $missingColumns)) {
                    $table->json('attachments')->nullable();
                }
            });
            
            echo "Missing columns added.\n";
        }
        
        // Mark migration as run if not already
        $migrationName = '2024_01_15_create_support_comments_table';
        $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
        
        if (!$exists) {
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            echo "Migration marked as run.\n";
        }
        
    } else {
        echo "Creating 'support_comments' table...\n";
        
        // Create the table
        Schema::create('support_comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable'); // Can be used for both service_requests and incident_reports
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->boolean('is_internal')->default(false); // Internal notes vs customer visible
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index(['commentable_type', 'commentable_id']);
        });
        
        echo "Table 'support_comments' created successfully.\n";
        
        // Mark migration as run
        $migrationName = '2024_01_15_create_support_comments_table';
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "Migration marked as run.\n";
    }
    
    echo "\n✅ Support comments table is ready!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
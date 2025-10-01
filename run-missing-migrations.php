<?php
/**
 * Run only the missing migrations without deleting existing data
 */

echo "\n========================================\n";
echo "RUNNING MISSING MIGRATIONS\n";
echo "========================================\n\n";

// First, try to run remaining migrations normally
echo "Attempting to run pending migrations...\n\n";
$output = shell_exec('php artisan migrate 2>&1');
echo $output . "\n";

// Check if it worked
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Check if tables were created
$stillMissing = [];
$nowExists = [];

$checkTables = ['incidents', 'incident_updates', 'messages', 'announcements', 'notifications', 'user_preferences'];

foreach ($checkTables as $table) {
    if (Schema::hasTable($table)) {
        $nowExists[] = $table;
        echo "✅ Table '$table' now exists\n";
    } else {
        $stillMissing[] = $table;
        echo "❌ Table '$table' still missing\n";
    }
}

if (count($stillMissing) > 0) {
    echo "\n⚠️  Some tables are still missing. Attempting manual migration...\n\n";
    
    // Try to run specific migration files
    $migrationFiles = [
        '2025_08_16_083422_create_incidents_table.php',
        '2025_08_16_100024_create_incident_updates_table.php',
        '2025_01_01_000001_create_messages_table.php',
        '2025_01_01_000002_create_announcements_table.php',
        '2025_01_01_000003_create_notifications_table.php',
        '2025_01_01_000004_create_user_preferences_table.php',
        '2025_01_20_000001_add_vehicle_id_and_profile_picture_to_users_table.php'
    ];
    
    foreach ($migrationFiles as $file) {
        $path = database_path('migrations/' . $file);
        if (file_exists($path)) {
            echo "Found migration file: $file\n";
            
            // Try to run this specific migration
            $className = 'Create' . str_replace(['_', 'table'], '', ucwords(str_replace(['create_', '_table.php', '.php'], '', $file), '_'));
            
            try {
                // Include and run the migration
                include_once $path;
                
                // Get the migration class
                $migration = include $path;
                if (is_object($migration)) {
                    echo "Running migration: $file\n";
                    $migration->up();
                    
                    // Record in migrations table
                    DB::table('migrations')->insert([
                        'migration' => str_replace('.php', '', $file),
                        'batch' => DB::table('migrations')->max('batch') + 1
                    ]);
                    
                    echo "✅ Migration completed: $file\n\n";
                }
            } catch (Exception $e) {
                echo "⚠️  Could not run $file: " . $e->getMessage() . "\n\n";
            }
        }
    }
}

// Final check
echo "\n========================================\n";
echo "FINAL STATUS:\n";
echo "========================================\n\n";

$allGood = true;
foreach ($checkTables as $table) {
    if (Schema::hasTable($table)) {
        echo "✅ $table - EXISTS\n";
    } else {
        echo "❌ $table - STILL MISSING\n";
        $allGood = false;
    }
}

if ($allGood) {
    echo "\n✅ SUCCESS! All tables have been created.\n";
    echo "\nNow run: php artisan db:seed\n";
    echo "To create default users and data.\n";
} else {
    echo "\n⚠️  Some tables could not be created.\n";
    echo "\nYou may need to run:\n";
    echo "php artisan migrate:fresh --seed --force\n";
    echo "(This will delete existing data but ensure all tables are created)\n";
}

echo "\n";
<?php
/**
 * Emergency Database Fix
 * Run this if migrations are not working
 */

echo "\n========================================\n";
echo "EMERGENCY DATABASE FIX\n";
echo "========================================\n\n";

// Check if we can run artisan commands
echo "Attempting to run migrations...\n";
$output = shell_exec('php artisan migrate --force 2>&1');
echo $output . "\n";

// If migrations failed, try to create tables manually
if (strpos($output, 'Nothing to migrate') === false && strpos($output, 'Migrated') === false) {
    echo "\nMigrations may have failed. Attempting alternative fix...\n\n";
    
    // Load Laravel
    require 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    
    try {
        // Check database connection
        DB::connection()->getPdo();
        echo "✅ Database connection successful\n\n";
        
        // Check if incidents table exists
        if (!Schema::hasTable('incidents')) {
            echo "❌ Incidents table does not exist\n";
            echo "Attempting fresh migration with force...\n\n";
            
            // Try fresh migration
            $freshOutput = shell_exec('php artisan migrate:fresh --seed --force 2>&1');
            echo $freshOutput . "\n";
            
            // Verify table creation
            if (Schema::hasTable('incidents')) {
                echo "\n✅ Incidents table created successfully!\n";
            } else {
                echo "\n❌ Failed to create incidents table\n";
                echo "Please run manually: php artisan migrate:fresh --seed\n";
            }
        } else {
            echo "✅ Incidents table already exists\n";
        }
        
        // Check other critical tables
        $tables = ['users', 'vehicles', 'incidents', 'maintenance_records', 'messages'];
        echo "\nChecking critical tables:\n";
        echo "------------------------\n";
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                echo "✅ Table '$table' exists\n";
            } else {
                echo "❌ Table '$table' missing\n";
            }
        }
        
        // Check if users exist
        echo "\nChecking users:\n";
        echo "------------------------\n";
        $userCount = DB::table('users')->count();
        echo "Total users: $userCount\n";
        
        if ($userCount == 0) {
            echo "Creating default users...\n";
            shell_exec('php artisan db:seed --class=UserSeeder --force 2>&1');
            echo "✅ Default users created\n";
        }
        
        // Display users
        $users = DB::table('users')->select('email', 'role')->get();
        foreach ($users as $user) {
            echo "  - {$user->email} ({$user->role})\n";
        }
        
    } catch (Exception $e) {
        echo "\n❌ Database Error: " . $e->getMessage() . "\n\n";
        echo "Please check:\n";
        echo "1. MySQL is running\n";
        echo "2. Database 'ifmms_zar' exists\n";
        echo "3. .env file has correct database credentials\n\n";
        echo "To create database manually:\n";
        echo "  mysql -u root -p\n";
        echo "  CREATE DATABASE ifmms_zar;\n";
        echo "  exit;\n";
    }
}

echo "\n========================================\n";
echo "Fix attempt complete!\n";
echo "========================================\n\n";
echo "Try accessing the application now.\n";
echo "If you still see errors, run:\n";
echo "  php artisan migrate:fresh --seed\n\n";
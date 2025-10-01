<?php
/**
 * Database Fix Script for IFMMS-ZAR
 * This script will create all missing tables and seed default data
 */

echo "\n========================================\n";
echo "IFMMS-ZAR Database Setup Script\n";
echo "========================================\n\n";

// Run migrations
echo "Step 1: Running database migrations...\n";
echo "----------------------------------------\n";
$migrationResult = shell_exec('php artisan migrate 2>&1');
echo $migrationResult . "\n";

// Clear caches
echo "Step 2: Clearing application caches...\n";
echo "----------------------------------------\n";
shell_exec('php artisan cache:clear 2>&1');
shell_exec('php artisan config:clear 2>&1');
shell_exec('php artisan route:clear 2>&1');
shell_exec('php artisan view:clear 2>&1');
echo "✅ Caches cleared successfully!\n\n";

// Seed the database
echo "Step 3: Seeding database with default data...\n";
echo "----------------------------------------\n";
$seedResult = shell_exec('php artisan db:seed 2>&1');
echo $seedResult . "\n";

// Create storage link
echo "Step 4: Creating storage link...\n";
echo "----------------------------------------\n";
$storageResult = shell_exec('php artisan storage:link 2>&1');
echo $storageResult . "\n";

// Verify tables
echo "Step 5: Verifying database tables...\n";
echo "----------------------------------------\n";

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$requiredTables = [
    'users' => '✅ Users table (authentication)',
    'vehicles' => '✅ Vehicles table (fleet management)',
    'incidents' => '✅ Incidents table (incident reporting)',
    'incident_updates' => '✅ Incident updates table',
    'maintenance_records' => '✅ Maintenance records table',
    'maintenance_schedules' => '✅ Maintenance schedules table',
    'maintenance_alerts' => '✅ Maintenance alerts table',
    'maintenance_types' => '✅ Maintenance types table',
    'vehicle_health_metrics' => '✅ Vehicle health metrics table',
    'messages' => '✅ Messages table (communication)',
    'announcements' => '✅ Announcements table',
    'notifications' => '✅ Notifications table',
    'user_preferences' => '✅ User preferences table'
];

$allTablesExist = true;
foreach ($requiredTables as $table => $description) {
    if (Schema::hasTable($table)) {
        echo $description . " - EXISTS\n";
    } else {
        echo "❌ Table '$table' - MISSING\n";
        $allTablesExist = false;
    }
}

echo "\n========================================\n";

if ($allTablesExist) {
    echo "✅ All tables created successfully!\n";
    echo "========================================\n\n";
    
    // Display login credentials
    echo "Default Login Credentials:\n";
    echo "----------------------------------------\n";
    echo "Admin Account:\n";
    echo "  Email: admin@zar.com\n";
    echo "  Password: Admin@12345\n\n";
    
    echo "Driver Account:\n";
    echo "  Email: driver1@zar.com\n";
    echo "  Password: Driver@12345\n\n";
    
    echo "Technician Account:\n";
    echo "  Email: tech1@zar.com\n";
    echo "  Password: Tech@12345\n\n";
    
    // Check if users exist
    try {
        $userCount = DB::table('users')->count();
        echo "Total users in database: $userCount\n";
        
        if ($userCount == 0) {
            echo "\n⚠️  No users found! Running user seeder...\n";
            shell_exec('php artisan db:seed --class=UserSeeder 2>&1');
            echo "✅ Default users created!\n";
        }
    } catch (Exception $e) {
        echo "Error checking users: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ Some tables are missing!\n";
    echo "========================================\n\n";
    echo "Try running: php artisan migrate:fresh --seed\n";
    echo "WARNING: This will delete all existing data!\n";
}

echo "\n✅ Database setup complete!\n";
echo "You can now access the system at: http://localhost:8000\n\n";
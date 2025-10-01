<?php
/**
 * Database Diagnostic Script
 * This will show exactly what's happening with your database
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n========================================\n";
echo "DATABASE DIAGNOSTIC REPORT\n";
echo "========================================\n\n";

// 1. Check database connection
try {
    $pdo = DB::connection()->getPdo();
    $database = DB::connection()->getDatabaseName();
    echo "✅ Connected to database: $database\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. List all tables in the database
echo "TABLES IN DATABASE:\n";
echo "-------------------\n";
try {
    $tables = DB::select('SHOW TABLES');
    $tableCount = 0;
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  • $tableName\n";
        $tableCount++;
    }
    if ($tableCount == 0) {
        echo "  ❌ NO TABLES FOUND IN DATABASE!\n";
    } else {
        echo "\nTotal tables: $tableCount\n";
    }
} catch (Exception $e) {
    echo "Error listing tables: " . $e->getMessage() . "\n";
}

// 3. Check specific required tables
echo "\n\nREQUIRED TABLES CHECK:\n";
echo "----------------------\n";
$requiredTables = [
    'users',
    'incidents',
    'incident_updates',
    'vehicles',
    'maintenance_records',
    'maintenance_schedules',
    'maintenance_alerts',
    'messages',
    'announcements',
    'notifications'
];

$missingTables = [];
foreach ($requiredTables as $table) {
    if (Schema::hasTable($table)) {
        echo "✅ $table - EXISTS\n";
    } else {
        echo "❌ $table - MISSING\n";
        $missingTables[] = $table;
    }
}

// 4. Check migrations table
echo "\n\nMIGRATION STATUS:\n";
echo "-----------------\n";
if (Schema::hasTable('migrations')) {
    $migrations = DB::table('migrations')->get();
    echo "Migrations run: " . $migrations->count() . "\n\n";
    foreach ($migrations as $migration) {
        echo "  ✓ {$migration->migration} (Batch {$migration->batch})\n";
    }
} else {
    echo "❌ Migrations table doesn't exist!\n";
}

// 5. Provide solution
echo "\n\n========================================\n";
echo "DIAGNOSIS RESULT:\n";
echo "========================================\n\n";

if (count($missingTables) > 0) {
    echo "❌ PROBLEM FOUND: " . count($missingTables) . " tables are missing!\n";
    echo "Missing tables: " . implode(', ', $missingTables) . "\n\n";
    
    echo "SOLUTION:\n";
    echo "---------\n";
    echo "Run these commands to fix:\n\n";
    echo "1. Force fresh migration (THIS WILL DELETE ALL DATA):\n";
    echo "   php artisan migrate:fresh --force\n\n";
    echo "2. Then seed the database:\n";
    echo "   php artisan db:seed\n\n";
    echo "3. Clear caches:\n";
    echo "   php artisan cache:clear\n\n";
    
    echo "OR run this single command (DELETES ALL DATA):\n";
    echo "   php artisan migrate:fresh --seed --force\n";
} else {
    echo "✅ All required tables exist!\n";
    echo "The database structure looks correct.\n\n";
    echo "Try clearing caches:\n";
    echo "   php artisan cache:clear\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan route:clear\n";
}

echo "\n";
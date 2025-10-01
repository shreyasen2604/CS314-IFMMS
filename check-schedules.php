<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use App\Models\User;

echo "=== CHECKING MAINTENANCE SCHEDULES ===\n\n";

// Check total schedules
$totalSchedules = MaintenanceSchedule::count();
echo "Total schedules in database: {$totalSchedules}\n\n";

// List all schedules
if ($totalSchedules > 0) {
    echo "Existing schedules:\n";
    echo "-------------------\n";
    $schedules = MaintenanceSchedule::with(['vehicle', 'technician'])->get();
    
    foreach ($schedules as $schedule) {
        echo "Schedule ID: {$schedule->id}\n";
        echo "  Vehicle: " . ($schedule->vehicle ? $schedule->vehicle->vehicle_number : 'N/A') . "\n";
        echo "  Type: {$schedule->maintenance_type}\n";
        echo "  Date: {$schedule->scheduled_date}\n";
        echo "  Status: {$schedule->status}\n";
        echo "  Priority: {$schedule->priority}\n";
        echo "  Technician: " . ($schedule->technician ? $schedule->technician->name : 'Unassigned') . "\n";
        echo "  Created by: {$schedule->created_by}\n";
        echo "-------------------\n";
    }
} else {
    echo "No schedules found in database.\n\n";
}

// Check vehicles
echo "\n=== CHECKING VEHICLES ===\n";
$vehicleCount = Vehicle::count();
echo "Total vehicles: {$vehicleCount}\n";

if ($vehicleCount > 0) {
    echo "\nAvailable vehicles:\n";
    Vehicle::all()->each(function($v) {
        echo "  - ID: {$v->id}, Number: {$v->vehicle_number}, Status: {$v->status}\n";
    });
} else {
    echo "No vehicles found!\n";
}

// Check users
echo "\n=== CHECKING USERS ===\n";
$adminCount = User::where('role', 'Admin')->count();
$techCount = User::where('role', 'Technician')->count();
echo "Admin users: {$adminCount}\n";
echo "Technician users: {$techCount}\n";

// Check if maintenance_schedules table exists
echo "\n=== CHECKING DATABASE TABLE ===\n";
try {
    $tableExists = \DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='maintenance_schedules'");
    if ($tableExists) {
        echo "✅ maintenance_schedules table exists\n";
        
        // Check table structure
        $columns = \DB::select("PRAGMA table_info(maintenance_schedules)");
        echo "\nTable columns:\n";
        foreach ($columns as $column) {
            echo "  - {$column->name} ({$column->type})\n";
        }
    } else {
        echo "❌ maintenance_schedules table does NOT exist!\n";
    }
} catch (\Exception $e) {
    echo "Error checking table: " . $e->getMessage() . "\n";
}

echo "\n=== END OF CHECK ===\n";
<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use App\Models\User;
use Carbon\Carbon;

try {
    // Get first vehicle
    $vehicle = Vehicle::first();
    if (!$vehicle) {
        echo "No vehicles found in database. Creating a test vehicle...\n";
        $vehicle = Vehicle::create([
            'vehicle_number' => 'TEST-001',
            'make' => 'Toyota',
            'model' => 'Camry',
            'year' => 2022,
            'status' => 'active',
            'mileage' => 10000,
            'fuel_type' => 'Petrol',
            'transmission' => 'Automatic'
        ]);
        echo "Created vehicle: {$vehicle->vehicle_number}\n";
    } else {
        echo "Using existing vehicle: {$vehicle->vehicle_number}\n";
    }

    // Get admin user
    $admin = User::where('role', 'Admin')->first();
    if (!$admin) {
        echo "No admin user found!\n";
        exit;
    }
    echo "Using admin user: {$admin->name}\n";

    // Get technician (optional)
    $technician = User::where('role', 'Technician')->first();
    
    // Create a test maintenance schedule
    $schedule = MaintenanceSchedule::create([
        'vehicle_id' => $vehicle->id,
        'technician_id' => $technician ? $technician->id : null,
        'maintenance_type' => 'oil_change',
        'scheduled_date' => Carbon::now()->addDays(3),
        'estimated_duration' => 60,
        'priority' => 'medium',
        'status' => 'scheduled',
        'description' => 'Test maintenance schedule created via script',
        'created_by' => $admin->id
    ]);

    echo "\n✅ Successfully created maintenance schedule!\n";
    echo "Schedule ID: {$schedule->id}\n";
    echo "Vehicle: {$vehicle->vehicle_number}\n";
    echo "Type: {$schedule->maintenance_type}\n";
    echo "Date: {$schedule->scheduled_date}\n";
    echo "Status: {$schedule->status}\n";
    echo "Priority: {$schedule->priority}\n";
    
    // Verify it was saved
    $count = MaintenanceSchedule::count();
    echo "\nTotal schedules in database: {$count}\n";
    
    echo "\n📌 Now check http://127.0.0.1:8000/maintenance/schedule to see if it appears.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
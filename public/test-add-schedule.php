<?php
// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use App\Models\User;
use Carbon\Carbon;

// Get first vehicle and technician
$vehicle = Vehicle::first();
$technician = User::where('role', 'Technician')->first();
$admin = User::where('role', 'Admin')->first();

if ($vehicle && $admin) {
    try {
        $schedule = MaintenanceSchedule::create([
            'vehicle_id' => $vehicle->id,
            'technician_id' => $technician ? $technician->id : null,
            'maintenance_type' => 'routine',
            'scheduled_date' => Carbon::now()->addDays(7),
            'estimated_duration' => 90,
            'priority' => 'high',
            'status' => 'scheduled',
            'description' => 'Test schedule added at ' . now()->format('Y-m-d H:i:s'),
            'created_by' => $admin->id
        ]);
        
        header('Location: /test-schedules.php?success=1');
    } catch (Exception $e) {
        header('Location: /test-schedules.php?error=' . urlencode($e->getMessage()));
    }
} else {
    header('Location: /test-schedules.php?error=' . urlencode('No vehicle or admin user found'));
}
?>
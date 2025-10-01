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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Maintenance Schedule Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: green; }
        .error { color: red; }
        .info { background-color: #e7f3ff; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Maintenance Schedule Diagnostic Page</h1>
    
    <div class="info">
        <h2>Database Status</h2>
        <?php
        try {
            $scheduleCount = MaintenanceSchedule::count();
            echo "<p class='success'>✅ Database connection successful</p>";
            echo "<p><strong>Total schedules in database: {$scheduleCount}</strong></p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <h2>All Maintenance Schedules</h2>
    <?php
    try {
        $schedules = MaintenanceSchedule::with(['vehicle', 'technician'])->get();
        
        if ($schedules->count() > 0) {
            ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Type</th>
                        <th>Scheduled Date</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Technician</th>
                        <th>Duration</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= $schedule->id ?></td>
                        <td><?= $schedule->vehicle ? $schedule->vehicle->vehicle_number : 'N/A' ?></td>
                        <td><?= $schedule->maintenance_type ?></td>
                        <td><?= $schedule->scheduled_date ?></td>
                        <td><?= $schedule->status ?></td>
                        <td><?= $schedule->priority ?></td>
                        <td><?= $schedule->technician ? $schedule->technician->name : 'Unassigned' ?></td>
                        <td><?= $schedule->estimated_duration ?> min</td>
                        <td><?= $schedule->description ?: 'No description' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        } else {
            echo "<p>No schedules found in the database.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>Error loading schedules: " . $e->getMessage() . "</p>";
    }
    ?>

    <h2>Available Vehicles</h2>
    <?php
    try {
        $vehicles = Vehicle::all();
        if ($vehicles->count() > 0) {
            ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle Number</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td><?= $vehicle->id ?></td>
                        <td><?= $vehicle->vehicle_number ?></td>
                        <td><?= $vehicle->make ?></td>
                        <td><?= $vehicle->model ?></td>
                        <td><?= $vehicle->status ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        } else {
            echo "<p>No vehicles found.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>Error loading vehicles: " . $e->getMessage() . "</p>";
    }
    ?>

    <h2>Available Technicians</h2>
    <?php
    try {
        $technicians = User::where('role', 'Technician')->get();
        if ($technicians->count() > 0) {
            ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($technicians as $tech): ?>
                    <tr>
                        <td><?= $tech->id ?></td>
                        <td><?= $tech->name ?></td>
                        <td><?= $tech->email ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        } else {
            echo "<p>No technicians found.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>Error loading technicians: " . $e->getMessage() . "</p>";
    }
    ?>

    <div class="info">
        <h3>Quick Actions</h3>
        <p>
            <a href="/maintenance/schedule">Go to Maintenance Schedule Page</a> | 
            <a href="/admin">Go to Admin Dashboard</a>
        </p>
    </div>

    <div class="info">
        <h3>Add Test Schedule via Form</h3>
        <form method="POST" action="/test-add-schedule.php">
            <button type="submit">Add Test Schedule</button>
        </form>
    </div>
</body>
</html>
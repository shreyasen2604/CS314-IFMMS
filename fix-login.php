<?php
/**
 * Quick Fix Script for Login Issues
 * Run this file to restore default user accounts
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "\n========================================\n";
echo "IFMMS-ZAR Login Fix Script\n";
echo "========================================\n\n";

try {
    // Create or update admin user
    $admin = User::updateOrCreate(
        ['email' => 'admin@zar.com'],
        [
            'name' => 'System Admin',
            'password' => bcrypt('Admin@12345'),
            'role' => 'Admin'
        ]
    );
    echo "✅ Admin user created/updated successfully!\n";
    echo "   Email: admin@zar.com\n";
    echo "   Password: Admin@12345\n\n";

    // Create or update driver user
    $driver = User::updateOrCreate(
        ['email' => 'driver1@zar.com'],
        [
            'name' => 'John Driver',
            'password' => bcrypt('Driver@12345'),
            'role' => 'Driver'
        ]
    );
    echo "✅ Driver user created/updated successfully!\n";
    echo "   Email: driver1@zar.com\n";
    echo "   Password: Driver@12345\n\n";

    // Create or update technician user
    $technician = User::updateOrCreate(
        ['email' => 'tech1@zar.com'],
        [
            'name' => 'Tina Technician',
            'password' => bcrypt('Tech@12345'),
            'role' => 'Technician'
        ]
    );
    echo "✅ Technician user created/updated successfully!\n";
    echo "   Email: tech1@zar.com\n";
    echo "   Password: Tech@12345\n\n";

    // Display all users
    echo "========================================\n";
    echo "All Users in Database:\n";
    echo "========================================\n";
    
    $allUsers = User::select('id', 'name', 'email', 'role')->get();
    foreach ($allUsers as $user) {
        echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$user->role}\n";
    }
    
    echo "\n========================================\n";
    echo "✅ Login fix completed successfully!\n";
    echo "========================================\n";
    echo "\nYou can now login with any of the credentials above.\n";
    echo "If you still have issues, try clearing cache:\n";
    echo "  php artisan cache:clear\n";
    echo "  php artisan config:clear\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nPlease ensure:\n";
    echo "1. Database is running\n";
    echo "2. .env file has correct database credentials\n";
    echo "3. Database 'ifmms_zar' exists\n";
    echo "4. Migrations have been run: php artisan migrate\n";
}
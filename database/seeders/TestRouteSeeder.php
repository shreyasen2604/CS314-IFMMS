<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\RouteCheckpoint;
use App\Models\User;

class TestRouteSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get the first admin user
        $admin = User::where('role', 'Admin')->first();
        
        if (!$admin) {
            echo "No admin user found. Creating one...\n";
            $admin = User::create([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'Admin'
            ]);
        }

        // Create a test route
        $route = Route::create([
            'route_name' => 'Test Delivery Route',
            'route_code' => 'TEST001',
            'description' => 'A test route for delivery services',
            'start_location' => 'Main Warehouse, 123 Industrial Ave',
            'end_location' => 'Customer District, 456 Commercial St',
            'route_type' => 'delivery',
            'priority' => 'medium',
            'status' => 'active',
            'total_distance' => 15.5,
            'estimated_duration' => 90,
            'schedule_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'fuel_cost_estimate' => 25.50,
            'special_instructions' => 'Handle with care. Contact customer before delivery.',
            'waypoints' => [],
            'created_by' => $admin->id
        ]);

        // Create checkpoints for the route
        $checkpoints = [
            [
                'checkpoint_name' => 'Pickup Point A',
                'address' => '789 Supplier Road, Industrial Zone',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'checkpoint_type' => 'pickup',
                'estimated_duration' => 15,
                'sequence_order' => 1,
                'is_mandatory' => true,
                'contact_info' => '+1-555-0101'
            ],
            [
                'checkpoint_name' => 'Rest Stop',
                'address' => '321 Highway Service Plaza',
                'latitude' => 40.7589,
                'longitude' => -73.9851,
                'checkpoint_type' => 'rest_stop',
                'estimated_duration' => 10,
                'sequence_order' => 2,
                'is_mandatory' => false,
                'contact_info' => null
            ],
            [
                'checkpoint_name' => 'Customer Delivery',
                'address' => '456 Commercial St, Business District',
                'latitude' => 40.7831,
                'longitude' => -73.9712,
                'checkpoint_type' => 'delivery',
                'estimated_duration' => 20,
                'sequence_order' => 3,
                'is_mandatory' => true,
                'contact_info' => '+1-555-0202'
            ]
        ];

        foreach ($checkpoints as $checkpointData) {
            RouteCheckpoint::create(array_merge($checkpointData, [
                'route_id' => $route->id
            ]));
        }

        echo "Test route created successfully with ID: {$route->id}\n";
        echo "Route has " . $route->checkpoints()->count() . " checkpoints\n";
    }
}
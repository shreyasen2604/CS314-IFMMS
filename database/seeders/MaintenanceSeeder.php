<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceAlert;
use App\Models\User;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample vehicles
        $vehicles = [
            [
                'vehicle_number' => 'ZAR-001',
                'make' => 'Mercedes',
                'model' => 'Actros',
                'year' => 2020,
                'vin' => 'WDB9634321L123456',
                'license_plate' => 'ABC-123-GP',
                'mileage' => 85000,
                'fuel_type' => 'diesel',
                'status' => 'active',
                'last_maintenance_date' => Carbon::now()->subDays(45),
                'next_maintenance_due' => Carbon::now()->addDays(15),
                'health_score' => 85.5,
            ],
            [
                'vehicle_number' => 'ZAR-002',
                'make' => 'Volvo',
                'model' => 'FH16',
                'year' => 2019,
                'vin' => 'YV2A4D0B5KA123456',
                'license_plate' => 'DEF-456-GP',
                'mileage' => 120000,
                'fuel_type' => 'diesel',
                'status' => 'active',
                'last_maintenance_date' => Carbon::now()->subDays(75),
                'next_maintenance_due' => Carbon::now()->subDays(5), // Overdue
                'health_score' => 65.2,
            ],
            [
                'vehicle_number' => 'ZAR-003',
                'make' => 'Scania',
                'model' => 'R450',
                'year' => 2021,
                'vin' => 'YS2R4X20005123456',
                'license_plate' => 'GHI-789-GP',
                'mileage' => 45000,
                'fuel_type' => 'diesel',
                'status' => 'maintenance',
                'last_maintenance_date' => Carbon::now()->subDays(10),
                'next_maintenance_due' => Carbon::now()->addDays(80),
                'health_score' => 92.8,
            ],
            [
                'vehicle_number' => 'ZAR-004',
                'make' => 'MAN',
                'model' => 'TGX',
                'year' => 2018,
                'vin' => 'WMA06XZZ4JM123456',
                'license_plate' => 'JKL-012-GP',
                'mileage' => 180000,
                'fuel_type' => 'diesel',
                'status' => 'active',
                'last_maintenance_date' => Carbon::now()->subDays(120),
                'next_maintenance_due' => Carbon::now()->subDays(30), // Overdue
                'health_score' => 35.7,
            ],
            [
                'vehicle_number' => 'ZAR-005',
                'make' => 'DAF',
                'model' => 'XF',
                'year' => 2022,
                'vin' => 'XLRTE39M0E0123456',
                'license_plate' => 'MNO-345-GP',
                'mileage' => 25000,
                'fuel_type' => 'diesel',
                'status' => 'active',
                'last_maintenance_date' => Carbon::now()->subDays(20),
                'next_maintenance_due' => Carbon::now()->addDays(70),
                'health_score' => 96.3,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }

        // Get created vehicles and users
        $createdVehicles = Vehicle::all();
        $technicians = User::where('role', 'Technician')->get();
        $admin = User::where('role', 'Admin')->first();

        // Create sample maintenance records
        $maintenanceTypes = ['routine', 'preventive', 'corrective', 'oil_change', 'tire_rotation', 'brake_service'];
        
        foreach ($createdVehicles as $vehicle) {
            // Create 3-5 historical maintenance records per vehicle
            for ($i = 0; $i < rand(3, 5); $i++) {
                MaintenanceRecord::create([
                    'vehicle_id' => $vehicle->id,
                    'technician_id' => $technicians->random()->id ?? null,
                    'maintenance_type' => $maintenanceTypes[array_rand($maintenanceTypes)],
                    'description' => 'Routine maintenance service including inspection and repairs',
                    'cost' => rand(500, 3000),
                    'parts_cost' => rand(200, 1500),
                    'labor_cost' => rand(300, 1500),
                    'mileage_at_service' => $vehicle->mileage - rand(5000, 25000),
                    'service_date' => Carbon::now()->subDays(rand(30, 365)),
                    'completion_date' => Carbon::now()->subDays(rand(25, 360)),
                    'status' => 'completed',
                    'notes' => 'Service completed successfully. All systems functioning normally.',
                    'next_service_mileage' => $vehicle->mileage + rand(5000, 10000),
                    'next_service_date' => Carbon::now()->addDays(rand(60, 120)),
                ]);
            }
        }

        // Create sample maintenance schedules
        foreach ($createdVehicles as $vehicle) {
            // Create 1-2 upcoming schedules per vehicle
            for ($i = 0; $i < rand(1, 2); $i++) {
                MaintenanceSchedule::create([
                    'vehicle_id' => $vehicle->id,
                    'technician_id' => $technicians->random()->id ?? null,
                    'maintenance_type' => $maintenanceTypes[array_rand($maintenanceTypes)],
                    'scheduled_date' => Carbon::now()->addDays(rand(1, 30))->addHours(rand(8, 17)),
                    'estimated_duration' => rand(60, 480), // 1-8 hours
                    'priority' => ['low', 'medium', 'high', 'critical'][array_rand(['low', 'medium', 'high', 'critical'])],
                    'status' => ['scheduled', 'confirmed'][array_rand(['scheduled', 'confirmed'])],
                    'description' => 'Scheduled maintenance service',
                    'recurring' => rand(0, 1),
                    'recurring_interval' => rand(0, 1) ? rand(1, 6) : null,
                    'recurring_type' => rand(0, 1) ? ['weekly', 'monthly', 'yearly'][array_rand(['weekly', 'monthly', 'yearly'])] : null,
                    'created_by' => $admin->id ?? 1,
                ]);
            }
        }

        // Create sample maintenance alerts
        $alertTypes = ['mileage_due', 'time_due', 'health_score', 'breakdown_risk', 'overdue'];
        $severities = ['low', 'medium', 'high', 'critical'];

        foreach ($createdVehicles as $vehicle) {
            // Create 0-3 alerts per vehicle
            for ($i = 0; $i < rand(0, 3); $i++) {
                $alertType = $alertTypes[array_rand($alertTypes)];
                $severity = $severities[array_rand($severities)];
                
                $alertData = [
                    'vehicle_id' => $vehicle->id,
                    'alert_type' => $alertType,
                    'severity' => $severity,
                    'status' => ['active', 'acknowledged'][array_rand(['active', 'acknowledged'])],
                ];

                switch ($alertType) {
                    case 'mileage_due':
                        $alertData['title'] = 'Maintenance Due - Mileage';
                        $alertData['message'] = "Vehicle {$vehicle->vehicle_number} has reached maintenance mileage threshold";
                        $alertData['threshold_value'] = 5000;
                        $alertData['current_value'] = $vehicle->mileage;
                        break;
                    case 'time_due':
                        $alertData['title'] = 'Maintenance Due - Time';
                        $alertData['message'] = "Vehicle {$vehicle->vehicle_number} maintenance is due based on time interval";
                        break;
                    case 'health_score':
                        $alertData['title'] = 'Low Health Score';
                        $alertData['message'] = "Vehicle {$vehicle->vehicle_number} health score has dropped below threshold";
                        $alertData['threshold_value'] = 60;
                        $alertData['current_value'] = $vehicle->health_score;
                        break;
                    case 'breakdown_risk':
                        $alertData['title'] = 'High Breakdown Risk';
                        $alertData['message'] = "Vehicle {$vehicle->vehicle_number} shows high risk of breakdown";
                        break;
                    case 'overdue':
                        $alertData['title'] = 'Overdue Maintenance';
                        $alertData['message'] = "Vehicle {$vehicle->vehicle_number} has overdue maintenance";
                        break;
                }

                if ($alertData['status'] === 'acknowledged') {
                    $alertData['acknowledged_by'] = $admin->id ?? 1;
                    $alertData['acknowledged_at'] = Carbon::now()->subDays(rand(1, 5));
                }

                MaintenanceAlert::create($alertData);
            }
        }
    }
}
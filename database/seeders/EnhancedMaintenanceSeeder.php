<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceAlert;
use App\Models\VehicleHealthMetric;
use App\Models\User;
use Carbon\Carbon;

class EnhancedMaintenanceSeeder extends Seeder
{
    public function run()
    {
        // Create Maintenance Types
        $this->createMaintenanceTypes();
        
        // Create Vehicle Health Metrics
        $this->createVehicleHealthMetrics();
        
        // Create Enhanced Maintenance Records
        $this->createEnhancedMaintenanceRecords();
        
        // Create Enhanced Maintenance Schedules
        $this->createEnhancedMaintenanceSchedules();
        
        // Create Enhanced Maintenance Alerts
        $this->createEnhancedMaintenanceAlerts();
        
        // Update vehicle health scores
        $this->updateVehicleHealthScores();
    }

    private function createMaintenanceTypes()
    {
        $maintenanceTypes = [
            [
                'name' => 'Oil Change',
                'code' => 'OIL_CHANGE',
                'description' => 'Regular engine oil and filter replacement',
                'category' => 'fluids',
                'estimated_duration' => 60,
                'estimated_cost' => 75.00,
                'frequency_type' => 'mile',
                'frequency_value' => 5000,
                'is_preventive' => true,
                'priority_level' => 'medium',
                'required_skills' => ['basic_maintenance'],
                'required_parts' => ['engine_oil', 'oil_filter']
            ],
            [
                'name' => 'Brake Inspection',
                'code' => 'BRAKE_INSPECT',
                'description' => 'Comprehensive brake system inspection',
                'category' => 'brakes',
                'estimated_duration' => 90,
                'estimated_cost' => 150.00,
                'frequency_type' => 'month',
                'frequency_value' => 6,
                'is_preventive' => true,
                'priority_level' => 'high',
                'required_skills' => ['brake_systems'],
                'required_parts' => []
            ],
            [
                'name' => 'Engine Diagnostic',
                'code' => 'ENGINE_DIAG',
                'description' => 'Computer diagnostic scan and engine analysis',
                'category' => 'engine',
                'estimated_duration' => 120,
                'estimated_cost' => 200.00,
                'frequency_type' => 'month',
                'frequency_value' => 12,
                'is_preventive' => false,
                'priority_level' => 'high',
                'required_skills' => ['diagnostics', 'engine_systems'],
                'required_parts' => []
            ],
            [
                'name' => 'Tire Rotation',
                'code' => 'TIRE_ROTATION',
                'description' => 'Rotate tires for even wear distribution',
                'category' => 'tires',
                'estimated_duration' => 45,
                'estimated_cost' => 50.00,
                'frequency_type' => 'mile',
                'frequency_value' => 7500,
                'is_preventive' => true,
                'priority_level' => 'low',
                'required_skills' => ['basic_maintenance'],
                'required_parts' => []
            ],
            [
                'name' => 'Transmission Service',
                'code' => 'TRANS_SERVICE',
                'description' => 'Transmission fluid change and filter replacement',
                'category' => 'transmission',
                'estimated_duration' => 180,
                'estimated_cost' => 300.00,
                'frequency_type' => 'mile',
                'frequency_value' => 30000,
                'is_preventive' => true,
                'priority_level' => 'high',
                'required_skills' => ['transmission_systems'],
                'required_parts' => ['transmission_fluid', 'transmission_filter']
            ],
            [
                'name' => 'Emergency Repair',
                'code' => 'EMERGENCY',
                'description' => 'Urgent repair for breakdown or safety issue',
                'category' => 'general',
                'estimated_duration' => 240,
                'estimated_cost' => 500.00,
                'is_preventive' => false,
                'priority_level' => 'critical',
                'required_skills' => ['advanced_diagnostics', 'emergency_repair'],
                'required_parts' => []
            ]
        ];

        foreach ($maintenanceTypes as $type) {
            MaintenanceType::create($type);
        }
    }

    private function createVehicleHealthMetrics()
    {
        $vehicles = Vehicle::all();
        $metricTypes = [
            'engine_temperature' => ['unit' => '°C', 'min' => 80, 'max' => 105],
            'oil_pressure' => ['unit' => 'PSI', 'min' => 20, 'max' => 80],
            'brake_wear' => ['unit' => '%', 'min' => 20, 'max' => 100],
            'tire_pressure' => ['unit' => 'PSI', 'min' => 30, 'max' => 35],
            'battery_voltage' => ['unit' => 'V', 'min' => 12.0, 'max' => 14.4],
            'fuel_efficiency' => ['unit' => 'MPG', 'min' => 15, 'max' => 30]
        ];

        foreach ($vehicles as $vehicle) {
            // Create historical metrics for the past 90 days
            for ($i = 90; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                
                foreach ($metricTypes as $metricType => $config) {
                    // Generate realistic values with some variation
                    $baseValue = ($config['min'] + $config['max']) / 2;
                    $variation = ($config['max'] - $config['min']) * 0.2;
                    $value = $baseValue + (rand(-100, 100) / 100) * $variation;
                    
                    // Determine status based on thresholds
                    $status = 'normal';
                    if ($value < $config['min'] || $value > $config['max']) {
                        $status = 'critical';
                    } elseif ($value < $config['min'] * 1.1 || $value > $config['max'] * 0.9) {
                        $status = 'warning';
                    }

                    VehicleHealthMetric::create([
                        'vehicle_id' => $vehicle->id,
                        'metric_type' => $metricType,
                        'metric_value' => round($value, 2),
                        'unit' => $config['unit'],
                        'threshold_min' => $config['min'],
                        'threshold_max' => $config['max'],
                        'status' => $status,
                        'recorded_at' => $date,
                        'source' => rand(0, 1) ? 'sensor' : 'manual'
                    ]);
                }
            }
        }
    }

    private function createEnhancedMaintenanceRecords()
    {
        $vehicles = Vehicle::all();
        $technicians = User::where('role', 'Technician')->get();
        $maintenanceTypes = MaintenanceType::all();

        foreach ($vehicles as $vehicle) {
            // Create 5-10 historical maintenance records per vehicle
            $recordCount = rand(5, 10);
            
            for ($i = 0; $i < $recordCount; $i++) {
                $serviceDate = Carbon::now()->subDays(rand(30, 365));
                $maintenanceType = $maintenanceTypes->random();
                $technician = $technicians->random();
                
                $partsUsed = $this->generatePartsUsed($maintenanceType);
                $partsCost = array_sum(array_column($partsUsed, 'cost'));
                $laborHours = rand(1, 8);
                $laborCost = $laborHours * 75; // $75/hour
                $totalCost = $partsCost + $laborCost;

                MaintenanceRecord::create([
                    'vehicle_id' => $vehicle->id,
                    'technician_id' => $technician->id,
                    'work_order_number' => 'WO-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'maintenance_type' => $maintenanceType->code,
                    'maintenance_category' => $maintenanceType->is_preventive ? 'preventive' : 'corrective',
                    'service_date' => $serviceDate,
                    'description' => $maintenanceType->description,
                    'mileage_at_service' => $vehicle->mileage - rand(1000, 10000),
                    'parts_cost' => $partsCost,
                    'labor_cost' => $laborCost,
                    'total_cost' => $totalCost,
                    'parts_used' => $partsUsed,
                    'labor_hours' => $laborHours,
                    'status' => 'completed',
                    'completion_date' => $serviceDate->copy()->addHours($laborHours),
                    'next_service_date' => $this->calculateNextServiceDate($serviceDate, $maintenanceType),
                    'notes' => $this->generateMaintenanceNotes($maintenanceType),
                    'recommendations' => $this->generateRecommendations(),
                    'vehicle_condition_before' => rand(60, 85) / 10,
                    'vehicle_condition_after' => rand(85, 100) / 10,
                    'warranty_work' => rand(0, 10) < 2, // 20% chance
                    'warranty_expiry' => rand(0, 10) < 2 ? $serviceDate->copy()->addMonths(6) : null
                ]);
            }
        }
    }

    private function createEnhancedMaintenanceSchedules()
    {
        $vehicles = Vehicle::all();
        $technicians = User::where('role', 'Technician')->get();
        $maintenanceTypes = MaintenanceType::all();

        foreach ($vehicles as $vehicle) {
            // Create 2-5 future scheduled maintenance items per vehicle
            $scheduleCount = rand(2, 5);
            
            for ($i = 0; $i < $scheduleCount; $i++) {
                $scheduledDate = Carbon::now()->addDays(rand(1, 90));
                $maintenanceType = $maintenanceTypes->random();
                $technician = rand(0, 1) ? $technicians->random() : null;

                $schedule = MaintenanceSchedule::create([
                    'vehicle_id' => $vehicle->id,
                    'technician_id' => $technician?->id,
                    'maintenance_type' => $maintenanceType->code,
                    'scheduled_date' => $scheduledDate,
                    'estimated_duration' => $maintenanceType->estimated_duration,
                    'priority' => ['low', 'medium', 'high', 'critical'][rand(0, 3)],
                    'status' => ['scheduled', 'confirmed'][rand(0, 1)],
                    'description' => $maintenanceType->description,
                    'created_by' => User::where('role', 'Admin')->first()->id,
                    'estimated_cost' => $maintenanceType->estimated_cost,
                    'required_parts' => $maintenanceType->required_parts,
                    'is_recurring' => rand(0, 10) < 3, // 30% chance
                    'recurring_type' => rand(0, 10) < 3 ? ['monthly', 'quarterly'][rand(0, 1)] : null,
                    'recurring_interval' => rand(0, 10) < 3 ? rand(1, 3) : null
                ]);

                // Create some recurring schedules
                if ($schedule->is_recurring && $schedule->recurring_type) {
                    $this->createRecurringSchedules($schedule);
                }
            }
        }
    }

    private function createEnhancedMaintenanceAlerts()
    {
        $vehicles = Vehicle::all();
        $alertTypes = [
            'overdue_maintenance' => ['severity' => 'high', 'title' => 'Overdue Maintenance'],
            'low_health_score' => ['severity' => 'critical', 'title' => 'Low Vehicle Health Score'],
            'high_mileage' => ['severity' => 'medium', 'title' => 'High Mileage Alert'],
            'sensor_warning' => ['severity' => 'warning', 'title' => 'Sensor Warning'],
            'driver_request' => ['severity' => 'medium', 'title' => 'Driver Maintenance Request'],
            'cost_threshold' => ['severity' => 'high', 'title' => 'Cost Threshold Exceeded']
        ];

        foreach ($vehicles as $vehicle) {
            // Create 1-3 alerts per vehicle
            $alertCount = rand(1, 3);
            
            for ($i = 0; $i < $alertCount; $i++) {
                $alertType = array_rand($alertTypes);
                $alertConfig = $alertTypes[$alertType];
                
                $createdAt = Carbon::now()->subDays(rand(0, 30));
                $status = ['active', 'acknowledged', 'resolved'][rand(0, 2)];
                
                $alert = MaintenanceAlert::create([
                    'vehicle_id' => $vehicle->id,
                    'alert_type' => $alertType,
                    'severity' => $alertConfig['severity'],
                    'title' => $alertConfig['title'],
                    'message' => $this->generateAlertMessage($alertType, $vehicle),
                    'status' => $status,
                    'threshold_value' => $this->getThresholdValue($alertType),
                    'current_value' => $this->getCurrentValue($alertType, $vehicle),
                    'source' => ['system', 'manual', 'sensor'][rand(0, 2)],
                    'escalation_level' => rand(1, 3),
                    'auto_resolve' => rand(0, 10) < 3,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt
                ]);

                // Add acknowledgment/resolution data for non-active alerts
                if ($status !== 'active') {
                    $acknowledgedAt = $createdAt->copy()->addHours(rand(1, 24));
                    $alert->update([
                        'acknowledged_by' => User::where('role', 'Technician')->inRandomOrder()->first()->id,
                        'acknowledged_at' => $acknowledgedAt,
                        'acknowledgment_notes' => 'Alert acknowledged and being investigated.'
                    ]);

                    if ($status === 'resolved') {
                        $resolvedAt = $acknowledgedAt->copy()->addHours(rand(1, 48));
                        $alert->update([
                            'resolved_by' => User::where('role', 'Technician')->inRandomOrder()->first()->id,
                            'resolved_at' => $resolvedAt,
                            'resolution_notes' => 'Issue resolved through maintenance action.'
                        ]);
                    }
                }
            }
        }
    }

    private function updateVehicleHealthScores()
    {
        $vehicles = Vehicle::all();
        
        foreach ($vehicles as $vehicle) {
            $healthScore = VehicleHealthMetric::getAverageHealthScore($vehicle->id);
            $vehicle->update(['health_score' => $healthScore]);
        }
    }

    // Helper methods
    private function generatePartsUsed($maintenanceType)
    {
        $parts = [];
        
        if ($maintenanceType->required_parts) {
            foreach ($maintenanceType->required_parts as $part) {
                $parts[] = [
                    'name' => $part,
                    'quantity' => rand(1, 3),
                    'cost' => rand(20, 150)
                ];
            }
        }
        
        return $parts;
    }

    private function calculateNextServiceDate($serviceDate, $maintenanceType)
    {
        if (!$maintenanceType->frequency_type || !$maintenanceType->frequency_value) {
            return null;
        }

        switch ($maintenanceType->frequency_type) {
            case 'day':
                return $serviceDate->copy()->addDays($maintenanceType->frequency_value);
            case 'week':
                return $serviceDate->copy()->addWeeks($maintenanceType->frequency_value);
            case 'month':
                return $serviceDate->copy()->addMonths($maintenanceType->frequency_value);
            case 'mile':
                // For mileage-based, return a date estimate
                return $serviceDate->copy()->addDays($maintenanceType->frequency_value / 100); // Rough estimate
            default:
                return null;
        }
    }

    private function generateMaintenanceNotes($maintenanceType)
    {
        $notes = [
            'OIL_CHANGE' => 'Oil and filter changed. Engine running smoothly.',
            'BRAKE_INSPECT' => 'Brake pads and rotors inspected. All within acceptable limits.',
            'ENGINE_DIAG' => 'Diagnostic scan completed. No error codes found.',
            'TIRE_ROTATION' => 'Tires rotated and balanced. Pressure checked.',
            'TRANS_SERVICE' => 'Transmission fluid changed. Filter replaced.',
            'EMERGENCY' => 'Emergency repair completed. Vehicle operational.'
        ];

        return $notes[$maintenanceType->code] ?? 'Maintenance completed successfully.';
    }

    private function generateRecommendations()
    {
        $recommendations = [
            'Monitor oil levels regularly',
            'Check tire pressure monthly',
            'Schedule next service in 6 months',
            'Replace air filter at next service',
            'Inspect belts and hoses',
            'Consider brake fluid flush'
        ];

        return $recommendations[rand(0, count($recommendations) - 1)];
    }

    private function createRecurringSchedules($baseSchedule)
    {
        $currentDate = $baseSchedule->scheduled_date->copy();
        $endDate = $currentDate->copy()->addYear();
        
        for ($i = 0; $i < 4; $i++) { // Create 4 recurring instances
            switch ($baseSchedule->recurring_type) {
                case 'monthly':
                    $currentDate->addMonths($baseSchedule->recurring_interval);
                    break;
                case 'quarterly':
                    $currentDate->addMonths(3 * $baseSchedule->recurring_interval);
                    break;
            }

            if ($currentDate->lte($endDate)) {
                MaintenanceSchedule::create([
                    'vehicle_id' => $baseSchedule->vehicle_id,
                    'technician_id' => $baseSchedule->technician_id,
                    'maintenance_type' => $baseSchedule->maintenance_type,
                    'scheduled_date' => $currentDate->copy(),
                    'estimated_duration' => $baseSchedule->estimated_duration,
                    'priority' => $baseSchedule->priority,
                    'status' => 'scheduled',
                    'description' => $baseSchedule->description,
                    'created_by' => $baseSchedule->created_by,
                    'parent_schedule_id' => $baseSchedule->id,
                    'estimated_cost' => $baseSchedule->estimated_cost,
                    'required_parts' => $baseSchedule->required_parts,
                    'is_recurring' => false
                ]);
            }
        }
    }

    private function generateAlertMessage($alertType, $vehicle)
    {
        $messages = [
            'overdue_maintenance' => "Vehicle {$vehicle->vehicle_number} is overdue for scheduled maintenance.",
            'low_health_score' => "Vehicle {$vehicle->vehicle_number} has a declining health score requiring attention.",
            'high_mileage' => "Vehicle {$vehicle->vehicle_number} has reached high mileage and needs inspection.",
            'sensor_warning' => "Sensor warning detected on vehicle {$vehicle->vehicle_number}.",
            'driver_request' => "Driver has requested maintenance for vehicle {$vehicle->vehicle_number}.",
            'cost_threshold' => "Maintenance costs for vehicle {$vehicle->vehicle_number} have exceeded threshold."
        ];

        return $messages[$alertType] ?? "Alert for vehicle {$vehicle->vehicle_number}.";
    }

    private function getThresholdValue($alertType)
    {
        $thresholds = [
            'low_health_score' => 60,
            'high_mileage' => 100000,
            'cost_threshold' => 5000
        ];

        return $thresholds[$alertType] ?? null;
    }

    private function getCurrentValue($alertType, $vehicle)
    {
        $values = [
            'low_health_score' => $vehicle->health_score,
            'high_mileage' => $vehicle->mileage,
            'cost_threshold' => rand(5000, 8000)
        ];

        return $values[$alertType] ?? null;
    }
}
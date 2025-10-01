<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incident;
use App\Models\User;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $driver = User::where('email', 'driver1@zar.com')->first();
        $tech   = User::where('email', 'tech1@zar.com')->first();

        if (!$driver) {
            $this->command->warn('Seed skipped: driver1@zar.com not found.');
            return;
        }

        Incident::updateOrCreate(
            ['title' => 'Engine overheating warning'],
            [
                'description' => 'Temperature gauge spiked during climb. Burning smell noted.',
                'category' => 'Engine',
                'severity' => 'P2',
                'status' => 'New',
                'reported_by_user_id' => $driver->id,
                'assigned_to_user_id' => $tech?->id,
                'vehicle_identifier' => 'TRK-001',
                'odometer' => 154230,
                'latitude' => -18.1245678,
                'longitude' => 178.4421000,
                'dtc_codes' => ['P0217'], // Overheat condition
            ]
        );
    }
}

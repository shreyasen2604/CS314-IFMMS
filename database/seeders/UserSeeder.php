<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@zar.com'],
            ['name' => 'System Admin','role' => 'Admin','password' => 'Admin@12345']
        );

        User::updateOrCreate(
            ['email' => 'driver1@zar.com'],
            ['name' => 'John Driver','role' => 'Driver','password' => 'Driver@12345']
        );

        User::updateOrCreate(
            ['email' => 'tech1@zar.com'],
            ['name' => 'Tina Technician','role' => 'Technician','password' => 'Tech@12345']
        );
    }
}

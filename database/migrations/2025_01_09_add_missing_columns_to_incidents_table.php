<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Add missing columns if they don't exist
            $columns = [
                'location_description' => ['type' => 'text', 'nullable' => true, 'after' => 'longitude'],
                'fuel_level' => ['type' => 'string', 'nullable' => true, 'after' => 'odometer'],
                'weather_conditions' => ['type' => 'string', 'nullable' => true, 'after' => 'location_description'],
                'road_conditions' => ['type' => 'string', 'nullable' => true, 'after' => 'weather_conditions'],
                'additional_notes' => ['type' => 'text', 'nullable' => true, 'after' => 'road_conditions'],
            ];

            foreach ($columns as $columnName => $config) {
                if (!Schema::hasColumn('incidents', $columnName)) {
                    switch ($config['type']) {
                        case 'text':
                            $column = $table->text($columnName);
                            break;
                        case 'string':
                            $column = $table->string($columnName);
                            break;
                        default:
                            $column = $table->string($columnName);
                    }
                    
                    if ($config['nullable']) {
                        $column->nullable();
                    }
                    
                    if (isset($config['after'])) {
                        $column->after($config['after']);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $columns = [
                'location_description',
                'fuel_level',
                'weather_conditions',
                'road_conditions',
                'additional_notes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('incidents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
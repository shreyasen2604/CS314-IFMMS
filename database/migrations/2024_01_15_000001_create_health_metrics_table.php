<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('health_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('engine_health')->nullable();
            $table->string('battery_condition')->nullable();
            $table->decimal('tire_pressure', 4, 2)->nullable();
            $table->string('oil_level')->nullable();
            $table->string('brake_condition')->nullable();
            $table->decimal('fuel_efficiency', 5, 2)->nullable();
            $table->integer('mileage')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_due')->nullable();
            $table->timestamps();

            $table->index('vehicle_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('health_metrics');
    }
};

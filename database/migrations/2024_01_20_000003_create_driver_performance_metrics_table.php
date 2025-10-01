<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->date('metric_date');
            $table->decimal('total_distance', 10, 2)->default(0); // km
            $table->integer('total_driving_time')->default(0); // minutes
            $table->decimal('fuel_efficiency', 8, 2)->nullable(); // km per liter
            $table->decimal('average_speed', 8, 2)->nullable(); // km/h
            $table->integer('routes_completed')->default(0);
            $table->integer('routes_assigned')->default(0);
            $table->decimal('on_time_percentage', 5, 2)->default(0); // percentage
            $table->integer('safety_incidents')->default(0);
            $table->integer('traffic_violations')->default(0);
            $table->decimal('customer_rating', 3, 2)->nullable(); // 1-5 scale
            $table->integer('deliveries_completed')->default(0);
            $table->integer('deliveries_failed')->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('idle_time', 8, 2)->default(0); // hours
            $table->json('performance_scores')->nullable(); // Store detailed scores
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['driver_id', 'metric_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_performance_metrics');
    }
};
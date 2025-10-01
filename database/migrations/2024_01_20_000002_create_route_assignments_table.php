<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('route_assignments')) {
            Schema::create('route_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('assignment_date');
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled']);
            $table->time('scheduled_start_time');
            $table->time('scheduled_end_time');
            $table->time('actual_start_time')->nullable();
            $table->time('actual_end_time')->nullable();
            $table->decimal('actual_distance', 10, 2)->nullable();
            $table->integer('actual_duration')->nullable(); // in minutes
            $table->decimal('fuel_consumed', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('gps_tracking_data')->nullable(); // Store GPS coordinates
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('route_assignments');
    }
};
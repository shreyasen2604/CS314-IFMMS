<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('routes')) {
            Schema::create('routes', function (Blueprint $table) {
                $table->id();
                $table->string('route_name');
                $table->string('route_code')->unique();
                $table->text('description')->nullable();
                $table->json('waypoints'); // Store route waypoints as JSON
                $table->decimal('total_distance', 10, 2); // in kilometers
                $table->integer('estimated_duration'); // in minutes
                $table->string('start_location');
                $table->string('end_location');
                $table->enum('route_type', ['delivery', 'pickup', 'service', 'maintenance', 'emergency']);
                $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
                $table->enum('status', ['active', 'inactive', 'under_review']);
                $table->json('schedule_days')->nullable(); // Days of week when route is active
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->decimal('fuel_cost_estimate', 8, 2)->nullable();
                $table->text('special_instructions')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('routes');
    }
};
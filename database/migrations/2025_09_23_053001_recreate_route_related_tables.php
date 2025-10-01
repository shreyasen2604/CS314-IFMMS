<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Recreate route_assignments table
        Schema::dropIfExists('route_assignments');
        Schema::create('route_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('assignment_date');
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
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

        // Recreate route_checkpoints table
        Schema::dropIfExists('route_checkpoints');
        Schema::create('route_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->string('checkpoint_name');
            $table->text('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('sequence_order');
            $table->enum('checkpoint_type', ['pickup', 'delivery', 'rest_stop', 'fuel_station', 'inspection'])->default('delivery');
            $table->integer('estimated_duration')->default(0); // in minutes
            $table->text('special_instructions')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->string('contact_info')->nullable();
            $table->timestamps();
        });

        // Recreate checkpoint_visits table
        Schema::dropIfExists('checkpoint_visits');
        Schema::create('checkpoint_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_assignment_id')->constrained('route_assignments')->onDelete('cascade');
            $table->foreignId('checkpoint_id')->constrained('route_checkpoints')->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped'])->default('pending');
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('departed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('photos')->nullable(); // Store photo paths
            $table->decimal('actual_latitude', 10, 8)->nullable();
            $table->decimal('actual_longitude', 11, 8)->nullable();
            $table->timestamps();
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkpoint_visits');
        Schema::dropIfExists('route_checkpoints');
        Schema::dropIfExists('route_assignments');
    }
};
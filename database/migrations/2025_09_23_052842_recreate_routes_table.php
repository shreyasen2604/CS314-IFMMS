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
        // First, disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop the existing routes table if it exists
        Schema::dropIfExists('routes');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Create the routes table with the correct structure
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->string('route_code')->unique();
            $table->text('description')->nullable();
            $table->json('waypoints')->nullable(); // Store route waypoints as JSON
            $table->decimal('total_distance', 10, 2)->default(0); // in kilometers
            $table->integer('estimated_duration')->default(0); // in minutes
            $table->string('start_location');
            $table->string('end_location');
            $table->enum('route_type', ['delivery', 'pickup', 'service', 'maintenance', 'emergency'])->default('delivery');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['active', 'inactive', 'under_review'])->default('active');
            $table->json('schedule_days')->nullable(); // Days of week when route is active
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('fuel_cost_estimate', 8, 2)->nullable();
            $table->text('special_instructions')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
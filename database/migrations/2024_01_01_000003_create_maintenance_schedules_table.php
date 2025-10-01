<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('maintenance_type', [
                'routine', 'preventive', 'corrective', 'emergency', 
                'oil_change', 'tire_rotation', 'brake_service', 'engine_service'
            ]);
            $table->datetime('scheduled_date');
            $table->integer('estimated_duration')->comment('Duration in minutes');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('description')->nullable();
            $table->boolean('recurring')->default(false);
            $table->integer('recurring_interval')->nullable()->comment('Interval in days/weeks/months');
            $table->enum('recurring_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
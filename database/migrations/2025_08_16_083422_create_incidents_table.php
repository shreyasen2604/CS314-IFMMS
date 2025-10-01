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
    Schema::create('incidents', function (Blueprint $table) {
        $table->id();

        // Basic details
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('category')->nullable(); // e.g., Engine, Tires, Brakes

        // Severity & status
        $table->enum('severity', ['P1','P2','P3','P4'])->default('P3'); // P1=Critical
        $table->enum('status', [
            'New','Acknowledged','Dispatched','In Progress',
            'Waiting','Resolved','Closed','On Hold','Cancelled','Duplicate'
        ])->default('New');

        // Link to users
        $table->foreignId('reported_by_user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();

        // Vehicle context (we'll replace with proper FK when Vehicle module exists)
        $table->string('vehicle_identifier')->nullable(); // e.g., TRK-001
        $table->unsignedInteger('odometer')->nullable();

        // Optional location + DTCs
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->json('dtc_codes')->nullable();

        // SLA timestamps (future use)
        $table->timestamp('acknowledged_at')->nullable();
        $table->timestamp('dispatched_at')->nullable();
        $table->timestamp('started_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamp('closed_at')->nullable();
        $table->timestamp('sla_response_due_at')->nullable();
        $table->timestamp('sla_resolution_due_at')->nullable();

        $table->timestamps();

        // Helpful indexes
        $table->index(['status', 'severity']);
        $table->index(['assigned_to_user_id']);
        $table->index(['reported_by_user_id']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};

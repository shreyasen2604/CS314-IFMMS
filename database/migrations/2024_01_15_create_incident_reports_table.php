<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['accident', 'breakdown', 'theft', 'vandalism', 'traffic_violation', 'other']);
            $table->enum('severity', ['minor', 'moderate', 'major', 'critical']);
            $table->enum('status', ['reported', 'investigating', 'processing', 'resolved', 'closed']);
            $table->datetime('incident_date');
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description');
            $table->text('immediate_action_taken')->nullable();
            $table->json('involved_parties')->nullable();
            $table->json('witnesses')->nullable();
            $table->boolean('police_report_filed')->default(false);
            $table->string('police_report_number')->nullable();
            $table->boolean('insurance_notified')->default(false);
            $table->string('insurance_claim_number')->nullable();
            $table->decimal('estimated_damage_cost', 10, 2)->nullable();
            $table->json('photos')->nullable();
            $table->json('documents')->nullable();
            $table->text('investigation_notes')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->datetime('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'severity']);
            $table->index('incident_number');
            $table->index('incident_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('incident_reports');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehicle_health_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // engine_temp, oil_pressure, brake_wear, etc.
            $table->decimal('metric_value', 10, 2);
            $table->string('unit')->nullable(); // °C, PSI, %, etc.
            $table->decimal('threshold_min', 10, 2)->nullable();
            $table->decimal('threshold_max', 10, 2)->nullable();
            $table->enum('status', ['normal', 'warning', 'critical'])->default('normal');
            $table->timestamp('recorded_at');
            $table->string('source')->default('manual'); // manual, sensor, diagnostic
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'recorded_at']);
            $table->index(['metric_type', 'recorded_at']);
            $table->index(['status', 'recorded_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_health_metrics');
    }
};
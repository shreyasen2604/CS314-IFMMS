<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('route_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->string('checkpoint_name');
            $table->text('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('sequence_order');
            $table->enum('checkpoint_type', ['pickup', 'delivery', 'rest_stop', 'fuel_station', 'inspection']);
            $table->integer('estimated_duration')->default(0); // minutes to spend at checkpoint
            $table->text('special_instructions')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->json('contact_info')->nullable(); // Store contact details
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_checkpoints');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('checkpoint_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_assignment_id')->constrained('route_assignments')->onDelete('cascade');
            $table->foreignId('checkpoint_id')->constrained('route_checkpoints')->onDelete('cascade');
            $table->timestamp('arrival_time')->nullable();
            $table->timestamp('departure_time')->nullable();
            $table->enum('status', ['pending', 'arrived', 'completed', 'skipped']);
            $table->decimal('actual_latitude', 10, 8)->nullable();
            $table->decimal('actual_longitude', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->json('proof_of_delivery')->nullable(); // Store photos, signatures, etc.
            $table->boolean('on_time')->default(true);
            $table->integer('delay_minutes')->default(0);
            $table->text('delay_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checkpoint_visits');
    }
};
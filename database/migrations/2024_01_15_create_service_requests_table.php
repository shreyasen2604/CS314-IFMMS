<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('category'); // breakdown, maintenance, inspection, other
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed', 'cancelled'])->default('open');
            $table->string('subject');
            $table->text('description');
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('attachments')->nullable();
            $table->datetime('response_time')->nullable();
            $table->datetime('resolution_time')->nullable();
            $table->integer('satisfaction_rating')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'priority']);
            $table->index('ticket_number');
            $table->index('requester_id');
            $table->index('assigned_to');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_requests');
    }
};
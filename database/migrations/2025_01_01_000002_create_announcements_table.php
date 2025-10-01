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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['info', 'warning', 'alert', 'success'])->default('info');
            $table->enum('target_audience', ['all', 'drivers', 'technicians', 'admins'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->datetime('publish_at')->nullable();
            $table->datetime('expire_at')->nullable();
            $table->json('attachments')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('target_audience');
            $table->index(['publish_at', 'expire_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
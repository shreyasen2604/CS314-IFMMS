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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('maintenance_alerts')->default(true);
            $table->boolean('incident_updates')->default(true);
            $table->boolean('announcement_notifications')->default(true);
            $table->boolean('message_notifications')->default(true);
            $table->json('quiet_hours')->nullable(); // {"start": "22:00", "end": "07:00"}
            $table->string('preferred_language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->json('notification_channels')->nullable(); // ["email", "sms", "push", "in_app"]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
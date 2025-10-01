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
    Schema::create('incident_updates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->enum('type', ['comment','status','assignment','system'])->default('comment');
        $table->text('body')->nullable();     // free text (comment or message)
        $table->json('data')->nullable();     // optional structured info
        $table->timestamps();

        $table->index(['incident_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_updates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->integer('estimated_duration')->default(60); // minutes
            $table->decimal('estimated_cost', 10, 2)->default(0);
            $table->string('frequency_type')->nullable(); // days, weeks, months, miles, etc.
            $table->integer('frequency_value')->nullable();
            $table->boolean('is_preventive')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('required_skills')->nullable();
            $table->json('required_parts')->nullable();
            $table->enum('priority_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['is_preventive', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_types');
    }
};
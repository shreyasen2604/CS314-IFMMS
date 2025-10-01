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
        Schema::table('maintenance_records', function (Blueprint $table) {
            // Add the cost column if it doesn't exist
            if (!Schema::hasColumn('maintenance_records', 'cost')) {
                $table->decimal('cost', 10, 2)->default(0)->after('service_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_records', 'cost')) {
                $table->dropColumn('cost');
            }
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            // Add new columns for enhanced record keeping (check if they don't exist first)
            if (!Schema::hasColumn('maintenance_records', 'schedule_id')) {
                $table->foreignId('schedule_id')->nullable()->constrained('maintenance_schedules')->after('technician_id');
            }
            if (!Schema::hasColumn('maintenance_records', 'work_order_number')) {
                $table->string('work_order_number')->nullable()->unique()->after('schedule_id');
            }
            if (!Schema::hasColumn('maintenance_records', 'parts_cost')) {
                $table->decimal('parts_cost', 10, 2)->default(0)->after('cost');
            }
            if (!Schema::hasColumn('maintenance_records', 'labor_cost')) {
                $table->decimal('labor_cost', 10, 2)->default(0)->after('parts_cost');
            }
            if (!Schema::hasColumn('maintenance_records', 'parts_used')) {
                $table->json('parts_used')->nullable()->after('labor_cost');
            }
            if (!Schema::hasColumn('maintenance_records', 'labor_hours')) {
                $table->integer('labor_hours')->nullable()->after('parts_used');
            }
            if (!Schema::hasColumn('maintenance_records', 'maintenance_category')) {
                $table->enum('maintenance_category', ['preventive', 'corrective', 'emergency', 'inspection'])->default('corrective')->after('labor_hours');
            }
            if (!Schema::hasColumn('maintenance_records', 'recommendations')) {
                $table->text('recommendations')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('maintenance_records', 'before_photos')) {
                $table->json('before_photos')->nullable()->after('recommendations');
            }
            if (!Schema::hasColumn('maintenance_records', 'after_photos')) {
                $table->json('after_photos')->nullable()->after('before_photos');
            }
            if (!Schema::hasColumn('maintenance_records', 'vehicle_condition_before')) {
                $table->decimal('vehicle_condition_before', 3, 1)->nullable()->after('after_photos'); // 1-10 scale
            }
            if (!Schema::hasColumn('maintenance_records', 'vehicle_condition_after')) {
                $table->decimal('vehicle_condition_after', 3, 1)->nullable()->after('vehicle_condition_before');
            }
            if (!Schema::hasColumn('maintenance_records', 'warranty_work')) {
                $table->boolean('warranty_work')->default(false)->after('vehicle_condition_after');
            }
            if (!Schema::hasColumn('maintenance_records', 'warranty_expiry')) {
                $table->date('warranty_expiry')->nullable()->after('warranty_work');
            }

            // Rename cost to total_cost for clarity (only if cost column exists and total_cost doesn't)
            if (Schema::hasColumn('maintenance_records', 'cost') && !Schema::hasColumn('maintenance_records', 'total_cost')) {
                $table->renameColumn('cost', 'total_cost');
            }

            // Add indexes (only if they don't exist)
            if (!Schema::hasIndex('maintenance_records', ['maintenance_category', 'service_date'])) {
                $table->index(['maintenance_category', 'service_date']);
            }
            if (!Schema::hasIndex('maintenance_records', ['warranty_work', 'warranty_expiry'])) {
                $table->index(['warranty_work', 'warranty_expiry']);
            }
            if (!Schema::hasIndex('maintenance_records', ['status', 'service_date'])) {
                $table->index(['status', 'service_date']);
            }
        });
    }

    public function down()
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn([
                'schedule_id', 'work_order_number', 'parts_cost', 'labor_cost',
                'parts_used', 'labor_hours', 'maintenance_category', 'recommendations',
                'before_photos', 'after_photos', 'vehicle_condition_before',
                'vehicle_condition_after', 'warranty_work', 'warranty_expiry'
            ]);
            $table->renameColumn('total_cost', 'cost');
        });
    }
};
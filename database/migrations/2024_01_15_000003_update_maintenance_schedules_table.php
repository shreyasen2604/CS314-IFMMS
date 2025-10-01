<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            // Add new columns for enhanced scheduling (check if they don't exist first)
            if (!Schema::hasColumn('maintenance_schedules', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->after('technician_id');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'parent_schedule_id')) {
                $table->foreignId('parent_schedule_id')->nullable()->constrained('maintenance_schedules')->after('created_by');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('parent_schedule_id');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'recurring_type')) {
                $table->string('recurring_type')->nullable()->after('is_recurring'); // daily, weekly, monthly, yearly
            }
            if (!Schema::hasColumn('maintenance_schedules', 'recurring_interval')) {
                $table->integer('recurring_interval')->nullable()->after('recurring_type');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'recurring_end_date')) {
                $table->date('recurring_end_date')->nullable()->after('recurring_interval');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'required_parts')) {
                $table->json('required_parts')->nullable()->after('recurring_end_date');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'estimated_cost')) {
                $table->decimal('estimated_cost', 10, 2)->nullable()->after('required_parts');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'completion_notes')) {
                $table->text('completion_notes')->nullable()->after('estimated_cost');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('completion_notes');
            }
            if (!Schema::hasColumn('maintenance_schedules', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }

            // Add indexes for better performance (only if they don't exist)
            if (!Schema::hasIndex('maintenance_schedules', ['status', 'scheduled_date'])) {
                $table->index(['status', 'scheduled_date']);
            }
            if (!Schema::hasIndex('maintenance_schedules', ['priority', 'scheduled_date'])) {
                $table->index(['priority', 'scheduled_date']);
            }
            if (!Schema::hasIndex('maintenance_schedules', ['is_recurring', 'recurring_type'])) {
                $table->index(['is_recurring', 'recurring_type']);
            }
        });
    }

    public function down()
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['parent_schedule_id']);
            $table->dropColumn([
                'created_by', 'parent_schedule_id', 'is_recurring', 'recurring_type',
                'recurring_interval', 'recurring_end_date', 'required_parts',
                'estimated_cost', 'completion_notes', 'started_at', 'completed_at'
            ]);
        });
    }
};
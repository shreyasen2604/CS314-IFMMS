<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_alerts', function (Blueprint $table) {
            // Add new columns for enhanced alert management (check if they don't exist first)
            if (!Schema::hasColumn('maintenance_alerts', 'resolved_by')) {
                $table->foreignId('resolved_by')->nullable()->constrained('users')->after('acknowledged_by');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('acknowledged_at');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'acknowledgment_notes')) {
                $table->text('acknowledgment_notes')->nullable()->after('resolved_at');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable()->after('acknowledgment_notes');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'auto_resolve')) {
                $table->boolean('auto_resolve')->default(false)->after('resolution_notes');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'auto_resolve_at')) {
                $table->timestamp('auto_resolve_at')->nullable()->after('auto_resolve');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'alert_data')) {
                $table->json('alert_data')->nullable()->after('auto_resolve_at'); // Additional structured data
            }
            if (!Schema::hasColumn('maintenance_alerts', 'source')) {
                $table->string('source')->default('system')->after('alert_data'); // system, manual, sensor
            }
            if (!Schema::hasColumn('maintenance_alerts', 'escalation_level')) {
                $table->integer('escalation_level')->default(1)->after('source');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('escalation_level');
            }
            if (!Schema::hasColumn('maintenance_alerts', 'notification_sent')) {
                $table->json('notification_sent')->nullable()->after('escalated_at'); // Track sent notifications
            }

            // Add indexes for better performance (only if they don't exist)
            if (!Schema::hasIndex('maintenance_alerts', ['severity', 'status', 'created_at'])) {
                $table->index(['severity', 'status', 'created_at']);
            }
            if (!Schema::hasIndex('maintenance_alerts', ['alert_type', 'status'])) {
                $table->index(['alert_type', 'status']);
            }
            if (!Schema::hasIndex('maintenance_alerts', ['auto_resolve', 'auto_resolve_at'])) {
                $table->index(['auto_resolve', 'auto_resolve_at']);
            }
            if (!Schema::hasIndex('maintenance_alerts', ['escalation_level', 'escalated_at'])) {
                $table->index(['escalation_level', 'escalated_at']);
            }
        });
    }

    public function down()
    {
        Schema::table('maintenance_alerts', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn([
                'resolved_by', 'resolved_at', 'acknowledgment_notes', 'resolution_notes',
                'auto_resolve', 'auto_resolve_at', 'alert_data', 'source',
                'escalation_level', 'escalated_at', 'notification_sent'
            ]);
        });
    }
};
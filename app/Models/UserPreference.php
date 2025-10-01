<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'maintenance_alerts',
        'incident_updates',
        'announcement_notifications',
        'message_notifications',
        'quiet_hours',
        'preferred_language',
        'timezone',
        'notification_channels'
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'maintenance_alerts' => 'boolean',
        'incident_updates' => 'boolean',
        'announcement_notifications' => 'boolean',
        'message_notifications' => 'boolean',
        'quiet_hours' => 'array',
        'notification_channels' => 'array'
    ];

    /**
     * Get the user that owns the preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user is in quiet hours
     */
    public function isInQuietHours(): bool
    {
        if (!$this->quiet_hours) {
            return false;
        }

        $now = now()->setTimezone($this->timezone ?? 'UTC');
        $currentTime = $now->format('H:i');
        
        $start = $this->quiet_hours['start'] ?? null;
        $end = $this->quiet_hours['end'] ?? null;

        if (!$start || !$end) {
            return false;
        }

        // Handle overnight quiet hours (e.g., 22:00 to 07:00)
        if ($start > $end) {
            return $currentTime >= $start || $currentTime <= $end;
        }

        return $currentTime >= $start && $currentTime <= $end;
    }

    /**
     * Get enabled notification channels
     */
    public function getEnabledChannels(): array
    {
        $channels = [];

        if ($this->email_notifications) {
            $channels[] = 'email';
        }

        if ($this->sms_notifications) {
            $channels[] = 'sms';
        }

        if ($this->push_notifications) {
            $channels[] = 'push';
        }

        // Always include in-app notifications
        $channels[] = 'database';

        return $channels;
    }

    /**
     * Check if a specific notification type is enabled
     */
    public function isNotificationTypeEnabled(string $type): bool
    {
        switch ($type) {
            case 'maintenance':
                return $this->maintenance_alerts;
            case 'incident':
                return $this->incident_updates;
            case 'announcement':
                return $this->announcement_notifications;
            case 'message':
                return $this->message_notifications;
            default:
                return true;
        }
    }
}
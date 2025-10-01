<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class IncidentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_number',
        'vehicle_id',
        'driver_id',
        'reported_by',
        'type',
        'severity',
        'status',
        'incident_date',
        'location',
        'latitude',
        'longitude',
        'description',
        'immediate_action_taken',
        'involved_parties',
        'witnesses',
        'police_report_filed',
        'police_report_number',
        'insurance_notified',
        'insurance_claim_number',
        'estimated_damage_cost',
        'photos',
        'documents',
        'investigation_notes',
        'resolution_notes',
        'resolved_at'
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'resolved_at' => 'datetime',
        'involved_parties' => 'array',
        'witnesses' => 'array',
        'photos' => 'array',
        'documents' => 'array',
        'police_report_filed' => 'boolean',
        'insurance_notified' => 'boolean',
        'estimated_damage_cost' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->incident_number = 'INC-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(SupportComment::class, 'commentable');
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'danger',
            'major' => 'warning',
            'moderate' => 'info',
            'minor' => 'success',
            default => 'secondary'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'reported' => 'danger',
            'investigating' => 'warning',
            'processing' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary'
        };
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    public function scopeCritical($query)
    {
        return $query->whereIn('severity', ['critical', 'major']);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('incident_date', '>=', now()->subDays($days));
    }
}
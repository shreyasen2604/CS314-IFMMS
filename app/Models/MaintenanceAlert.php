<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'alert_type',
        'severity',
        'title',
        'message',
        'threshold_value',
        'current_value',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'resolved_at'
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'threshold_value' => 'decimal:2',
        'current_value' => 'decimal:2'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }
}
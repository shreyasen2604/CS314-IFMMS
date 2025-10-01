<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'technician_id',
        'maintenance_type',
        'scheduled_date',
        'estimated_duration',
        'priority',
        'status',
        'description',
        'recurring',
        'recurring_interval',
        'recurring_type',
        'created_by'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'recurring' => 'boolean'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_date->isPast() && $this->status !== 'completed';
    }

    public function isDueToday(): bool
    {
        return $this->scheduled_date->isToday() && $this->status !== 'completed';
    }
}
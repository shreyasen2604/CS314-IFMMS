<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_number',
        'make',
        'model',
        'year',
        'vin',
        'license_plate',
        'mileage',
        'fuel_type',
        'status',
        'last_maintenance_date',
        'next_maintenance_due',
        'health_score',
        'driver_id',
        'location',
        'department'
    ];

    protected $casts = [
        'last_maintenance_date' => 'date',
        'next_maintenance_due' => 'date',
        'health_score' => 'decimal:2',
    ];

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function healthMetrics()
    {
        return $this->hasOne(HealthMetrics::class)->latest();
    }

    public function allHealthMetrics()
    {
        return $this->hasMany(HealthMetrics::class);
    }

    public function isOverdue(): bool
    {
        return $this->next_maintenance_due && $this->next_maintenance_due->isPast();
    }

    public function isDueSoon(): bool
    {
        return $this->next_maintenance_due &&
               $this->next_maintenance_due->diffInDays(now()) <= 7 &&
               !$this->isOverdue();
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}

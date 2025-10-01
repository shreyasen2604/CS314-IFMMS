<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class HealthMetrics extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'engine_health',
        'battery_condition',
        'tire_pressure',
        'oil_level',
        'brake_condition',
        'mileage',
        'last_service_date',
        'next_service_due'
    ];

    public function scopeRecent(Builder $query)
    {
        return $query->latest('created_at');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}

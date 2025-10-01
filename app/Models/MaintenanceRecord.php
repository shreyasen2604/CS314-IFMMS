<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'technician_id',
        'maintenance_type',
        'description',
        'cost',
        'parts_cost',
        'labor_cost',
        'mileage_at_service',
        'service_date',
        'completion_date',
        'status',
        'notes',
        'next_service_mileage',
        'next_service_date'
    ];

    protected $casts = [
        'service_date' => 'date',
        'completion_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function getTotalCostAttribute(): float
    {
        return $this->parts_cost + $this->labor_cost;
    }
}
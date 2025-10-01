<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'estimated_duration',
        'estimated_cost',
        'frequency_type',
        'frequency_value',
        'is_preventive',
        'is_active',
        'required_skills',
        'required_parts',
        'priority_level'
    ];

    protected $casts = [
        'estimated_duration' => 'integer',
        'estimated_cost' => 'decimal:2',
        'frequency_value' => 'integer',
        'is_preventive' => 'boolean',
        'is_active' => 'boolean',
        'required_skills' => 'array',
        'required_parts' => 'array'
    ];

    // Relationships
    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class, 'maintenance_type', 'code');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class, 'maintenance_type', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePreventive($query)
    {
        return $query->where('is_preventive', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Static methods
    public static function getCategories()
    {
        return [
            'engine' => 'Engine',
            'transmission' => 'Transmission',
            'brakes' => 'Brakes',
            'electrical' => 'Electrical',
            'hvac' => 'HVAC',
            'tires' => 'Tires & Wheels',
            'body' => 'Body & Interior',
            'safety' => 'Safety Systems',
            'fluids' => 'Fluids & Filters',
            'general' => 'General Maintenance'
        ];
    }
}
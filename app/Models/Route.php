<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'route_code',
        'description',
        'waypoints',
        'total_distance',
        'estimated_duration',
        'start_location',
        'end_location',
        'route_type',
        'priority',
        'status',
        'schedule_days',
        'start_time',
        'end_time',
        'fuel_cost_estimate',
        'special_instructions',
        'created_by'
    ];

    protected $casts = [
        'waypoints' => 'array',
        'schedule_days' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_distance' => 'decimal:2',
        'fuel_cost_estimate' => 'decimal:2'
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(RouteAssignment::class);
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(RouteCheckpoint::class)->orderBy('sequence_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(RouteAssignment::class)->whereIn('status', ['assigned', 'in_progress']);
    }

    public function completedAssignments(): HasMany
    {
        return $this->hasMany(RouteAssignment::class)->where('status', 'completed');
    }

    public function getEfficiencyRatingAttribute()
    {
        $completed = $this->completedAssignments()->count();
        $total = $this->assignments()->count();
        
        if ($total === 0) return 0;
        
        return round(($completed / $total) * 100, 2);
    }

    public function getAverageCompletionTimeAttribute()
    {
        return $this->completedAssignments()
            ->whereNotNull('actual_duration')
            ->avg('actual_duration');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('route_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
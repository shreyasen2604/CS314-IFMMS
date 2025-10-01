<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'driver_id',
        'vehicle_id',
        'assignment_date',
        'status',
        'scheduled_start_time',
        'scheduled_end_time',
        'actual_start_time',
        'actual_end_time',
        'actual_distance',
        'actual_duration',
        'fuel_consumed',
        'notes',
        'gps_tracking_data',
        'assigned_by'
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'scheduled_start_time' => 'datetime:H:i',
        'scheduled_end_time' => 'datetime:H:i',
        'actual_start_time' => 'datetime:H:i',
        'actual_end_time' => 'datetime:H:i',
        'actual_distance' => 'decimal:2',
        'fuel_consumed' => 'decimal:2',
        'gps_tracking_data' => 'array'
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function checkpointVisits(): HasMany
    {
        return $this->hasMany(CheckpointVisit::class);
    }

    public function getIsOnTimeAttribute()
    {
        if (!$this->actual_end_time || !$this->scheduled_end_time) {
            return null;
        }

        return $this->actual_end_time <= $this->scheduled_end_time;
    }

    public function getDelayMinutesAttribute()
    {
        if (!$this->actual_end_time || !$this->scheduled_end_time) {
            return 0;
        }

        $delay = $this->actual_end_time->diffInMinutes($this->scheduled_end_time, false);
        return max(0, -$delay); // Return positive delay minutes
    }

    public function getCompletionPercentageAttribute()
    {
        $totalCheckpoints = $this->route->checkpoints()->count();
        if ($totalCheckpoints === 0) return 100;

        $completedCheckpoints = $this->checkpointVisits()->where('status', 'completed')->count();
        return round(($completedCheckpoints / $totalCheckpoints) * 100, 2);
    }

    public function getFuelEfficiencyAttribute()
    {
        if (!$this->fuel_consumed || !$this->actual_distance || $this->fuel_consumed == 0) {
            return null;
        }

        return round($this->actual_distance / $this->fuel_consumed, 2);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('assignment_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('assignment_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('assignment_date', now()->month)
                    ->whereYear('assignment_date', now()->year);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}
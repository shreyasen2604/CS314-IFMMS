<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckpointVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_assignment_id',
        'checkpoint_id',
        'status',
        'arrived_at',
        'departed_at',
        'notes',
        'photos',
        'actual_latitude',
        'actual_longitude'
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'departed_at' => 'datetime',
        'actual_latitude' => 'decimal:8',
        'actual_longitude' => 'decimal:8',
        'photos' => 'array'
    ];

    public function routeAssignment(): BelongsTo
    {
        return $this->belongsTo(RouteAssignment::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(RouteCheckpoint::class);
    }

    public function getDurationAttribute()
    {
        if (!$this->arrived_at || !$this->departed_at) {
            return null;
        }

        return $this->departed_at->diffInMinutes($this->arrived_at);
    }

    public function getActualCoordinatesAttribute()
    {
        if (!$this->actual_latitude || !$this->actual_longitude) {
            return null;
        }

        return [
            'lat' => (float) $this->actual_latitude,
            'lng' => (float) $this->actual_longitude
        ];
    }

    public function getLocationAccuracyAttribute()
    {
        if (!$this->actual_latitude || !$this->actual_longitude) {
            return null;
        }

        $checkpoint = $this->checkpoint;
        if (!$checkpoint) {
            return null;
        }

        return $this->calculateDistance(
            $checkpoint->latitude,
            $checkpoint->longitude,
            $this->actual_latitude,
            $this->actual_longitude
        );
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return round($earthRadius * $c, 2); // Return distance in meters
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOnTime($query)
    {
        return $query->where('on_time', true);
    }

    public function scopeDelayed($query)
    {
        return $query->where('on_time', false);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
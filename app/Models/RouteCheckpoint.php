<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteCheckpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'checkpoint_name',
        'address',
        'latitude',
        'longitude',
        'sequence_order',
        'checkpoint_type',
        'estimated_duration',
        'special_instructions',
        'is_mandatory',
        'contact_info'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_mandatory' => 'boolean',
        'contact_info' => 'array'
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(CheckpointVisit::class, 'checkpoint_id');
    }

    public function getCoordinatesAttribute()
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude
        ];
    }

    public function getDistanceFromPrevious()
    {
        $previousCheckpoint = $this->route->checkpoints()
            ->where('sequence_order', '<', $this->sequence_order)
            ->orderBy('sequence_order', 'desc')
            ->first();

        if (!$previousCheckpoint) {
            return 0;
        }

        return $this->calculateDistance(
            $previousCheckpoint->latitude,
            $previousCheckpoint->longitude,
            $this->latitude,
            $this->longitude
        );
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return round($earthRadius * $c, 2);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('checkpoint_type', $type);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }
}
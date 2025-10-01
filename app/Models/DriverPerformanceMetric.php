<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverPerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'metric_date',
        'total_distance',
        'total_driving_time',
        'fuel_efficiency',
        'average_speed',
        'routes_completed',
        'routes_assigned',
        'on_time_percentage',
        'safety_incidents',
        'traffic_violations',
        'customer_rating',
        'deliveries_completed',
        'deliveries_failed',
        'overtime_hours',
        'idle_time',
        'performance_scores',
        'notes'
    ];

    protected $casts = [
        'metric_date' => 'date',
        'total_distance' => 'decimal:2',
        'fuel_efficiency' => 'decimal:2',
        'average_speed' => 'decimal:2',
        'on_time_percentage' => 'decimal:2',
        'customer_rating' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'idle_time' => 'decimal:2',
        'performance_scores' => 'array'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getOverallPerformanceScoreAttribute()
    {
        $scores = [];
        
        // On-time performance (30%)
        $scores['on_time'] = $this->on_time_percentage * 0.3;
        
        // Route completion rate (25%)
        $completionRate = $this->routes_assigned > 0 ? ($this->routes_completed / $this->routes_assigned) * 100 : 0;
        $scores['completion'] = $completionRate * 0.25;
        
        // Safety score (25%) - inverse of incidents
        $safetyScore = max(0, 100 - ($this->safety_incidents * 10) - ($this->traffic_violations * 5));
        $scores['safety'] = $safetyScore * 0.25;
        
        // Fuel efficiency (20%)
        $fuelScore = $this->fuel_efficiency ? min(100, $this->fuel_efficiency * 10) : 50;
        $scores['fuel'] = $fuelScore * 0.2;
        
        return round(array_sum($scores), 2);
    }

    public function getPerformanceGradeAttribute()
    {
        $score = $this->overall_performance_score;
        
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'C+';
        if ($score >= 65) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    public function getDeliverySuccessRateAttribute()
    {
        $total = $this->deliveries_completed + $this->deliveries_failed;
        if ($total === 0) return 0;
        
        return round(($this->deliveries_completed / $total) * 100, 2);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('metric_date', now()->month)
                    ->whereYear('metric_date', now()->year);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereMonth('metric_date', now()->subMonth()->month)
                    ->whereYear('metric_date', now()->subMonth()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('metric_date', now()->year);
    }

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public static function calculateMetricsForDriver($driverId, $date)
    {
        $assignments = RouteAssignment::where('driver_id', $driverId)
            ->whereDate('assignment_date', $date)
            ->get();

        $metrics = [
            'total_distance' => $assignments->sum('actual_distance') ?? 0,
            'total_driving_time' => $assignments->sum('actual_duration') ?? 0,
            'routes_completed' => $assignments->where('status', 'completed')->count(),
            'routes_assigned' => $assignments->count(),
            'fuel_consumed' => $assignments->sum('fuel_consumed') ?? 0,
        ];

        // Calculate derived metrics
        $metrics['fuel_efficiency'] = $metrics['fuel_consumed'] > 0 ? 
            round($metrics['total_distance'] / $metrics['fuel_consumed'], 2) : null;
        
        $metrics['average_speed'] = $metrics['total_driving_time'] > 0 ? 
            round(($metrics['total_distance'] / $metrics['total_driving_time']) * 60, 2) : null;
        
        $onTimeCount = $assignments->filter(function ($assignment) {
            return $assignment->is_on_time;
        })->count();
        
        $metrics['on_time_percentage'] = $assignments->count() > 0 ? 
            round(($onTimeCount / $assignments->count()) * 100, 2) : 0;

        return $metrics;
    }
}
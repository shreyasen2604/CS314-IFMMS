<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VehicleHealthMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'metric_type',
        'metric_value',
        'unit',
        'threshold_min',
        'threshold_max',
        'status',
        'recorded_at',
        'source',
        'notes'
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'threshold_min' => 'decimal:2',
        'threshold_max' => 'decimal:2',
        'recorded_at' => 'datetime'
    ];

    // Metric types constants
    const METRIC_TYPES = [
        'engine_temperature' => 'Engine Temperature',
        'oil_pressure' => 'Oil Pressure',
        'oil_level' => 'Oil Level',
        'coolant_level' => 'Coolant Level',
        'brake_fluid_level' => 'Brake Fluid Level',
        'brake_pad_wear' => 'Brake Pad Wear',
        'tire_pressure' => 'Tire Pressure',
        'tire_tread_depth' => 'Tire Tread Depth',
        'battery_voltage' => 'Battery Voltage',
        'battery_health' => 'Battery Health',
        'fuel_efficiency' => 'Fuel Efficiency',
        'transmission_fluid' => 'Transmission Fluid',
        'air_filter_condition' => 'Air Filter Condition',
        'exhaust_emissions' => 'Exhaust Emissions',
        'overall_health' => 'Overall Health Score'
    ];

    // Status constants
    const STATUS_NORMAL = 'normal';
    const STATUS_WARNING = 'warning';
    const STATUS_CRITICAL = 'critical';

    // Source constants
    const SOURCE_MANUAL = 'manual';
    const SOURCE_SENSOR = 'sensor';
    const SOURCE_DIAGNOSTIC = 'diagnostic';

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Scopes
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('recorded_at', '>=', now()->subDays($days));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCritical($query)
    {
        return $query->where('status', self::STATUS_CRITICAL);
    }

    public function scopeWarning($query)
    {
        return $query->where('status', self::STATUS_WARNING);
    }

    public function scopeNormal($query)
    {
        return $query->where('status', self::STATUS_NORMAL);
    }

    public function scopeLatestReading($query)
    {
        return $query->orderBy('recorded_at', 'desc')->limit(1);
    }

    // Accessors & Mutators
    public function getFormattedValueAttribute()
    {
        $value = number_format($this->metric_value, 2);
        return $this->unit ? "{$value} {$this->unit}" : $value;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_CRITICAL => 'danger',
            self::STATUS_WARNING => 'warning',
            self::STATUS_NORMAL => 'success',
            default => 'secondary'
        };
    }

    public function getStatusIconAttribute()
    {
        return match($this->status) {
            self::STATUS_CRITICAL => 'exclamation-circle',
            self::STATUS_WARNING => 'exclamation-triangle',
            self::STATUS_NORMAL => 'check-circle',
            default => 'info-circle'
        };
    }

    // Methods
    public function updateStatus()
    {
        if ($this->threshold_min !== null && $this->metric_value < $this->threshold_min) {
            $this->status = self::STATUS_CRITICAL;
        } elseif ($this->threshold_max !== null && $this->metric_value > $this->threshold_max) {
            $this->status = self::STATUS_CRITICAL;
        } elseif ($this->isNearThreshold()) {
            $this->status = self::STATUS_WARNING;
        } else {
            $this->status = self::STATUS_NORMAL;
        }

        $this->save();
        return $this;
    }

    public function isNearThreshold($warningPercentage = 0.1)
    {
        if ($this->threshold_min !== null) {
            $warningMin = $this->threshold_min * (1 + $warningPercentage);
            if ($this->metric_value <= $warningMin) {
                return true;
            }
        }

        if ($this->threshold_max !== null) {
            $warningMax = $this->threshold_max * (1 - $warningPercentage);
            if ($this->metric_value >= $warningMax) {
                return true;
            }
        }

        return false;
    }

    public function isCritical()
    {
        return $this->status === self::STATUS_CRITICAL;
    }

    public function isWarning()
    {
        return $this->status === self::STATUS_WARNING;
    }

    public function isNormal()
    {
        return $this->status === self::STATUS_NORMAL;
    }

    // Static methods
    public static function recordMetric($vehicleId, $type, $value, $unit = null, $source = self::SOURCE_MANUAL, $notes = null)
    {
        // Get thresholds based on metric type
        $thresholds = self::getDefaultThresholds($type);

        $metric = self::create([
            'vehicle_id' => $vehicleId,
            'metric_type' => $type,
            'metric_value' => $value,
            'unit' => $unit ?? $thresholds['unit'] ?? null,
            'threshold_min' => $thresholds['min'] ?? null,
            'threshold_max' => $thresholds['max'] ?? null,
            'recorded_at' => now(),
            'source' => $source,
            'notes' => $notes
        ]);

        // Update status based on thresholds
        $metric->updateStatus();

        // Update vehicle health score if needed
        if ($type === 'overall_health') {
            Vehicle::find($vehicleId)->update(['health_score' => $value]);
        }

        // Create alert if critical
        if ($metric->isCritical()) {
            self::createHealthAlert($metric);
        }

        return $metric;
    }

    public static function getDefaultThresholds($metricType)
    {
        $thresholds = [
            'engine_temperature' => ['min' => 70, 'max' => 110, 'unit' => '°C'],
            'oil_pressure' => ['min' => 20, 'max' => 80, 'unit' => 'PSI'],
            'oil_level' => ['min' => 20, 'max' => 100, 'unit' => '%'],
            'coolant_level' => ['min' => 30, 'max' => 100, 'unit' => '%'],
            'brake_fluid_level' => ['min' => 30, 'max' => 100, 'unit' => '%'],
            'brake_pad_wear' => ['min' => 20, 'max' => 100, 'unit' => '%'],
            'tire_pressure' => ['min' => 28, 'max' => 35, 'unit' => 'PSI'],
            'tire_tread_depth' => ['min' => 2, 'max' => 10, 'unit' => 'mm'],
            'battery_voltage' => ['min' => 12.2, 'max' => 14.7, 'unit' => 'V'],
            'battery_health' => ['min' => 50, 'max' => 100, 'unit' => '%'],
            'fuel_efficiency' => ['min' => 5, 'max' => 50, 'unit' => 'MPG'],
            'transmission_fluid' => ['min' => 30, 'max' => 100, 'unit' => '%'],
            'air_filter_condition' => ['min' => 20, 'max' => 100, 'unit' => '%'],
            'exhaust_emissions' => ['min' => 0, 'max' => 100, 'unit' => 'ppm'],
            'overall_health' => ['min' => 0, 'max' => 100, 'unit' => '%']
        ];

        return $thresholds[$metricType] ?? [];
    }

    protected static function createHealthAlert($metric)
    {
        $vehicle = $metric->vehicle;
        $metricName = self::METRIC_TYPES[$metric->metric_type] ?? $metric->metric_type;

        MaintenanceAlert::create([
            'vehicle_id' => $metric->vehicle_id,
            'type' => 'health_metric',
            'severity' => 'critical',
            'title' => "Critical {$metricName} Reading",
            'description' => "Vehicle {$vehicle->vehicle_number} has a critical {$metricName} reading of {$metric->formatted_value}",
            'metric_type' => $metric->metric_type,
            'metric_value' => $metric->metric_value,
            'threshold_value' => $metric->threshold_min ?? $metric->threshold_max,
            'status' => 'active',
            'created_by' => 'system'
        ]);
    }

    // Get latest metrics for a vehicle
    public static function getLatestMetrics($vehicleId)
    {
        $metrics = [];
        
        foreach (array_keys(self::METRIC_TYPES) as $type) {
            $latest = self::where('vehicle_id', $vehicleId)
                ->where('metric_type', $type)
                ->latestReading()
                ->first();
                
            if ($latest) {
                $metrics[$type] = $latest;
            }
        }

        return $metrics;
    }

    // Calculate overall health score based on all metrics
    public static function calculateHealthScore($vehicleId)
    {
        $metrics = self::where('vehicle_id', $vehicleId)
            ->recent(7) // Last 7 days
            ->get()
            ->groupBy('metric_type');

        $scores = [];
        $weights = [
            'engine_temperature' => 0.15,
            'oil_pressure' => 0.15,
            'brake_pad_wear' => 0.15,
            'tire_pressure' => 0.10,
            'battery_health' => 0.10,
            'coolant_level' => 0.10,
            'brake_fluid_level' => 0.10,
            'transmission_fluid' => 0.05,
            'air_filter_condition' => 0.05,
            'fuel_efficiency' => 0.05
        ];

        foreach ($metrics as $type => $typeMetrics) {
            $latestMetric = $typeMetrics->sortByDesc('recorded_at')->first();
            
            if ($latestMetric) {
                $score = match($latestMetric->status) {
                    self::STATUS_NORMAL => 100,
                    self::STATUS_WARNING => 70,
                    self::STATUS_CRITICAL => 30,
                    default => 50
                };

                $weight = $weights[$type] ?? 0.05;
                $scores[] = $score * $weight;
            }
        }

        $overallScore = array_sum($scores);
        
        // If we don't have enough data, assume 75% health
        if (count($scores) < 3) {
            $overallScore = 75;
        }

        // Record the overall health score
        self::recordMetric($vehicleId, 'overall_health', $overallScore, '%', self::SOURCE_DIAGNOSTIC);

        return $overallScore;
    }
}
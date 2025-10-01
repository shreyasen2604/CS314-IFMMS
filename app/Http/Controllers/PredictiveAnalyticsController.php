<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\VehicleHealthMetric;
use App\Models\MaintenanceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PredictiveAnalyticsController extends Controller
{
    public function index()
    {
        // Get all vehicles with health data
        $vehicleHealthData = Vehicle::with(['healthMetrics', 'maintenanceRecords'])
            ->get()
            ->map(function($vehicle) {
                $vehicle->health_score = $vehicle->health_score ?? 75; // Default health score
                return $vehicle;
            });

        // Get breakdown risk vehicles (vehicles with health score < 60 or overdue maintenance)
        $breakdownRisk = Vehicle::where(function($query) {
                $query->where('health_score', '<', 60)
                      ->orWhere('next_maintenance_due', '<', now());
            })
            ->get();

        // Get monthly costs
        $monthlyCosts = MaintenanceRecord::select(
                DB::raw('YEAR(service_date) as year'),
                DB::raw('MONTH(service_date) as month'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('COUNT(*) as service_count')
            )
            ->whereYear('service_date', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Get maintenance types distribution
        $maintenanceTypes = MaintenanceRecord::select('maintenance_type', DB::raw('COUNT(*) as count'))
            ->whereYear('service_date', now()->year)
            ->groupBy('maintenance_type')
            ->orderBy('count', 'desc')
            ->limit(8)
            ->get();

        // Get performance trends (last 30 days)
        $performanceTrends = MaintenanceRecord::select(
                DB::raw('DATE(service_date) as date'),
                DB::raw('COUNT(*) as service_count'),
                DB::raw('AVG(cost) as avg_cost')
            )
            ->where('service_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get additional analytics data
        $analytics = [
            'fleet_health_overview' => $this->getFleetHealthOverview(),
            'predictive_insights' => $this->getPredictiveInsights(),
            'cost_forecasting' => $this->getCostForecasting(),
            'breakdown_risk_assessment' => $this->getBreakdownRiskAssessment(),
            'performance_trends_data' => $this->getPerformanceTrends(),
            'maintenance_efficiency' => $this->getMaintenanceEfficiency()
        ];

        return view('maintenance.analytics', compact(
            'vehicleHealthData',
            'breakdownRisk',
            'monthlyCosts',
            'maintenanceTypes',
            'performanceTrends',
            'analytics'
        ));
    }

    public function getFleetHealthOverview()
    {
        $vehicles = Vehicle::with(['healthMetrics' => function($query) {
            $query->recent(30);
        }])->get();

        $healthData = [];
        $riskLevels = ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0];

        foreach ($vehicles as $vehicle) {
            $healthScore = $vehicle->health_score;
            $riskLevel = $this->calculateRiskLevel($vehicle);
            
            $healthData[] = [
                'vehicle_id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'health_score' => $healthScore,
                'risk_level' => $riskLevel,
                'last_maintenance' => $vehicle->last_maintenance_date,
                'next_due' => $vehicle->next_maintenance_due,
                'mileage' => $vehicle->mileage,
                'status' => $vehicle->status
            ];

            $riskLevels[$riskLevel]++;
        }

        return [
            'vehicles' => $healthData,
            'risk_distribution' => $riskLevels,
            'avg_health_score' => collect($healthData)->avg('health_score'),
            'vehicles_at_risk' => $riskLevels['high'] + $riskLevels['critical']
        ];
    }

    public function getPredictiveInsights()
    {
        $insights = [];

        // Predict vehicles likely to need maintenance soon
        $vehicles = Vehicle::with(['maintenanceRecords', 'healthMetrics'])->get();

        foreach ($vehicles as $vehicle) {
            $prediction = $this->predictMaintenanceNeeds($vehicle);
            if ($prediction['confidence'] > 0.7) {
                $insights[] = $prediction;
            }
        }

        // Sort by urgency (confidence * severity)
        usort($insights, function($a, $b) {
            return ($b['confidence'] * $this->getSeverityWeight($b['predicted_issue'])) <=> 
                   ($a['confidence'] * $this->getSeverityWeight($a['predicted_issue']));
        });

        return array_slice($insights, 0, 10); // Top 10 insights
    }

    public function getCostForecasting()
    {
        $historicalData = MaintenanceRecord::select(
            DB::raw('YEAR(service_date) as year'),
            DB::raw('MONTH(service_date) as month'),
            DB::raw('SUM(cost) as total_cost'),
            DB::raw('COUNT(*) as service_count')
        )
        ->whereYear('service_date', '>=', now()->subYears(2))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Simple trend analysis
        $monthlyAverages = $historicalData->groupBy(function($item) {
            return $item->month;
        })->map(function($monthData) {
            return [
                'avg_cost' => $monthData->avg('total_cost'),
                'avg_count' => $monthData->avg('service_count')
            ];
        });

        // Forecast next 6 months
        $forecast = [];
        $currentMonth = now();
        
        for ($i = 1; $i <= 6; $i++) {
            $forecastMonth = $currentMonth->copy()->addMonths($i);
            $monthNum = $forecastMonth->month;
            
            $baseAvg = $monthlyAverages->get($monthNum, ['avg_cost' => 5000, 'avg_count' => 10]);
            
            // Add trend factor (simplified)
            $trendFactor = 1 + (0.02 * $i); // 2% increase per month
            
            $forecast[] = [
                'month' => $forecastMonth->format('Y-m'),
                'predicted_cost' => round($baseAvg['avg_cost'] * $trendFactor, 2),
                'predicted_services' => round($baseAvg['avg_count'] * $trendFactor),
                'confidence' => max(0.5, 0.9 - ($i * 0.1)) // Decreasing confidence
            ];
        }

        return [
            'historical_data' => $historicalData,
            'monthly_averages' => $monthlyAverages,
            'forecast' => $forecast,
            'yearly_projection' => array_sum(array_column($forecast, 'predicted_cost')) * 2
        ];
    }

    public function getBreakdownRiskAssessment()
    {
        $vehicles = Vehicle::with(['maintenanceRecords', 'healthMetrics'])->get();
        $riskAssessment = [];

        foreach ($vehicles as $vehicle) {
            $riskScore = $this->calculateBreakdownRisk($vehicle);
            
            if ($riskScore > 0.3) { // Only include vehicles with significant risk
                $riskAssessment[] = [
                    'vehicle_id' => $vehicle->id,
                    'vehicle_number' => $vehicle->vehicle_number,
                    'risk_score' => $riskScore,
                    'risk_level' => $this->getRiskLevel($riskScore),
                    'primary_risk_factors' => $this->identifyRiskFactors($vehicle),
                    'recommended_actions' => $this->getRecommendedActions($vehicle, $riskScore),
                    'estimated_cost_if_breakdown' => $this->estimateBreakdownCost($vehicle)
                ];
            }
        }

        // Sort by risk score
        usort($riskAssessment, function($a, $b) {
            return $b['risk_score'] <=> $a['risk_score'];
        });

        return $riskAssessment;
    }

    public function getPerformanceTrends()
    {
        $trends = [];

        // Fleet availability trend
        $availabilityTrend = $this->calculateAvailabilityTrend();
        
        // Maintenance cost trend
        $costTrend = $this->calculateCostTrend();
        
        // Efficiency trend
        $efficiencyTrend = $this->calculateEfficiencyTrend();

        return [
            'availability' => $availabilityTrend,
            'cost' => $costTrend,
            'efficiency' => $efficiencyTrend,
            'overall_trend' => $this->calculateOverallTrend($availabilityTrend, $costTrend, $efficiencyTrend)
        ];
    }

    public function getMaintenanceEfficiency()
    {
        $efficiency = [];

        // Preventive vs Corrective ratio
        $preventive = MaintenanceRecord::where('maintenance_type', 'LIKE', '%preventive%')
            ->whereYear('service_date', now()->year)->count();
        $corrective = MaintenanceRecord::where('maintenance_type', 'LIKE', '%corrective%')
            ->whereYear('service_date', now()->year)->count();

        $efficiency['preventive_ratio'] = $preventive + $corrective > 0 ? 
            round($preventive / ($preventive + $corrective) * 100, 2) : 0;

        // Average downtime
        $efficiency['avg_downtime'] = $this->calculateAverageDowntime();

        // First-time fix rate
        $efficiency['first_time_fix_rate'] = $this->calculateFirstTimeFixRate();

        // Technician utilization
        $efficiency['technician_utilization'] = $this->calculateTechnicianUtilization();

        return $efficiency;
    }

    // API endpoints for real-time data
    public function getVehicleHealthData($vehicleId)
    {
        $vehicle = Vehicle::with(['healthMetrics' => function($query) {
            $query->recent(90)->orderBy('recorded_at');
        }])->findOrFail($vehicleId);

        $healthHistory = $vehicle->healthMetrics->groupBy('metric_type')->map(function($metrics) {
            return $metrics->map(function($metric) {
                return [
                    'date' => $metric->recorded_at->format('Y-m-d'),
                    'value' => $metric->metric_value,
                    'status' => $metric->status
                ];
            });
        });

        return response()->json([
            'vehicle' => $vehicle,
            'health_history' => $healthHistory,
            'current_health_score' => $vehicle->health_score,
            'risk_assessment' => $this->calculateBreakdownRisk($vehicle)
        ]);
    }

    public function getFleetTrends(Request $request)
    {
        $period = $request->get('period', 30); // days
        
        $trends = [
            'health_scores' => $this->getHealthScoreTrends($period),
            'maintenance_costs' => $this->getMaintenanceCostTrends($period),
            'availability' => $this->getAvailabilityTrends($period)
        ];

        return response()->json($trends);
    }

    // Private helper methods
    private function calculateRiskLevel($vehicle)
    {
        $score = $vehicle->health_score;
        $daysSinceLastMaintenance = $vehicle->last_maintenance_date ? 
            $vehicle->last_maintenance_date->diffInDays(now()) : 365;

        if ($score < 40 || $daysSinceLastMaintenance > 180) return 'critical';
        if ($score < 60 || $daysSinceLastMaintenance > 120) return 'high';
        if ($score < 80 || $daysSinceLastMaintenance > 60) return 'medium';
        return 'low';
    }

    private function predictMaintenanceNeeds($vehicle)
    {
        // Simplified prediction algorithm
        $factors = [];
        $confidence = 0;
        $predictedIssue = 'general_maintenance';

        // Mileage factor
        if ($vehicle->mileage > 100000) {
            $factors[] = 'high_mileage';
            $confidence += 0.3;
        }

        // Time since last maintenance
        $daysSinceLastMaintenance = $vehicle->last_maintenance_date ? 
            $vehicle->last_maintenance_date->diffInDays(now()) : 365;
        
        if ($daysSinceLastMaintenance > 90) {
            $factors[] = 'overdue_maintenance';
            $confidence += 0.4;
            $predictedIssue = 'routine_service';
        }

        // Health score
        if ($vehicle->health_score < 70) {
            $factors[] = 'declining_health';
            $confidence += 0.3;
            $predictedIssue = 'diagnostic_check';
        }

        return [
            'vehicle_id' => $vehicle->id,
            'vehicle_number' => $vehicle->vehicle_number,
            'predicted_issue' => $predictedIssue,
            'confidence' => min($confidence, 1.0),
            'risk_factors' => $factors,
            'recommended_date' => now()->addDays(7)->format('Y-m-d')
        ];
    }

    private function getSeverityWeight($issue)
    {
        return match($issue) {
            'engine_failure' => 5,
            'brake_system' => 4,
            'transmission' => 4,
            'diagnostic_check' => 2,
            'routine_service' => 1,
            default => 2
        };
    }

    private function calculateBreakdownRisk($vehicle)
    {
        $risk = 0;

        // Age factor
        $age = now()->year - $vehicle->year;
        $risk += min($age * 0.05, 0.3);

        // Mileage factor
        $risk += min($vehicle->mileage / 200000, 0.3);

        // Health score factor
        $risk += (100 - $vehicle->health_score) / 100 * 0.4;

        // Maintenance history factor
        $recentMaintenanceCount = $vehicle->maintenanceRecords()
            ->where('service_date', '>=', now()->subMonths(6))
            ->count();
        
        if ($recentMaintenanceCount > 3) {
            $risk += 0.2; // Frequent repairs indicate problems
        }

        return min($risk, 1.0);
    }

    private function getRiskLevel($score)
    {
        if ($score >= 0.8) return 'critical';
        if ($score >= 0.6) return 'high';
        if ($score >= 0.4) return 'medium';
        return 'low';
    }

    private function identifyRiskFactors($vehicle)
    {
        $factors = [];

        if ($vehicle->health_score < 60) $factors[] = 'Low health score';
        if ($vehicle->mileage > 150000) $factors[] = 'High mileage';
        if (now()->year - $vehicle->year > 10) $factors[] = 'Vehicle age';
        
        $daysSinceLastMaintenance = $vehicle->last_maintenance_date ? 
            $vehicle->last_maintenance_date->diffInDays(now()) : 365;
        if ($daysSinceLastMaintenance > 120) $factors[] = 'Overdue maintenance';

        return $factors;
    }

    private function getRecommendedActions($vehicle, $riskScore)
    {
        $actions = [];

        if ($riskScore > 0.8) {
            $actions[] = 'Schedule immediate inspection';
            $actions[] = 'Consider temporary service suspension';
        } elseif ($riskScore > 0.6) {
            $actions[] = 'Schedule priority maintenance';
            $actions[] = 'Increase monitoring frequency';
        } else {
            $actions[] = 'Schedule routine maintenance';
            $actions[] = 'Continue regular monitoring';
        }

        return $actions;
    }

    private function estimateBreakdownCost($vehicle)
    {
        // Simplified cost estimation
        $baseCost = 2000;
        $ageFactor = (now()->year - $vehicle->year) * 100;
        $mileageFactor = $vehicle->mileage / 1000;
        
        return round($baseCost + $ageFactor + $mileageFactor, 2);
    }

    private function calculateAvailabilityTrend()
    {
        // Mock implementation - would calculate actual availability over time
        return [
            'current' => 92.5,
            'trend' => 'stable',
            'change' => 0.2
        ];
    }

    private function calculateCostTrend()
    {
        // Mock implementation - would calculate cost trends
        return [
            'current' => 15000,
            'trend' => 'increasing',
            'change' => 8.5
        ];
    }

    private function calculateEfficiencyTrend()
    {
        // Mock implementation - would calculate efficiency trends
        return [
            'current' => 87.3,
            'trend' => 'improving',
            'change' => 3.2
        ];
    }

    private function calculateOverallTrend($availability, $cost, $efficiency)
    {
        // Simplified overall trend calculation
        $score = ($availability['current'] + $efficiency['current'] - ($cost['change'] * 5)) / 2;
        
        if ($score > 85) return 'excellent';
        if ($score > 75) return 'good';
        if ($score > 65) return 'fair';
        return 'needs_attention';
    }

    private function calculateAverageDowntime()
    {
        // Mock calculation - would be based on actual downtime data
        return '4.2 hours';
    }

    private function calculateFirstTimeFixRate()
    {
        // Mock calculation - would be based on actual repair data
        return 78.5;
    }

    private function calculateTechnicianUtilization()
    {
        // Mock calculation - would be based on actual technician work data
        return 82.3;
    }

    private function getHealthScoreTrends($period)
    {
        // Mock implementation - would return actual health score trends
        return [];
    }

    private function getMaintenanceCostTrends($period)
    {
        // Mock implementation - would return actual cost trends
        return [];
    }

    private function getAvailabilityTrends($period)
    {
        // Mock implementation - would return actual availability trends
        return [];
    }
}
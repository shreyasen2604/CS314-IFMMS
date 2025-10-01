<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DriverPerformanceMetric;
use App\Models\RouteAssignment;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'Driver')->with(['driverPerformanceMetrics' => function ($q) {
            $q->thisMonth()->latest('metric_date');
        }]);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('performance_grade')) {
            // This would require a more complex query to filter by calculated grade
            // For now, we'll filter after getting the results
        }

        $drivers = $query->paginate(20);

        // Calculate overall statistics
        $stats = $this->calculateOverallStats();

        return view('driver-performance.index', compact('drivers', 'stats'));
    }

    public function show(User $driver, Request $request)
    {
        if ($driver->role !== 'Driver') {
            abort(404, 'Driver not found');
        }

        $period = $request->get('period', 'month'); // month, quarter, year
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Get performance metrics for the period
        $metrics = DriverPerformanceMetric::where('driver_id', $driver->id)
            ->whereBetween('metric_date', [$startDate, $endDate])
            ->orderBy('metric_date', 'desc')
            ->get();

        // Get route assignments for the period
        $assignments = RouteAssignment::where('driver_id', $driver->id)
            ->with(['route', 'vehicle', 'checkpointVisits'])
            ->whereBetween('assignment_date', [$startDate, $endDate])
            ->orderBy('assignment_date', 'desc')
            ->limit(20)
            ->get();

        // Calculate summary statistics
        $summary = $this->calculateDriverSummary($driver->id, $startDate, $endDate);

        // Get performance trends
        $trends = $this->getPerformanceTrends($driver->id, $period);

        return view('driver-performance.show', compact('driver', 'metrics', 'assignments', 'summary', 'trends', 'period'));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'drivers' => 'required|array|min:2|max:5',
            'drivers.*' => 'exists:users,id',
            'period' => 'required|in:month,quarter,year'
        ]);

        $driverIds = $request->drivers;
        $period = $request->period;
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $drivers = User::whereIn('id', $driverIds)->where('role', 'Driver')->get();
        $comparisons = [];

        foreach ($drivers as $driver) {
            $summary = $this->calculateDriverSummary($driver->id, $startDate, $endDate);
            $comparisons[] = [
                'driver' => $driver,
                'summary' => $summary
            ];
        }

        return view('driver-performance.compare', compact('comparisons', 'period'));
    }

    public function rankings(Request $request)
    {
        $period = $request->get('period', 'month');
        $metric = $request->get('metric', 'overall_score');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $rankings = $this->calculateRankings($startDate, $endDate, $metric);

        return view('driver-performance.rankings', compact('rankings', 'period', 'metric'));
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Get fleet-wide analytics
        $analytics = [
            'fleet_performance' => $this->getFleetPerformanceData($startDate, $endDate),
            'performance_distribution' => $this->getPerformanceDistribution($startDate, $endDate),
            'trend_analysis' => $this->getTrendAnalysis($startDate, $endDate),
            'top_performers' => $this->getTopPerformers($startDate, $endDate),
            'improvement_areas' => $this->getImprovementAreas($startDate, $endDate)
        ];

        return view('driver-performance.analytics', compact('analytics', 'period'));
    }

    public function updateMetrics(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'safety_incidents' => 'integer|min:0',
            'traffic_violations' => 'integer|min:0',
            'customer_rating' => 'numeric|between:1,5',
            'notes' => 'nullable|string'
        ]);

        $driver = User::findOrFail($request->driver_id);
        if ($driver->role !== 'Driver') {
            return response()->json(['error' => 'Invalid driver'], 400);
        }

        // Calculate automatic metrics from route assignments
        $autoMetrics = DriverPerformanceMetric::calculateMetricsForDriver($request->driver_id, $request->date);

        // Update or create performance metric record
        $metric = DriverPerformanceMetric::updateOrCreate(
            [
                'driver_id' => $request->driver_id,
                'metric_date' => $request->date
            ],
            array_merge($autoMetrics, [
                'safety_incidents' => $request->safety_incidents ?? 0,
                'traffic_violations' => $request->traffic_violations ?? 0,
                'customer_rating' => $request->customer_rating,
                'notes' => $request->notes
            ])
        );

        return response()->json([
            'success' => 'Performance metrics updated successfully',
            'metric' => $metric
        ]);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'driver_ids' => 'required|array',
            'driver_ids.*' => 'exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $drivers = User::whereIn('id', $request->driver_ids)->where('role', 'Driver')->get();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $reportData = [];
        foreach ($drivers as $driver) {
            $summary = $this->calculateDriverSummary($driver->id, $startDate, $endDate);
            $reportData[] = [
                'driver' => $driver,
                'summary' => $summary,
                'metrics' => DriverPerformanceMetric::where('driver_id', $driver->id)
                    ->whereBetween('metric_date', [$startDate, $endDate])
                    ->orderBy('metric_date')
                    ->get()
            ];
        }

        switch ($request->format) {
            case 'csv':
                return $this->generateCSVReport($reportData, $startDate, $endDate);
            case 'excel':
                return $this->generateExcelReport($reportData, $startDate, $endDate);
            case 'pdf':
                return $this->generatePDFReport($reportData, $startDate, $endDate);
        }
    }

    private function calculateOverallStats()
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'total_drivers' => User::where('role', 'Driver')->count(),
            'active_drivers' => RouteAssignment::thisMonth()->distinct('driver_id')->count(),
            'avg_performance_score' => DriverPerformanceMetric::thisMonth()->avg('on_time_percentage') ?? 0,
            'total_routes_completed' => RouteAssignment::thisMonth()->where('status', 'completed')->count(),
            'safety_incidents' => DriverPerformanceMetric::thisMonth()->sum('safety_incidents'),
            'fuel_efficiency_avg' => DriverPerformanceMetric::thisMonth()->avg('fuel_efficiency') ?? 0
        ];
    }

    private function calculateDriverSummary($driverId, $startDate, $endDate)
    {
        $assignments = RouteAssignment::where('driver_id', $driverId)
            ->whereBetween('assignment_date', [$startDate, $endDate])
            ->get();

        $metrics = DriverPerformanceMetric::where('driver_id', $driverId)
            ->whereBetween('metric_date', [$startDate, $endDate])
            ->get();

        return [
            'total_assignments' => $assignments->count(),
            'completed_assignments' => $assignments->where('status', 'completed')->count(),
            'completion_rate' => $assignments->count() > 0 ? 
                round(($assignments->where('status', 'completed')->count() / $assignments->count()) * 100, 2) : 0,
            'total_distance' => $assignments->sum('actual_distance') ?? 0,
            'total_fuel_consumed' => $assignments->sum('fuel_consumed') ?? 0,
            'avg_fuel_efficiency' => $metrics->avg('fuel_efficiency') ?? 0,
            'on_time_percentage' => $metrics->avg('on_time_percentage') ?? 0,
            'safety_incidents' => $metrics->sum('safety_incidents'),
            'traffic_violations' => $metrics->sum('traffic_violations'),
            'avg_customer_rating' => $metrics->avg('customer_rating') ?? 0,
            'overall_score' => $metrics->avg('on_time_percentage') ?? 0 // Simplified calculation
        ];
    }

    private function getPerformanceTrends($driverId, $period)
    {
        $startDate = $this->getStartDate($period);
        
        $metrics = DriverPerformanceMetric::where('driver_id', $driverId)
            ->whereBetween('metric_date', [$startDate, now()])
            ->orderBy('metric_date')
            ->get();

        return [
            'on_time_trend' => $metrics->pluck('on_time_percentage', 'metric_date'),
            'fuel_efficiency_trend' => $metrics->pluck('fuel_efficiency', 'metric_date'),
            'safety_trend' => $metrics->pluck('safety_incidents', 'metric_date'),
            'completion_trend' => $metrics->map(function ($metric) {
                return $metric->routes_assigned > 0 ? 
                    ($metric->routes_completed / $metric->routes_assigned) * 100 : 0;
            })
        ];
    }

    private function calculateRankings($startDate, $endDate, $metric)
    {
        $drivers = User::where('role', 'Driver')->get();
        $rankings = [];

        foreach ($drivers as $driver) {
            $summary = $this->calculateDriverSummary($driver->id, $startDate, $endDate);
            $rankings[] = [
                'driver' => $driver,
                'score' => $summary[$metric] ?? 0,
                'summary' => $summary
            ];
        }

        // Sort by the selected metric
        usort($rankings, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $rankings;
    }

    private function getStartDate($period)
    {
        return match($period) {
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
    }

    private function getFleetPerformanceData($startDate, $endDate)
    {
        return [
            'total_assignments' => RouteAssignment::whereBetween('assignment_date', [$startDate, $endDate])->count(),
            'completed_assignments' => RouteAssignment::whereBetween('assignment_date', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'avg_completion_rate' => RouteAssignment::whereBetween('assignment_date', [$startDate, $endDate])
                ->selectRaw('AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as rate')
                ->value('rate') ?? 0,
            'total_distance' => RouteAssignment::whereBetween('assignment_date', [$startDate, $endDate])
                ->sum('actual_distance') ?? 0,
            'total_fuel_consumed' => RouteAssignment::whereBetween('assignment_date', [$startDate, $endDate])
                ->sum('fuel_consumed') ?? 0
        ];
    }

    private function getPerformanceDistribution($startDate, $endDate)
    {
        $metrics = DriverPerformanceMetric::whereBetween('metric_date', [$startDate, $endDate])->get();
        
        $distribution = [
            'excellent' => 0, // 90-100%
            'good' => 0,      // 80-89%
            'average' => 0,   // 70-79%
            'poor' => 0       // <70%
        ];

        foreach ($metrics as $metric) {
            $score = $metric->overall_performance_score;
            if ($score >= 90) $distribution['excellent']++;
            elseif ($score >= 80) $distribution['good']++;
            elseif ($score >= 70) $distribution['average']++;
            else $distribution['poor']++;
        }

        return $distribution;
    }

    private function getTrendAnalysis($startDate, $endDate)
    {
        // Implementation for trend analysis
        return [];
    }

    private function getTopPerformers($startDate, $endDate)
    {
        return $this->calculateRankings($startDate, $endDate, 'overall_score');
    }

    private function getImprovementAreas($startDate, $endDate)
    {
        // Implementation for identifying improvement areas
        return [];
    }

    private function generateCSVReport($reportData, $startDate, $endDate)
    {
        $csvData = "Driver Name,Email,Total Assignments,Completed,Completion Rate,Total Distance,Fuel Efficiency,On-Time %,Safety Incidents,Overall Score\n";
        
        foreach ($reportData as $data) {
            $driver = $data['driver'];
            $summary = $data['summary'];
            
            $csvData .= sprintf(
                "%s,%s,%d,%d,%.2f,%.2f,%.2f,%.2f,%d,%.2f\n",
                $driver->name,
                $driver->email,
                $summary['total_assignments'],
                $summary['completed_assignments'],
                $summary['completion_rate'],
                $summary['total_distance'],
                $summary['avg_fuel_efficiency'],
                $summary['on_time_percentage'],
                $summary['safety_incidents'],
                $summary['overall_score']
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="driver_performance_report.csv"');
    }

    private function generateExcelReport($reportData, $startDate, $endDate)
    {
        // Implementation for Excel report generation
        // This would typically use a library like PhpSpreadsheet
        return $this->generateCSVReport($reportData, $startDate, $endDate);
    }

    private function generatePDFReport($reportData, $startDate, $endDate)
    {
        // Implementation for PDF report generation
        // This would typically use a library like TCPDF or DomPDF
        return $this->generateCSVReport($reportData, $startDate, $endDate);
    }
}
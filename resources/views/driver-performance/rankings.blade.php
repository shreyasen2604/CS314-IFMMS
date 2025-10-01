@extends('layouts.app')

@section('title', 'Driver Performance Rankings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Driver Performance Rankings</h3>
                    <div>
                        <a href="{{ route('maintenance.driver-performance.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Back to Overview
                        </a>
                        <a href="{{ route('maintenance.driver-performance.analytics') }}" class="btn btn-info">
                            <i class="fas fa-chart-line"></i> Analytics
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('maintenance.driver-performance.rankings') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="period" class="form-label">Time Period</label>
                                    <select name="period" class="form-select" id="period">
                                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="metric" class="form-label">Ranking Metric</label>
                                    <select name="metric" class="form-select" id="metric">
                                        <option value="overall_score" {{ $metric == 'overall_score' ? 'selected' : '' }}>Overall Score</option>
                                        <option value="on_time_percentage" {{ $metric == 'on_time_percentage' ? 'selected' : '' }}>On-Time Performance</option>
                                        <option value="completion_rate" {{ $metric == 'completion_rate' ? 'selected' : '' }}>Completion Rate</option>
                                        <option value="avg_fuel_efficiency" {{ $metric == 'avg_fuel_efficiency' ? 'selected' : '' }}>Fuel Efficiency</option>
                                        <option value="avg_customer_rating" {{ $metric == 'avg_customer_rating' ? 'selected' : '' }}>Customer Rating</option>
                                        <option value="total_distance" {{ $metric == 'total_distance' ? 'selected' : '' }}>Total Distance</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-sync"></i> Update Rankings
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Top 3 Podium -->
                    @if(count($rankings) >= 3)
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="text-center mb-4">
                                <h4>Top Performers - {{ ucfirst($period) }} {{ ucfirst(str_replace('_', ' ', $metric)) }}</h4>
                            </div>
                            <div class="row justify-content-center">
                                <!-- 2nd Place -->
                                <div class="col-md-3 text-center">
                                    <div class="card border-secondary">
                                        <div class="card-body">
                                            <div class="position-relative mb-3">
                                                <div class="bg-secondary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 80px;">
                                                    @if($rankings[1]['driver']->profile_picture)
                                                        <img src="{{ asset('storage/' . $rankings[1]['driver']->profile_picture) }}" 
                                                             class="rounded-circle" width="70" height="70" alt="Avatar">
                                                    @else
                                                        <span class="text-white fw-bold fs-2">{{ substr($rankings[1]['driver']->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary fs-6">
                                                    2nd
                                                </span>
                                            </div>
                                            <h5>{{ $rankings[1]['driver']->name }}</h5>
                                            <p class="text-muted mb-2">{{ $rankings[1]['driver']->email }}</p>
                                            <h4 class="text-secondary">{{ number_format($rankings[1]['score'], 2) }}{{ $metric == 'total_distance' ? ' km' : ($metric == 'avg_fuel_efficiency' ? ' km/L' : ($metric == 'avg_customer_rating' ? '/5' : '%')) }}</h4>
                                        </div>
                                    </div>
                                </div>

                                <!-- 1st Place -->
                                <div class="col-md-3 text-center">
                                    <div class="card border-warning" style="margin-top: -20px;">
                                        <div class="card-body">
                                            <div class="position-relative mb-3">
                                                <div class="bg-warning rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                                     style="width: 100px; height: 100px;">
                                                    @if($rankings[0]['driver']->profile_picture)
                                                        <img src="{{ asset('storage/' . $rankings[0]['driver']->profile_picture) }}" 
                                                             class="rounded-circle" width="90" height="90" alt="Avatar">
                                                    @else
                                                        <span class="text-white fw-bold" style="font-size: 2.5rem;">{{ substr($rankings[0]['driver']->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning fs-5">
                                                    <i class="fas fa-crown"></i> 1st
                                                </span>
                                            </div>
                                            <h4>{{ $rankings[0]['driver']->name }}</h4>
                                            <p class="text-muted mb-2">{{ $rankings[0]['driver']->email }}</p>
                                            <h3 class="text-warning">{{ number_format($rankings[0]['score'], 2) }}{{ $metric == 'total_distance' ? ' km' : ($metric == 'avg_fuel_efficiency' ? ' km/L' : ($metric == 'avg_customer_rating' ? '/5' : '%')) }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3rd Place -->
                                <div class="col-md-3 text-center">
                                    <div class="card border-dark">
                                        <div class="card-body">
                                            <div class="position-relative mb-3">
                                                <div class="bg-dark rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 80px;">
                                                    @if($rankings[2]['driver']->profile_picture)
                                                        <img src="{{ asset('storage/' . $rankings[2]['driver']->profile_picture) }}" 
                                                             class="rounded-circle" width="70" height="70" alt="Avatar">
                                                    @else
                                                        <span class="text-white fw-bold fs-2">{{ substr($rankings[2]['driver']->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark fs-6">
                                                    3rd
                                                </span>
                                            </div>
                                            <h5>{{ $rankings[2]['driver']->name }}</h5>
                                            <p class="text-muted mb-2">{{ $rankings[2]['driver']->email }}</p>
                                            <h4 class="text-dark">{{ number_format($rankings[2]['score'], 2) }}{{ $metric == 'total_distance' ? ' km' : ($metric == 'avg_fuel_efficiency' ? ' km/L' : ($metric == 'avg_customer_rating' ? '/5' : '%')) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Full Rankings Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Complete Rankings</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Rank</th>
                                            <th>Driver</th>
                                            <th>{{ ucfirst(str_replace('_', ' ', $metric)) }}</th>
                                            <th>Assignments</th>
                                            <th>Completion Rate</th>
                                            <th>On-Time %</th>
                                            <th>Safety Score</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rankings as $index => $ranking)
                                        @php
                                            $driver = $ranking['driver'];
                                            $summary = $ranking['summary'];
                                            $safetyScore = 100 - ($summary['safety_incidents'] * 10);
                                            
                                            // Determine rank styling
                                            if ($index == 0) $rankClass = 'text-warning fw-bold';
                                            elseif ($index == 1) $rankClass = 'text-secondary fw-bold';
                                            elseif ($index == 2) $rankClass = 'text-dark fw-bold';
                                            else $rankClass = '';
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fs-5 {{ $rankClass }}">
                                                    @if($index == 0)
                                                        <i class="fas fa-crown text-warning"></i>
                                                    @endif
                                                    #{{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        @if($driver->profile_picture)
                                                            <img src="{{ asset('storage/' . $driver->profile_picture) }}" 
                                                                 class="rounded-circle" width="40" height="40" alt="Avatar">
                                                        @else
                                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 40px;">
                                                                <span class="text-white fw-bold">{{ substr($driver->name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $driver->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $driver->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fs-5 fw-bold {{ $rankClass }}">
                                                    {{ number_format($ranking['score'], 2) }}{{ $metric == 'total_distance' ? ' km' : ($metric == 'avg_fuel_efficiency' ? ' km/L' : ($metric == 'avg_customer_rating' ? '/5' : '%')) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $summary['completed_assignments'] }}</span> / {{ $summary['total_assignments'] }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $summary['completion_rate'] >= 90 ? 'success' : ($summary['completion_rate'] >= 80 ? 'warning' : 'danger') }}">
                                                    {{ number_format($summary['completion_rate'], 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $summary['on_time_percentage'] >= 90 ? 'success' : ($summary['on_time_percentage'] >= 80 ? 'warning' : 'danger') }}">
                                                    {{ number_format($summary['on_time_percentage'], 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $safetyScore >= 90 ? 'success' : ($safetyScore >= 80 ? 'warning' : 'danger') }}">
                                                    {{ number_format($safetyScore, 0) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('maintenance.driver-performance.show', $driver) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#period, #metric').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
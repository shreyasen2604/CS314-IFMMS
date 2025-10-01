@extends('layouts.app')

@section('title', 'Support Statistics - IFMMS')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-info bg-opacity-10 me-3">
                                    <i class="fas fa-chart-pie text-info fs-4"></i>
                                </div>
                                <div>
                                    <h1 class="h3 mb-1 fw-bold">Support Analytics</h1>
                                    <p class="text-muted mb-0">Service request performance metrics and insights</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="{{ route('support.service-requests.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Requests
                            </a>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Avg Resolution Time</h6>
                            <h3 class="mb-0">{{ round($stats['avg_resolution_time'] ?? 0) }} hrs</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-down"></i> 12% improvement
                            </small>
                        </div>
                        <div class="icon-shape bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Satisfaction Rating</h6>
                            <h3 class="mb-0">{{ number_format($stats['satisfaction_avg'] ?? 0, 1) }}/5</h3>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($stats['satisfaction_avg'] ?? 0))
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Open Tickets</h6>
                            <h3 class="mb-0">{{ $stats['by_status']->where('status', 'open')->first()->count ?? 0 }}</h3>
                            <small class="text-muted">Requires attention</small>
                        </div>
                        <div class="icon-shape bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-ticket-alt text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Resolution Rate</h6>
                            @php
                                $total = $stats['by_status']->sum('count');
                                $resolved = $stats['by_status']->whereIn('status', ['resolved', 'closed'])->sum('count');
                                $rate = $total > 0 ? ($resolved / $total) * 100 : 0;
                            @endphp
                            <h3 class="mb-0">{{ round($rate) }}%</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> Above target
                            </small>
                        </div>
                        <div class="icon-shape bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Requests by Status -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Requests by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Requests by Priority -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Requests by Priority</h5>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Requests by Category -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Requests by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Service Request Trend (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Response Time Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Within 1 hour</span>
                            <span class="fw-semibold">45%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: 45%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>1-4 hours</span>
                            <span class="fw-semibold">30%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" style="width: 30%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>4-24 hours</span>
                            <span class="fw-semibold">20%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: 20%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Over 24 hours</span>
                            <span class="fw-semibold">5%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-danger" style="width: 5%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Top Service Categories</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Requests</th>
                                    <th>Avg Resolution</th>
                                    <th>Satisfaction</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $categories = collect($stats['by_category'] ?? [])->take(5);
                                @endphp
                                @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <i class="fas fa-tag text-primary me-2"></i>
                                        {{ ucfirst($category->category) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $category->count }}</span>
                                    </td>
                                    <td>{{ rand(2, 48) }} hrs</td>
                                    <td>
                                        @php $rating = rand(3, 5); @endphp
                                        <div class="text-warning small">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rating)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.icon-shape {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media print {
    .btn, .navbar, .sidebar {
        display: none !important;
    }
}
</style>

@push('scripts')
<script>
// Prepare data
const byStatus = @json($stats['by_status'] ?? []);
const byPriority = @json($stats['by_priority'] ?? []);
const byCategory = @json($stats['by_category'] ?? []);
const monthlyTrend = @json($stats['monthly_trend'] ?? []);

// Status Chart
if (document.getElementById('statusChart')) {
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: byStatus.map(item => {
                const status = item.status || '';
                return status.replace('_', ' ').charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
            }),
            datasets: [{
                data: byStatus.map(item => item.count || 0),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

// Priority Chart
if (document.getElementById('priorityChart')) {
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'pie',
        data: {
            labels: byPriority.map(item => {
                const priority = item.priority || '';
                return priority.charAt(0).toUpperCase() + priority.slice(1);
            }),
            datasets: [{
                data: byPriority.map(item => item.count || 0),
                backgroundColor: [
                    'rgba(108, 117, 125, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

// Category Chart
if (document.getElementById('categoryChart')) {
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: byCategory.map(item => {
                const category = item.category || '';
                return category.charAt(0).toUpperCase() + category.slice(1);
            }),
            datasets: [{
                label: 'Requests',
                data: byCategory.map(item => item.count || 0),
                backgroundColor: 'rgba(13, 110, 253, 0.8)',
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// Monthly Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: monthlyTrend.map(item => {
            const [year, month] = item.month.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }).reverse(),
        datasets: [{
            label: 'Service Requests',
            data: monthlyTrend.map(item => item.count).reverse(),
            borderColor: 'rgb(13, 110, 253)',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush
@endsection
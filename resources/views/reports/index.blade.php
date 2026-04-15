@extends('layouts.app')


@section('content')
    <div class="page-header">
        <h1><i class="bi bi-bar-chart" style="margin-right: 8px;"></i>Absence Reports</h1>
        <p>Analytics and statistics on student absences</p>
    </div>

    <!-- Date Range Filter -->
    <div class="card" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <label style="font-weight: 600;">View data for:</label>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('reports.index', ['range' => 7]) }}" 
                   class="btn {{ $dateRange == 7 ? '' : 'btn-secondary' }}"
                   style="padding: 8px 16px; font-size: 13px;">7 Days</a>
                <a href="{{ route('reports.index', ['range' => 30]) }}" 
                   class="btn {{ $dateRange == 30 ? '' : 'btn-secondary' }}"
                   style="padding: 8px 16px; font-size: 13px;">30 Days</a>
                <a href="{{ route('reports.index', ['range' => 90]) }}" 
                   class="btn {{ $dateRange == 90 ? '' : 'btn-secondary' }}"
                   style="padding: 8px 16px; font-size: 13px;">90 Days</a>
                <a href="{{ route('reports.index', ['range' => 365]) }}" 
                   class="btn {{ $dateRange == 365 ? '' : 'btn-secondary' }}"
                   style="padding: 8px 16px; font-size: 13px;">1 Year</a>
            </div>
        </div>
    </div>

    <!-- Key Statistics Cards -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-value">{{ $absenceStats['total'] }}</div>
            <div class="stat-label">Total Absences</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #10b981;">{{ $absenceStats['approved'] }}</div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #f59e0b;">{{ $absenceStats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #ef4444;">{{ $absenceStats['rejected'] }}</div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 20px; margin-bottom: 20px;">
        
        <!-- Absence by Status Pie Chart -->
        <div class="card">
            <div class="card-title"><i class="bi bi-pie-chart" style="margin-right: 8px;"></i>Absence Status Distribution</div>
            <div style="position: relative; height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Approval Rate Doughnut Chart -->
        <div class="card">
            <div class="card-title"><i class="bi bi-check-circle" style="margin-right: 8px;"></i>Approval Status</div>
            <div style="position: relative; height: 300px;">
                <canvas id="approvalChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Daily Absence Trend Chart -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-title"><i class="bi bi-graph-up" style="margin-right: 8px;"></i>Daily Absence Trend</div>
        <div style="position: relative; height: 350px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>



    <!-- Detailed Statistics Table -->
    <div class="card" style="margin-top: 30px;">
        <div class="card-title"><i class="bi bi-list" style="margin-right: 8px;"></i>Summary Statistics</div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="padding: 15px; border: 1px solid #eee; border-radius: 6px;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">TOTAL ABSENCES</div>
                <div style="font-size: 28px; font-weight: bold; color: #f97316;">{{ $absenceStats['total'] }}</div>
            </div>
            <div style="padding: 15px; border: 1px solid #eee; border-radius: 6px;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">APPROVAL RATE</div>
                <div style="font-size: 28px; font-weight: bold; color: #10b981;">{{ $absenceStats['approvalRate'] }}%</div>
            </div>
            <div style="padding: 15px; border: 1px solid #eee; border-radius: 6px;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">PENDING REVIEWS</div>
                <div style="font-size: 28px; font-weight: bold; color: #f59e0b;">{{ $absenceStats['pending'] }}</div>
            </div>
            <div style="padding: 15px; border: 1px solid #eee; border-radius: 6px;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">REJECTION RATE</div>
                <div style="font-size: 28px; font-weight: bold; color: #ef4444;">
                    @if($absenceStats['total'] > 0)
                        {{ round(($absenceStats['rejected'] / $absenceStats['total']) * 100, 2) }}%
                    @else
                        0%
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Color scheme
        const colors = {
            primary: '#f97316',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#06b6d4',
        };

        // Status Distribution Pie Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($absenceByStatus['labels']) !!},
                datasets: [{
                    data: {!! json_encode($absenceByStatus['data']) !!},
                    backgroundColor: [
                        colors.warning,
                        colors.success,
                        colors.danger,
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#666', font: { size: 13 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed ?? 0) / sum * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Approval Rate Chart
        const approvalCtx = document.getElementById('approvalChart').getContext('2d');
        new Chart(approvalCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($approvalRateData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($approvalRateData['data']) !!},
                    backgroundColor: [
                        colors.success,
                        colors.danger,
                        colors.warning,
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#666', font: { size: 13 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed ?? 0) / sum * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Daily Trend Line Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyAbsenceData['dates']) !!},
                datasets: [{
                    label: 'Daily Absences',
                    data: {!! json_encode($dailyAbsenceData['data']) !!},
                    borderColor: colors.primary,
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#666', font: { size: 13 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#666' },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        title: { display: true, text: 'Number of Absences', color: '#333' }
                    },
                    x: {
                        ticks: { color: '#666' },
                        grid: { display: false }
                    }
                }
            }
        });


    </script>
@endsection

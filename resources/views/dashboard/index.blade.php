@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-hand-thumbs-up" style="margin-right: 8px;"></i>Welcome, {{ $user->name }}!</h1>
        <p>Student Internship Attendance & Logbook System</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Today's Attendance</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Total Hours</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Logbook Entries</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Pending Tasks</div>
        </div>
    </div>

    <div class="card">
        <div class="card-title"><i class="bi bi-graph-up" style="margin-right: 8px;"></i>Absence Chart (Last 30 Days)</div>
        <div style="position: relative; height: 300px; margin-bottom: 20px;">
            <canvas id="absenceChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('absenceChart').getContext('2d');
        const absenceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($absenceData['dates']) !!},
                datasets: [{
                    label: 'Number of Absences',
                    data: {!! json_encode($absenceData['absences']) !!},
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#666',
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        }
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#666'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Number of Students',
                            color: '#333'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#666'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="px-6 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Dashboard Kerusakan</h1>
                <p class="text-slate-500 mt-1">Monitoring trend dan distribusi defect produksi</p>
            </div>

            <form method="GET" action="{{ route('dashboard.defects') }}"
                class="flex bg-white rounded-lg shadow-sm border border-slate-200 p-1">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                    class="border-none text-sm focus:ring-0 text-slate-600 rounded-l-md">
                <span class="flex items-center text-slate-400 px-2">-</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                    class="border-none text-sm focus:ring-0 text-slate-600">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-md text-sm font-medium transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <!-- Line Chart: Weekly Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
            <h3 class="text-lg font-bold text-slate-700 mb-6 flex items-center gap-2">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-chart-line"></i></span>
                Trend Kerusakan Mingguan (Week {{ $startDate->weekOfYear }} - {{ $endDate->weekOfYear }})
            </h3>
            <div class="relative h-80">
                <canvas id="weeklyTrendChart"></canvas>
            </div>
        </div>

        <!-- Donut Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Chart 1: By Type -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col">
                <h3 class="text-md font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100">
                    Distribusi per Jenis Kerusakan
                </h3>
                <div class="relative h-64 flex-1">
                    <canvas id="chartByType"></canvas>
                </div>
            </div>

            <!-- Chart 2: By Department -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col">
                <h3 class="text-md font-bold text-slate-700 mb-4 pb-3 border-b border-slate-100">
                    Distribusi per Departemen
                </h3>
                <div class="relative h-64 flex-1">
                    <canvas id="chartByDept"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        // Common Options
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Weekly Trend Line Chart
        new Chart(document.getElementById('weeklyTrendChart'), {
            type: 'line',
            data: {
                labels: @json($lineChartLabels),
                datasets: [
                    {
                        label: 'Total PCS',
                        data: @json($lineChartPcs),
                        borderColor: '#3b82f6', // Blue
                        backgroundColor: '#3b82f6',
                        yAxisID: 'y',
                        tension: 0.3,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Total Tonase (Ton)',
                        data: @json($lineChartKg),
                        borderColor: '#f97316', // Orange
                        backgroundColor: '#f97316',
                        yAxisID: 'y1',
                        tension: 0.3,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                    tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        min: 0,
                        title: { display: true, text: 'PCS', color: '#3b82f6', font: { weight: 'bold' } },
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0 }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        min: 0,
                        title: { display: true, text: 'Tonase', color: '#f97316', font: { weight: 'bold' } },
                        grid: { drawOnChartArea: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: false,
                            font: { size: 10 }
                        }
                    }
                }
            }
        });

        // 2. Donut Charts
        const donutColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1'];

        // Chart By Type
        new Chart(document.getElementById('chartByType'), {
            type: 'doughnut',
            data: {
                labels: @json($chartByType['labels']),
                datasets: [{
                    data: @json($chartByType['data']),
                    backgroundColor: donutColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 10, padding: 15, font: { size: 11 } } }
                },
                layout: { padding: 20 },
                cutout: '65%'
            }
        });

        // Chart By Dept
        new Chart(document.getElementById('chartByDept'), {
            type: 'doughnut',
            data: {
                labels: @json($chartByDept['labels']),
                datasets: [{
                    data: @json($chartByDept['data']),
                    backgroundColor: donutColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 10, padding: 15, font: { size: 11 } } }
                },
                layout: { padding: 20 },
                cutout: '65%'
            }
        });
    </script>
@endsection
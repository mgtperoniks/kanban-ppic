@extends('layouts.app')

@section('top_bar')
    <div class="flex items-center justify-between w-full">
        <div>
            <h1 class="text-lg font-bold text-slate-800 leading-tight">Dashboard Kerusakan</h1>
            <p class="text-gray-500 text-[10px]">Monitoring trend dan distribusi defect produksi</p>
        </div>

        <form method="GET" action="{{ route('dashboard.defects') }}"
            class="flex items-center space-x-2 bg-white rounded-lg shadow-sm border border-slate-200 p-1">
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                class="border-none text-[11px] focus:ring-0 text-slate-600 px-2 py-1 bg-slate-50 rounded">
            <span class="text-slate-400 text-xs">-</span>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                class="border-none text-[11px] focus:ring-0 text-slate-600 px-2 py-1 bg-slate-50 rounded">
            
            <div class="h-4 border-l border-slate-200 mx-1"></div>

            <select name="department" class="border-none text-[11px] focus:ring-0 text-slate-600 px-2 py-1 max-w-[130px] bg-slate-50 rounded">
                <option value="">Semua Departemen</option>
                @foreach($departmentsList as $dept)
                    <option value="{{ $dept }}" {{ $selectedDepartment === $dept ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $dept)) }}
                    </option>
                @endforeach
            </select>
            
            <select name="defect_type" class="border-none text-[11px] focus:ring-0 text-slate-600 px-2 py-1 max-w-[130px] bg-slate-50 rounded">
                <option value="">Semua Kerusakan</option>
                @foreach($defectTypesList as $type)
                    <option value="{{ $type }}" {{ $selectedDefectType === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-[11px] font-bold transition-colors">
                Filter
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="px-2 py-4">

        <div class="grid grid-cols-1 gap-6 mb-8">
            <!-- Line Chart: Weekly Trend PCS -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-700 mb-6 flex items-center gap-2">
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-chart-line"></i></span>
                    Trend Kerusakan Mingguan - PCS (Week {{ $startDate->weekOfYear }} - {{ $endDate->weekOfYear }})
                </h3>
                <div class="relative h-64">
                    <canvas id="weeklyTrendChartPcs"></canvas>
                </div>
            </div>

            <!-- Line Chart: Weekly Trend Berat -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-700 mb-6 flex items-center gap-2">
                    <span class="p-2 bg-orange-50 text-orange-500 rounded-lg"><i class="fas fa-weight-hanging"></i></span>
                    Trend Kerusakan Mingguan - Berat (Week {{ $startDate->weekOfYear }} - {{ $endDate->weekOfYear }})
                </h3>
                <div class="relative h-64">
                    <canvas id="weeklyTrendChartKg"></canvas>
                </div>
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
        const pcsData = @json($lineChartPcs);
        const kgData = @json($lineChartKg);

        const maxPcsValue = Math.max(...pcsData, 10);
        const maxTonValue = Math.max(...kgData, 0.01);

        // Chart 1: PCS
        new Chart(document.getElementById('weeklyTrendChartPcs'), {
            type: 'line',
            data: {
                labels: @json($lineChartLabels),
                datasets: [
                    {
                        label: 'Total PCS',
                        data: pcsData,
                        borderColor: '#3b82f6', // Blue
                        backgroundColor: '#3b82f6',
                        yAxisID: 'y',
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
                        suggestedMax: maxPcsValue * 1.1,
                        title: { display: true, text: 'PCS', color: '#3b82f6', font: { weight: 'bold' } },
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0 }
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

        // Chart 2: Berat (KG)
        new Chart(document.getElementById('weeklyTrendChartKg'), {
            type: 'line',
            data: {
                labels: @json($lineChartLabels),
                datasets: [
                    {
                        label: 'Total Berat (KG)',
                        data: kgData,
                        borderColor: '#f97316', // Orange
                        backgroundColor: '#f97316',
                        yAxisID: 'y',
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
                        suggestedMax: maxTonValue * 1.1,
                        title: { display: true, text: 'Berat (KG)', color: '#f97316', font: { weight: 'bold' } },
                        grid: { color: '#f1f5f9' }
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

        const donutOptions = (unit) => ({
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 10,
                        padding: 15,
                        font: { size: 11, weight: 'bold' },
                        generateLabels: function (chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                const dataset = data.datasets[0];
                                const total = dataset.data.reduce((acc, val) => acc + val, 0);
                                return data.labels.map((label, i) => {
                                    const value = dataset.data[i];
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.backgroundColor[i],
                                        lineWidth: 0,
                                        hidden: isNaN(dataset.data[i]) || chart.getDatasetMeta(0).data[i].hidden,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const dataset = context.chart.data.datasets[0];
                            const total = dataset.data.reduce((acc, val) => acc + val, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return ` ${label}: ${value.toLocaleString()} ${unit} (${percentage}%)`;
                        }
                    }
                }
            },
            layout: { padding: { right: 20 } },
            cutout: '65%'
        });

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
            options: donutOptions('PCS')
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
            options: donutOptions('PCS')
        });
    </script>
@endsection
@extends('layouts.app')

@section('content')
<div class="p-6 h-full overflow-y-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard Produksi</h1>
    <p class="text-gray-500 mb-6">Monitoring distribusi dan perpindahan produksi</p>

    <!-- Top Stats Cards -->
    <div class="grid grid-cols-6 gap-4 mb-6">
        @foreach($depts as $dept)
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="font-bold text-gray-700 mb-2">{{ ucfirst($dept) }}</h3>
            <div class="flex justify-between items-end">
                <div class="text-gray-500 text-xs">PCS:</div>
                <div class="font-bold text-lg">{{ number_format($stats[$dept]['pcs']) }}</div>
            </div>
            <div class="flex justify-between items-end">
                <div class="text-gray-500 text-xs">KG:</div>
                <div class="font-bold text-green-600">{{ number_format($stats[$dept]['kg']) }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Row 1: Distribution -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold text-gray-700 mb-4">Distribusi PCS per Departemen</h3>
            <div class="relative h-64">
                <canvas id="distPcsChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold text-gray-700 mb-4">Distribusi KG per Departemen</h3>
            <div class="relative h-64">
                <canvas id="distKgChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Movement History -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold text-gray-700 mb-4">Perpindahan Produksi (PCS) - 7 Hari</h3>
            <div class="relative h-64">
                <canvas id="movePcsChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold text-gray-700 mb-4">Perpindahan Produksi (KG) - 7 Hari</h3>
            <div class="relative h-64">
                <canvas id="moveKgChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const depts = @json($depts);
    const stats = @json($stats);
    const dates = @json($dates);
    const lineStats = @json($lineStats);

    // Helpers
    const formatDepts = depts.map(d => d.charAt(0).toUpperCase() + d.slice(1));
    const dataPcs = depts.map(d => stats[d].pcs);
    const dataKg = depts.map(d => stats[d].kg);

    // Distribution Charts (Donut)
    new Chart(document.getElementById('distPcsChart'), {
        type: 'doughnut',
        data: {
            labels: formatDepts,
            datasets: [{
                data: dataPcs,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('distKgChart'), {
        type: 'doughnut',
        data: {
            labels: formatDepts,
            datasets: [{
                data: dataKg,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Movement Charts (Line)
    const lineColors = { 1: '#3b82f6', 2: '#10b981', 3: '#f59e0b', 4: '#ef4444' };
    
    function createLineConfig(label, dataSet) {
        return {
            type: 'line',
            data: {
                labels: dates,
                datasets: [1, 2, 3, 4].map(line => ({
                    label: 'Line ' + line,
                    data: dataSet[line],
                    borderColor: lineColors[line],
                    tension: 0.3,
                    fill: false
                }))
            },
            options: { responsive: true, maintainAspectRatio: false }
        };
    }

    new Chart(document.getElementById('movePcsChart'), createLineConfig('PCS', lineStats.pcs));
    new Chart(document.getElementById('moveKgChart'), createLineConfig('KG', lineStats.kg));

</script>
@endsection

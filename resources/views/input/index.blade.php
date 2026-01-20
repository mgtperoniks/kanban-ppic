@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Index Input {{ ucfirst($dept) }}</h1>
            <p class="text-gray-500">Daftar input harian per tanggal</p>
        </div>
        <a href="{{ route('input.create', $dept) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
            <i class="fas fa-plus mr-2"></i> Bulk Import Baru
        </a>
    </div>

    <div class="space-y-4">
        @forelse($dailyStats as $stat)
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
            <a href="{{ route('input.show', ['dept' => $dept, 'date' => $stat->date]) }}" class="block">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-gray-400"></i> 
                            {{ \Carbon\Carbon::parse($stat->date)->isoFormat('dddd, D MMMM Y') }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1 flex gap-4">
                            <span><i class="fas fa-box text-blue-500"></i> Total: {{ $stat->total_pcs }} pcs</span>
                            <span><i class="fas fa-weight-hanging text-green-500"></i> Total: {{ $stat->total_kg }} kg</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $stat->items_count }} item diinput
                        </div>
                    </div>
                    <div class="text-gray-400">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="text-center py-10 text-gray-500 bg-white rounded-lg shadow">
            <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
            <p>Belum ada data input untuk departemen {{ ucfirst($dept) }}.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

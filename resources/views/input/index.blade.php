@extends('layouts.app')

@section('top_bar')
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between w-full gap-4">
        <div>
            <h1 class="text-lg font-bold text-gray-800 leading-tight">Index Input {{ ucfirst($dept) }}</h1>
            <p class="text-gray-500 text-[10px]">Daftar input harian per tanggal</p>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <form action="{{ route('input.index', $dept) }}" method="GET" class="flex-grow md:flex-grow-0 relative">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Code / Heat No..."
                    class="w-full md:w-64 border border-gray-300 rounded-md py-1.5 px-3 text-sm focus:ring-blue-500 focus:border-blue-500" />
                @if(!empty($search))
                    <a href="{{ route('input.index', $dept) }}"
                        class="absolute right-2 top-1.5 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('input.create', $dept) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded shadow text-xs flex items-center gap-2 shrink-0">
                <i class="fas fa-plus"></i> Bulk Import Baru
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="p-0">

        @if(!empty($search))
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-semibold text-gray-700">Hasil Pencarian: "{{ $search }}"</h2>
                </div>
                @if($searchResults->isEmpty())
                    <div class="bg-white rounded-lg shadow p-4 text-center text-gray-500">
                        <i class="fas fa-search text-3xl mb-2 text-gray-300"></i>
                        <p>Tidak ada data ditemukan untuk pencarian tersebut.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($searchResults as $item)
                            <div class="bg-white rounded-lg shadow border-l-4 border-blue-500 overflow-hidden">
                                <div class="p-3 bg-gray-50 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                    <div>
                                        <div class="font-bold text-blue-700">{{ $item->item_code }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->item_name }}</div>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <div class="text-xs font-semibold text-gray-600">Code: <span
                                                class="text-gray-800">{{ $item->code ?? '-' }}</span></div>
                                        <div class="text-xs font-semibold text-gray-600">Heat No: <span
                                                class="text-gray-800">{{ $item->heat_number ?? '-' }}</span></div>
                                        <div class="text-xs mt-1">
                                            Status: <span
                                                class="px-2 py-0.5 rounded text-white bg-blue-500">{{ ucfirst($item->current_dept) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 text-xs">
                                    <h3 class="font-semibold text-gray-700 mb-2">Riwayat Proses:</h3>
                                    @if(isset($item->all_histories) ? $item->all_histories->isEmpty() : $item->histories->isEmpty())
                                        <span class="text-gray-400">Belum ada riwayat pergerakan.</span>
                                    @else
                                        <div class="flex flex-wrap items-center gap-2">
                                            @php
                                                $historiesToDisplay = isset($item->all_histories) ? $item->all_histories : $item->histories;
                                                $sortedHistories = $historiesToDisplay->sortBy('moved_at')->values();
                                            @endphp

                                            @foreach($sortedHistories as $index => $history)
                                                <div
                                                    class="bg-blue-50 text-blue-800 border border-blue-200 rounded px-2 py-1.5 flex flex-col relative">
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <span class="font-bold">{{ ucfirst($history->from_dept) }}</span>
                                                        <i class="fas fa-arrow-right text-blue-400 text-[10px]"></i>
                                                        <span class="font-bold">{{ ucfirst($history->to_dept) }}</span>
                                                        @if($history->qty_pcs)
                                                            <span class="text-gray-500 font-normal ml-1">({{ $history->qty_pcs }}pcs)</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-[10px] text-blue-600 mt-1">
                                                        <i class="far fa-calendar-alt"></i>
                                                        {{ \Carbon\Carbon::parse($history->item->production_date ?? $history->moved_at)->format('d M y') }}
                                                    </div>
                                                </div>

                                                @if($index < count($sortedHistories) - 1)
                                                    <div class="text-gray-300">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <hr class="my-6 border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700 mb-2">Riwayat Input Harian</h2>
        @endif

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
                                    <span><i class="fas fa-weight-hanging text-green-500"></i> Total: {{ $stat->total_kg }}
                                        kg</span>
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
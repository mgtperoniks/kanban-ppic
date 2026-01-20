@extends('layouts.app')

@section('content')
<div class="h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-white shadow px-6 py-4 flex justify-between items-center z-10">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Departemen {{ ucfirst($dept) }}</h1>
            <div class="text-sm text-gray-500 mt-1">
                <span class="font-bold text-blue-600"><i class="fas fa-box"></i> {{ $totalPcs }} pcs</span>
                <span class="mx-2">|</span>
                <span class="font-bold text-green-600"><i class="fas fa-weight-hanging"></i> {{ $totalKg }} kg</span>
            </div>
        </div>
        
        @if($nextDept)
        <button onclick="submitMove()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow flex items-center gap-2">
            Proses ke {{ ucfirst($nextDept) }} <i class="fas fa-arrow-right"></i>
        </button>
        @else
        <span class="bg-gray-200 text-gray-600 py-2 px-4 rounded">End of Line</span>
        @endif
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 overflow-x-auto p-6 bg-gray-100">
        <form id="moveForm" action="{{ route('kanban.move') }}" method="POST">
            @csrf
            <input type="hidden" name="to_dept" value="{{ $nextDept }}">
            
            <div class="grid grid-cols-4 gap-6 h-full min-w-[1200px]">
                @foreach($lines as $lineNum => $items)
                <div class="flex flex-col h-full bg-white rounded-lg shadow-sm border border-gray-200">
                    <!-- Column Header -->
                    <div class="bg-blue-600 text-white px-4 py-3 rounded-t-lg flex justify-between items-center">
                        <h3 class="font-bold">Line {{ $lineNum }}</h3>
                        <div class="text-xs opacity-90">
                            {{ $items->sum('qty_pcs') }} pcs | {{ $items->sum('weight_kg') }} kg
                        </div>
                    </div>

                    <!-- Items Container -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-3 bg-gray-50">
                        @foreach($items as $item)
                            @php
                                $colorClass = 'border-l-4 ';
                                if($item->aging_color == 'green') $colorClass .= 'border-green-500 bg-green-50';
                                elseif($item->aging_color == 'yellow') $colorClass .= 'border-yellow-500 bg-yellow-50';
                                elseif($item->aging_color == 'orange') $colorClass .= 'border-orange-500 bg-orange-50';
                                else $colorClass .= 'border-red-500 bg-red-50';
                            @endphp

                            <div class="relative bg-white p-3 rounded shadow-sm border border-gray-200 {{ $colorClass }} text-sm hover:shadow-md transition-shadow">
                                <div class="absolute top-3 right-3">
                                    <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                </div>
                                
                                <div class="font-bold text-gray-800 mb-1">{{ $item->item_name }}</div>
                                <div class="text-xs text-gray-500 mb-2"># {{ $item->heat_number }}</div>
                                
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="text-gray-600">
                                        <i class="fas fa-layer-group text-blue-400"></i> {{ $item->qty_pcs }}
                                    </div>
                                    <div class="text-gray-600">
                                        <i class="fas fa-weight text-green-400"></i> {{ $item->weight_kg }}
                                    </div>
                                </div>

                                <div class="mt-2 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                                    <span class="text-gray-400">Aging:</span>
                                    <span class="font-bold 
                                        @if($item->aging_days < 5) text-green-600 
                                        @elseif($item->aging_days <= 14) text-yellow-600 
                                        @elseif($item->aging_days <= 30) text-orange-600 
                                        @else text-red-600 @endif">
                                        {{ number_format($item->aging_days, 1) }} hari
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </form>
    </div>
</div>

<script>
function submitMove() {
    const checked = document.querySelectorAll('input[name="item_ids[]"]:checked');
    if (checked.length === 0) {
        alert('Pilih item terlebih dahulu!');
        return;
    }
    if (confirm('Pindahkan ' + checked.length + ' item ke {{ ucfirst($nextDept) }}?')) {
        document.getElementById('moveForm').submit();
    }
}
</script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-6 h-full flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Rencana Produksi</h1>
            <a href="{{ route('plan.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition-all">
                <i class="fas fa-plus mr-2"></i> Tambah Rencana
            </a>
        </div>

        <div class="flex-1 overflow-auto">
            <table class="min-w-full border-collapse border border-gray-200 text-sm">
                <thead class="bg-gray-100 sticky top-0">
                    <tr>
                        <th class="border border-gray-200 px-3 py-2 text-left">Line</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">Customer</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">P.O. Number</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">Item Name</th>
                        <th class="border border-gray-200 px-3 py-2 text-center">Planned</th>
                        <th class="border border-gray-200 px-3 py-2 text-center">Remaining</th>
                        <th class="border border-gray-200 px-3 py-2 text-center">Status</th>
                        <th class="border border-gray-200 px-3 py-2 text-center">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-200 px-3 py-2 font-bold text-blue-600 text-center">
                                {{ $plan->line_number }}</td>
                            <td class="border border-gray-200 px-3 py-2 uppercase">{{ $plan->customer }}</td>
                            <td class="border border-gray-200 px-3 py-2 font-mono">{{ $plan->po_number }}</td>
                            <td class="border border-gray-200 px-3 py-2">
                                <div class="font-bold">{{ $plan->item_name }}</div>
                                <div class="text-[10px] text-gray-500">{{ $plan->item_code }} | {{ $plan->aisi }} |
                                    {{ $plan->size }}</div>
                            </td>
                            <td class="border border-gray-200 px-3 py-2 text-center font-bold">
                                {{ number_format($plan->qty_planned) }}</td>
                            <td class="border border-gray-200 px-3 py-2 text-center font-bold text-orange-600">
                                {{ number_format($plan->qty_remaining) }}</td>
                            <td class="border border-gray-200 px-3 py-2 text-center">
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase 
                                    {{ $plan->status == 'planning' ? 'bg-gray-100 text-gray-600' : ($plan->status == 'active' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600') }}">
                                    {{ $plan->status }}
                                </span>
                            </td>
                            <td class="border border-gray-200 px-3 py-2 text-center text-gray-500 text-[11px]">
                                {{ $plan->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-gray-200 px-3 py-8 text-center text-gray-400 italic">Belum ada
                                data rencana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
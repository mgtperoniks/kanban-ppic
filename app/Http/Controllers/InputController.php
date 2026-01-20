<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InputController extends Controller
{
    public function index($dept)
    {
        // Group by Date for the "Index Harian" view
        $dailyStats = \App\Models\ProductionItem::where('current_dept', $dept)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as items_count, SUM(qty_pcs) as total_pcs, SUM(weight_kg) as total_kg')
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();

        return view('input.index', compact('dept', 'dailyStats'));
    }

    public function create($dept)
    {
        return view('input.create', compact('dept'));
    }

    public function show($dept, $date)
    {
        $items = \App\Models\ProductionItem::where('current_dept', $dept)
            ->whereDate('created_at', $date)
            ->get();

        return view('input.show', compact('dept', 'date', 'items'));
    }

    public function store(Request $request, $dept)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.heat_number' => 'required|string',
            'items.*.item_name' => 'required|string',
            'items.*.qty_pcs' => 'required|integer',
            'items.*.weight_kg' => 'required|numeric',
        ]);

        foreach ($data['items'] as $item) {
            $newItem = \App\Models\ProductionItem::create([
                'heat_number' => $item['heat_number'],
                'item_name' => $item['item_name'],
                'qty_pcs' => $item['qty_pcs'],
                'weight_kg' => $item['weight_kg'],
                'current_dept' => $dept,
                'line_number' => 1, // Default to Line 1
                'dept_entry_at' => now(),
            ]);

            \App\Models\ProductionHistory::create([
                'item_id' => $newItem->id,
                'from_dept' => null,
                'to_dept' => $dept,
                'line_number' => 1,
                'qty_pcs' => $item['qty_pcs'],
                'weight_kg' => $item['weight_kg'],
                'moved_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => count($data['items']) . ' Items Imported Successfully!', 'redirect' => route('input.index', $dept)]);
    }
}

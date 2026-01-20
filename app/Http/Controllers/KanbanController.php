<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KanbanController extends Controller
{
    public function index($dept)
    {
        $items = \App\Models\ProductionItem::where('current_dept', $dept)
            ->orderBy('line_number')
            ->orderBy('created_at')
            ->get();

        $lines = [
            1 => $items->where('line_number', 1),
            2 => $items->where('line_number', 2),
            3 => $items->where('line_number', 3),
            4 => $items->where('line_number', 4),
        ];

        // Next Department Logic
        $flow = ['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish'];
        $currentIndex = array_search($dept, $flow);
        $nextDept = ($currentIndex !== false && isset($flow[$currentIndex + 1])) ? $flow[$currentIndex + 1] : null;

        // Stats
        $totalPcs = $items->sum('qty_pcs');
        $totalKg = $items->sum('weight_kg');

        return view('kanban.index', compact('dept', 'lines', 'nextDept', 'totalPcs', 'totalKg'));
    }

    public function move(Request $request)
    {
        $data = $request->validate([
            'item_ids' => 'required|array',
            'to_dept' => 'required|string',
        ]);

        $items = \App\Models\ProductionItem::whereIn('id', $data['item_ids'])->get();
        
        foreach ($items as $item) {
            $fromDept = $item->current_dept;
            
            // Move Item
            $item->update([
                'current_dept' => $data['to_dept'],
                'dept_entry_at' => now(), // Reset aging
                // Keep line_number or reset? "Data akan otomatis masuk ke Line 1 dep Cor". 
                // Usually FIFO maintains flow, but maybe reset to Line 1 is safer if other depts have different capacities.
                // For now, I'll keep the line number to simulate "flowing through the lines" 
                // UNLESS user specified otherwise. User said "1 batch barang tadi pindah ke kanban departemen netto". 
                // I'll keep line_number for now as it's cleaner visually.
            ]);

            // Log History
            \App\Models\ProductionHistory::create([
                'item_id' => $item->id,
                'from_dept' => $fromDept,
                'to_dept' => $data['to_dept'],
                'line_number' => $item->line_number, // Capture line number it was in
                'qty_pcs' => $item->qty_pcs,
                'weight_kg' => $item->weight_kg,
                'moved_at' => now(),
            ]);
        }

        return redirect()->route('kanban.index', $data['to_dept'])->with('success', count($items) . ' Items Moved to ' . ucfirst($data['to_dept']));
    }
}

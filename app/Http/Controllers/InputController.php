<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InputController extends Controller
{
    public function index($dept)
    {
        // Group by Production Date for the "Index Harian" view
        $dailyStats = \App\Models\ProductionItem::where('current_dept', $dept)
            ->selectRaw('COALESCE(production_date, DATE(created_at)) as date, COUNT(*) as items_count, SUM(qty_pcs) as total_pcs, SUM(weight_kg) as total_kg')
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
            ->where(function ($q) use ($date) {
                $q->where('production_date', $date)
                    ->orWhere(function ($sq) use ($date) {
                        $sq->whereNull('production_date')
                            ->whereDate('created_at', $date);
                    });
            })
            ->get();

        return view('input.show', compact('dept', 'date', 'items'));
    }

    public function store(Request $request, $dept)
    {
        $data = $request->validate([
            'production_date' => 'required|date',
            'items' => 'required|array',
            'items.*.code' => 'nullable|string',
            'items.*.heat_number' => 'nullable|string',
            'items.*.item_code' => 'nullable|string',
            'items.*.item_name' => 'nullable|string',
            'items.*.qty_pcs' => 'required|integer',
            'items.*.hasil' => 'nullable|integer', // Qty being moved forward
            'items.*.rusak' => 'nullable|integer', // Qty being scrapped
            'items.*.weight_kg' => 'nullable|numeric',
            'items.*.bruto_weight' => 'nullable|numeric',
            'items.*.netto_weight' => 'nullable|numeric',
            'items.*.bubut_weight' => 'nullable|numeric',
            'items.*.finish_weight' => 'nullable|numeric',
            'items.*.po_number' => 'nullable|string',
            'items.*.customer' => 'nullable|string',
            'items.*.line_number' => 'nullable|integer',
        ]);

        $productionDate = $data['production_date'];
        $flow = ['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish', 'completed'];
        $currentIndex = array_search($dept, $flow);
        $nextDept = $flow[$currentIndex + 1] ?? 'completed';

        $processedCount = 0;
        $errors = [];

        foreach ($data['items'] as $item) {
            if ($dept === 'cor') {
                // COR INPUT: Creates new items in 'netto' from Plan
                // (Existing logic refactored for new fields)
                $plan = \App\Models\ProductionPlan::where('item_code', $item['item_code'])
                    ->where('line_number', $item['line_number'])
                    ->where('qty_remaining', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->first();

                $planId = null;
                if ($plan) {
                    $planId = $plan->id;
                    $plan->decrement('qty_remaining', $item['qty_pcs']);
                    $plan->update(['status' => ($plan->qty_remaining <= 0 ? 'completed' : 'active')]);
                }

                $newItem = \App\Models\ProductionItem::create([
                    'plan_id' => $planId,
                    'code' => $item['code'] ?? null,
                    'heat_number' => $item['heat_number'] ?? null,
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'],
                    'qty_pcs' => $item['qty_pcs'],
                    'weight_kg' => $item['weight_kg'] ?? 0,
                    'bruto_weight' => $item['bruto_weight'] ?? null,
                    'netto_weight' => $item['netto_weight'] ?? null,
                    'bubut_weight' => $item['bubut_weight'] ?? null,
                    'finish_weight' => $item['finish_weight'] ?? null,
                    'po_number' => $item['po_number'] ?? ($plan->po_number ?? null),
                    'customer' => $item['customer'] ?? ($plan->customer ?? null),
                    // User said: "Input Cor ... (maka barang ini sekarang ada didepartemen netto)"
                    'current_dept' => 'netto',
                    'line_number' => $item['line_number'],
                    'dept_entry_at' => now(),
                    'production_date' => $productionDate,
                ]);

                \App\Models\ProductionHistory::create([
                    'item_id' => $newItem->id,
                    'from_dept' => 'cor',
                    'to_dept' => 'netto',
                    'line_number' => $item['line_number'],
                    'qty_pcs' => $item['qty_pcs'],
                    'weight_kg' => $newItem->weight_kg,
                    'moved_at' => now(),
                ]);

                $processedCount++;
            } else {
                // OTHER DEPARTMENTS: Moves existing items forward
                $qtyHasil = (int) ($item['hasil'] ?? 0);
                $qtyRusak = (int) ($item['rusak'] ?? 0);
                $totalReported = $qtyHasil + $qtyRusak;

                if ($totalReported <= 0)
                    continue;

                // Find item in current department
                $sourceItem = \App\Models\ProductionItem::where('current_dept', $dept)
                    ->where('code', $item['code'])
                    ->where('heat_number', $item['heat_number'])
                    ->first();

                if (!$sourceItem) {
                    $errors[] = "Item {$item['code']} #{$item['heat_number']} tidak ditemukan di departemen " . ucfirst($dept);
                    continue;
                }

                if ($totalReported > $sourceItem->qty_pcs) {
                    $errors[] = "Item {$item['code']} #{$item['heat_number']} melebihi stok: reported $totalReported, available {$sourceItem->qty_pcs}";
                    continue;
                }

                // SUCCESS: Proceed with Split/Move
                // 1. Create new item in Next Dept
                $nextItem = $sourceItem->replicate();
                $nextItem->current_dept = $nextDept;
                $nextItem->qty_pcs = $qtyHasil;
                $nextItem->scrap_qty = 0; // Reset scrap for next stage
                $nextItem->dept_entry_at = now();
                $nextItem->production_date = $productionDate;

                // Update weights if provided
                if ($item['bubut_weight'])
                    $nextItem->bubut_weight = $item['bubut_weight'];
                if ($item['finish_weight'])
                    $nextItem->finish_weight = $item['finish_weight'];
                if ($item['weight_kg'])
                    $nextItem->weight_kg = $item['weight_kg'];

                $nextItem->save();

                // 2. Update Source Item
                $sourceItem->decrement('qty_pcs', $totalReported);
                $sourceItem->increment('scrap_qty', $qtyRusak);

                // Log History
                \App\Models\ProductionHistory::create([
                    'item_id' => $sourceItem->id,
                    'from_dept' => $dept,
                    'to_dept' => $nextDept,
                    'line_number' => $sourceItem->line_number,
                    'qty_pcs' => $qtyHasil,
                    'weight_kg' => $nextItem->weight_kg,
                    'moved_at' => now(),
                ]);

                $processedCount++;
            }
        }

        return response()->json([
            'success' => $processedCount > 0,
            'processed' => $processedCount,
            'errors' => $errors,
            'message' => $processedCount . " Heat Number berhasil diproses. " . (count($errors) > 0 ? count($errors) . " gagal." : ""),
            'redirect' => route('input.index', $dept)
        ]);
    }
}

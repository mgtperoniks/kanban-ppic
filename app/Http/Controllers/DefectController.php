<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ProductionItem;
use App\Models\DefectType;
use App\Models\ProductionDefect;
use Illuminate\Support\Facades\DB;

class DefectController extends Controller
{
    public function index($dept)
    {
        // Get items in this department that have scrap_qty > 0
        // And haven't been fully accounted for in production_defects
        // For simplicity, we just look for recent items with scrap_qty > 0

        $items = ProductionItem::where('current_dept', $dept)
            ->where('scrap_qty', '>', 0)
            ->orderByDesc('production_date')
            ->orderByDesc('created_at')
            ->with(['defects.defectType']) // Eager load existing defects
            ->paginate(20);

        $defectTypes = DefectType::where('department', $dept)->active()->get();

        return view('input.defect.index', compact('dept', 'items', 'defectTypes'));
    }

    public function store(Request $request, ProductionItem $item)
    {
        $request->validate([
            'defects' => 'required|array',
            'defects.*.defect_type_id' => 'required|exists:defect_types,id',
            'defects.*.qty' => 'required|integer|min:1',
            'defects.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $item) {
            // Remove existing defects for this item to allow "sync" style update
            // Or typically we might just add new ones. 
            // Let's go with: delete all existing defects for this item and re-create.
            $item->defects()->delete();

            $totalLogged = 0;
            foreach ($request->defects as $defectData) {
                if ($defectData['qty'] > 0) {
                    $item->defects()->create([
                        'defect_type_id' => $defectData['defect_type_id'],
                        'qty' => $defectData['qty'],
                        'notes' => $defectData['notes'] ?? null,
                    ]);
                    $totalLogged += $defectData['qty'];
                }
            }

            // Optional: Validation that totalLogged <= $item->scrap_qty
            // For now, we allow flexibility but maybe warn in UI
        });

        return back()->with('success', 'Detail kerusakan berhasil disimpan.');
    }
}

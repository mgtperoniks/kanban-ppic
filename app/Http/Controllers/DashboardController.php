<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Live Stock Stats
        $stock = \App\Models\ProductionItem::selectRaw('current_dept, SUM(qty_pcs) as total_pcs, SUM(weight_kg) as total_kg')
            ->groupBy('current_dept')
            ->get()
            ->keyBy('current_dept');

        $depts = ['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish'];
        $stats = [];
        foreach ($depts as $dept) {
            $stats[$dept] = [
                'pcs' => $stock[$dept]->total_pcs ?? 0,
                'kg' => $stock[$dept]->total_kg ?? 0,
            ];
        }

        // 2. Movement History (Last 7 Days)
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('Y-m-d');
        }

        // Fetch history
        $movements = \App\Models\ProductionHistory::where('moved_at', '>=', now()->subDays(7))
            ->get();

        // Stages to track (6 movements)
        $stages = [
            'netto' => 'Hasil Cor',
            'bubut_od' => 'Hasil Netto',
            'bubut_cnc' => 'Hasil Bubut OD',
            'bor' => 'Hasil Bubut CNC',
            'finish' => 'Hasil Bor',
            'completed' => 'Hasil Finish',
        ];

        $lineStats = [
            'pcs' => [],
            'kg' => [],
        ];

        foreach ($stages as $stageKey => $stageName) {
            $lineStats['pcs'][$stageName] = [];
            $lineStats['kg'][$stageName] = [];

            foreach ($dates as $date) {
                $dayMoves = $movements->filter(function ($item) use ($date, $stageKey) {
                    return $item->moved_at->format('Y-m-d') === $date && $item->to_dept === $stageKey;
                });

                $lineStats['pcs'][$stageName][] = $dayMoves->sum('qty_pcs');
                $lineStats['kg'][$stageName][] = $dayMoves->sum('weight_kg');
            }
        }

        return view('dashboard', compact('stats', 'depts', 'dates', 'lineStats', 'stages'));
    }

    public function getChartData()
    {
        // ... AJAX structure if needed, or pass in view.
        // I will do migration first if I want to be 100% accurate.
    }
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductionItem;
use App\Models\ProductionHistory;

$validScraps = [
    'LA206022606' => ['code' => 'ET464', 'rusak' => 1],
    'LA217022602' => ['code' => 'ET573', 'rusak' => 1],
    'P209022602' => ['code' => 'S577', 'rusak' => 2],
    'P216022609' => ['code' => 'S577', 'rusak' => 2],
    'A217022603' => ['code' => 'S585', 'rusak' => 4],
    'A217022604' => ['code' => 'S585', 'rusak' => 1],
    'A413022607' => ['code' => 'S580', 'rusak' => 7],
    'P212022604' => ['code' => 'AB34', 'rusak' => 4],
    'A218022607' => ['code' => 'AB22', 'rusak' => 4],
];

echo "Restoring valid scrap quantities...\n";

foreach ($validScraps as $heat => $data) {
    // 1. Update ProductionItem in Netto
    $item = ProductionItem::where('current_dept', 'netto')
        ->where('code', $data['code'])
        ->where('heat_number', $heat)
        ->first();

    if ($item) {
        $item->scrap_qty = $data['rusak'];
        $item->save();
        echo "Restored Item {$data['code']} #{$heat} to scrap_qty = {$data['rusak']}\n";

        // 2. Update ProductionHistory 
        // Find the history record moving from Netto to Bubut OD for this item
        $history = \Illuminate\Support\Facades\DB::table('production_histories')
            ->join('production_items as next_item', 'production_histories.item_id', '=', 'next_item.id')
            ->where('production_histories.from_dept', 'netto')
            ->where('next_item.code', $data['code'])
            ->where('next_item.heat_number', $heat)
            ->select('production_histories.id')
            ->first();

        if ($history) {
            ProductionHistory::where('id', $history->id)->update(['scrap_qty' => $data['rusak']]);
            echo "   -> Updated history ID {$history->id}\n";
        } else {
            echo "   -> Warning: No history found for this item.\n";
        }
    } else {
        echo "Error: Item {$data['code']} #{$heat} not found in Netto.\n";
    }
}
echo "Done.\n";

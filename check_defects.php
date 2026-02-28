<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Look at Netto items that have defects or scrap_qty > 0
$nettoItems = \App\Models\ProductionItem::where('current_dept', 'netto')
    ->where(function ($q) {
        $q->where('scrap_qty', '>', 0)
            ->orWhereHas('defects');
    })->with('defects')->get();

echo "Netto Items with scrap or defects: " . $nettoItems->count() . "\n";
$totalScrap = 0;
$totalDefectQty = 0;

foreach ($nettoItems as $index => $item) {
    if ($index < 10) {
        echo "ID: {$item->id}, Code: {$item->code}, Heat: {$item->heat_number}, Qty: {$item->qty_pcs}, ScrapQty: {$item->scrap_qty}\n";
        echo "  -> Defects: " . $item->defects->count() . " records, sum qty: " . $item->defects->sum('qty') . "\n";
    }
    $totalScrap += $item->scrap_qty;
    $totalDefectQty += $item->defects->sum('qty');
}

echo "\nTotal Scrap Qty in Netto items: $totalScrap\n";
echo "Total Defect Qty in Netto items: $totalDefectQty\n";

// Check if any histories exist for these items out of netto
$histories = \App\Models\ProductionHistory::where('from_dept', 'netto')->count();
echo "Total histories out of netto: $histories\n";

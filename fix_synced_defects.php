<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\DB::beginTransaction();
try {
    // Get all items in netto that have scrap_qty > 0 or defects
    $nettoItems = \App\Models\ProductionItem::where('current_dept', 'netto')
        ->where(function ($q) {
            $q->where('scrap_qty', '>', 0)
                ->orWhereHas('defects');
        })->get();

    $itemCount = $nettoItems->count();
    $defectCount = 0;

    foreach ($nettoItems as $item) {
        // Since there is no movement OUT of Netto (all deleted), 
        // scrap_qty should be 0.
        $item->scrap_qty = 0;
        $item->save();

        // Delete all associated defects
        $deleted = $item->defects()->delete();
        $defectCount += $deleted;
    }

    \Illuminate\Support\Facades\DB::commit();
    echo "Successfully cleaned up!\n";
    echo "Reset scrap_qty to 0 for $itemCount orphan items in Netto.\n";
    echo "Deleted $defectCount orphan defect records in Netto.\n";

} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\ProductionItem::with('histories')->where('code', 'AB47')->where('heat_number', 'A219022602')->get();

echo "\n--- RAW ITEMS WITH HISTORIES ---\n";
foreach ($items as $item) {
    echo "ID: {$item->id} | Dept: {$item->current_dept}\n";
    foreach ($item->histories as $h) {
        echo "   -> History: {$h->from_dept} to {$h->to_dept} ({$h->qty_pcs} pcs)\n";
    }
}

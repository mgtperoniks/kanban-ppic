<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\ProductionItem::where('code', 'AB47')->get();
echo "Total AB47 Items: " . $items->count() . "\n";
foreach ($items as $i) {
    echo "ID: {$i->id}, Code: {$i->code}, Heat: {$i->heat_number}, Dept: {$i->current_dept}\n";
    foreach ($i->histories as $h) {
        echo "  - History: {$h->from_dept} to {$h->to_dept} ({$h->qty_pcs} pcs)\n";
    }
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n--- SIMULATING CONTROLLER logic ---\n";
$rawResults = \App\Models\ProductionItem::with('histories')->where('code', 'AB47')->where('heat_number', 'A219022602')->orderByDesc('created_at')->get();

$grouped = [];
foreach ($rawResults as $item) {
    dump("Processing raw result ID: " . $item->id . " - Dept: " . $item->current_dept);

    $key = $item->code . '|' . $item->heat_number;
    if (!isset($grouped[$key])) {
        $grouped[$key] = $item;
        $grouped[$key]->all_histories = collect();
    } else {
        if ($item->updated_at > $grouped[$key]->updated_at) {
            $tempHistories = $grouped[$key]->all_histories;
            $grouped[$key] = $item;
            $grouped[$key]->all_histories = $tempHistories;
        }
    }

    $allItemIds = \App\Models\ProductionItem::where('code', $item->code)->where('heat_number', $item->heat_number)->pluck('id');
    dump("  Found item IDs for this heat: ", $allItemIds->toArray());

    $allHistories = \App\Models\ProductionHistory::whereIn('item_id', $allItemIds)->orderBy('moved_at', 'asc')->get();
    dump("  Found histories count: " . $allHistories->count());

    foreach ($allHistories as $history) {
        if (!$grouped[$key]->all_histories->contains('id', $history->id)) {
            $grouped[$key]->all_histories->push($history);
        }
    }
}

$searchResults = collect(array_values($grouped));
echo "\nResulting Card Count: " . $searchResults->count() . "\n";
$card = $searchResults->first();
if ($card) {
    echo "Card Status: {$card->current_dept}\n";
    echo "Card Histories (" . $card->all_histories->count() . "):\n";
    foreach ($card->all_histories as $h) {
        echo "  - {$h->from_dept} to {$h->to_dept}\n";
    }
}

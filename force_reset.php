<?php
use App\Models\ProductionItem;
use App\Models\ProductionHistory;
use App\Models\ProductionDefect;
use App\Models\ProductionPlan;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Forcing production data cleanup...\n";

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    $defectsCount = DB::table('production_defects')->count();
    DB::table('production_defects')->truncate();
    echo "- Truncated production_defects ($defectsCount records)\n";

    $historyCount = DB::table('production_histories')->count();
    DB::table('production_histories')->truncate();
    echo "- Truncated production_histories ($historyCount records)\n";

    $itemsCount = DB::table('production_items')->count();
    DB::table('production_items')->truncate();
    echo "- Truncated production_items ($itemsCount records)\n";

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // 2. Reset Production Plans
    $plans = ProductionPlan::all();
    foreach ($plans as $plan) {
        $plan->qty_remaining = $plan->qty_planned;
        $plan->status = 'active';
        $plan->save();
    }
    echo "- Reset " . $plans->count() . " production plans to initial state.\n";

    echo "\nAll production data forced cleared. System is now empty.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Livewire\Admin\StockReports;
use App\Models\Store;
use App\Models\Warehouse;

$c = new StockReports();
$c->filterStoreId = Store::first()?->id ?? 1;
$c->filterWarehouseId = Warehouse::first()?->id ?? 1;

try {
    $totals = $c->getStockBatchTotalByProduct();
    echo 'Total products in batch totals: ' . count($totals) . "\n";
    echo 'Total stok toko (sum): ' . $c->getTotalStokToko() . "\n";
    echo 'Totals keys: ' . implode(',', array_keys($totals)) . "\n";
    exit(0);
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

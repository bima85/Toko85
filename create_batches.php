<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$purchase = \App\Models\Purchase::find(1);
if ($purchase) {
    echo "Creating StockBatches for Purchase ID: {$purchase->id}\n";

    $store = $purchase->store_id ? \App\Models\Store::find($purchase->store_id) : null;
    $warehouse = $purchase->warehouse_id ? \App\Models\Warehouse::find($purchase->warehouse_id) : null;

    foreach ($purchase->purchaseItems as $item) {
        $destination = $item->destination_type ?? 'toko';

        if ($destination === 'toko' && $store && ($item->qty ?? 0) > 0) {
            $batchName = "Pembelian - {$purchase->no_invoice} - {$store->nama_toko}";

            \App\Models\StockBatch::create([
                'product_id' => $item->product_id,
                'type' => 'store',
                'store_id' => $purchase->store_id,
                'warehouse_id' => null,
                'name' => $batchName,
                'qty' => $item->qty,
                'status' => 'aktual',
                'created_at' => now(),
            ]);
            echo "  Created StockBatch for TOKO: name={$batchName}, qty={$item->qty}\n";
        } elseif ($destination === 'gudang' && $warehouse && ($item->qty_gudang ?? 0) > 0) {
            $batchName = "Pembelian - {$purchase->no_invoice} - {$warehouse->nama_gudang}";

            \App\Models\StockBatch::create([
                'product_id' => $item->product_id,
                'type' => 'warehouse',
                'store_id' => null,
                'warehouse_id' => $purchase->warehouse_id,
                'name' => $batchName,
                'qty' => $item->qty_gudang,
                'status' => 'aktual',
                'created_at' => now(),
            ]);
            echo "  Created StockBatch for GUDANG: name={$batchName}, qty={$item->qty_gudang}\n";
        }
    }
}

echo "\nVerifying data:\n";
$adjustments = \App\Models\StockAdjustment::where('product_id', 23)->get();
echo "Total StockAdjustments for Product 23: " . $adjustments->count() . "\n";
foreach ($adjustments as $a) {
    $loc = $a->store_id ? "TOKO ({$a->store_id})" : "GUDANG ({$a->warehouse_id})";
    echo "  - {$loc}: qty={$a->quantity}\n";
}

$stock = \App\Models\StockBatch::where('product_id', 23)->get();
echo "\nTotal StockBatches for Product 23: " . $stock->count() . "\n";
foreach ($stock as $b) {
    $type = $b->type === 'store' ? 'TOKO' : 'GUDANG';
    echo "  - {$type}: {$b->name} = {$b->qty}\n";
}

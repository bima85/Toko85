<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Delete existing batch yang salah
$batch = \App\Models\StockBatch::find(1);
if ($batch) {
    echo "Deleting incorrect StockBatch: {$batch->id}\n";
    $batch->delete();
}

// Buat ulang dengan struktur yang benar
$purchase = \App\Models\Purchase::find(1);
if ($purchase && $purchase->purchaseItems) {
    foreach ($purchase->purchaseItems as $item) {
        $destination = $item->destination_type ?? 'toko';

        if ($destination === 'toko' && $purchase->store_id && ($item->qty ?? 0) > 0) {
            $store = \App\Models\Store::find($purchase->store_id);
            $batchName = "Pembelian - {$purchase->no_invoice} - {$store->nama_toko}";

            \App\Models\StockBatch::create([
                'product_id' => $item->product_id,
                'location_type' => 'store',
                'location_id' => $purchase->store_id,
                'nama_tumpukan' => $batchName,
                'qty' => $item->qty,
                'status' => 'aktual',
                'created_at' => now(),
            ]);
            echo "Created StockBatch: product={$item->product_id}, location=store#{$purchase->store_id}, qty={$item->qty}\n";
        } elseif ($destination === 'gudang' && $purchase->warehouse_id && ($item->qty_gudang ?? 0) > 0) {
            $warehouse = \App\Models\Warehouse::find($purchase->warehouse_id);
            $batchName = "Pembelian - {$purchase->no_invoice} - {$warehouse->nama_gudang}";

            \App\Models\StockBatch::create([
                'product_id' => $item->product_id,
                'location_type' => 'warehouse',
                'location_id' => $purchase->warehouse_id,
                'nama_tumpukan' => $batchName,
                'qty' => $item->qty_gudang,
                'status' => 'aktual',
                'created_at' => now(),
            ]);
            echo "Created StockBatch: product={$item->product_id}, location=warehouse#{$purchase->warehouse_id}, qty={$item->qty_gudang}\n";
        }
    }
}

echo "\n=== VERIFICATION ===\n";
$batches = \App\Models\StockBatch::where('product_id', 23)->get();
echo "Total StockBatches for Product 23: " . $batches->count() . "\n";
foreach ($batches as $b) {
    echo "  - location_type={$b->location_type}, location_id={$b->location_id}, qty={$b->qty}, status={$b->status}\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PURCHASES ===\n";
$purchases = \App\Models\Purchase::with(['purchaseItems', 'supplier'])
    ->orderBy('id', 'desc')
    ->limit(3)
    ->get();

foreach ($purchases as $p) {
    echo "ID: {$p->id}, Invoice: {$p->no_invoice}, Supplier: {$p->supplier->nama_supplier}\n";
    echo "  Store: {$p->store_id}, Warehouse: {$p->warehouse_id}\n";
    foreach ($p->purchaseItems as $item) {
        echo "    Product: {$item->product_id}, Qty: {$item->qty}, Qty_Gudang: {$item->qty_gudang}, Destination: {$item->destination_type}\n";
    }
}

echo "\n=== STOCK ADJUSTMENTS ===\n";
$adjustments = \App\Models\StockAdjustment::orderBy('id', 'desc')->limit(10)->get();
foreach ($adjustments as $adj) {
    echo "ID: {$adj->id}, Product: {$adj->product_id}, Store: {$adj->store_id}, Warehouse: {$adj->warehouse_id}, Qty: {$adj->quantity}\n";
}

echo "\n=== STOCK BATCHES ===\n";
$batches = \App\Models\StockBatch::orderBy('id', 'desc')->limit(10)->get();
foreach ($batches as $batch) {
    echo "ID: {$batch->id}, Product: {$batch->product_id}, Type: {$batch->type}, Store: {$batch->store_id}, Warehouse: {$batch->warehouse_id}, Qty: {$batch->qty}\n";
}

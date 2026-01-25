<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\StockBatch, App\Models\Store;

// Get the first store
$store = Store::first();
echo "Store ID: " . ($store ? $store->id : 'NULL') . "\n";

// Check all StockBatch records
echo "\n=== ALL StockBatch Records ===\n";
$all = DB::table('stock_batches')->get();
echo "Total: " . count($all) . "\n";
foreach ($all as $row) {
    echo "ID: {$row->id}, Product: {$row->product_id}, Qty: {$row->qty}, location_type: {$row->location_type}, location_id: {$row->location_id}\n";
}

// Test the exact query from getStoreStocks
echo "\n=== Query Results (location_type=store, location_id=1) ===\n";
$batches = StockBatch::active()
    ->with(['product.category', 'product.subcategory'])
    ->where('location_type', 'store')
    ->where('location_id', 1)
    ->get();

echo "Results: " . count($batches) . "\n";
foreach ($batches as $batch) {
    echo "ID: {$batch->id}, Product: {$batch->product_id}, Product Name: " . ($batch->product?->nama_produk ?? 'NULL') . ", Qty: {$batch->qty}\n";
}

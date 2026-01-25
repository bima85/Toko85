<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STOCK CARDS ===\n";
$cards = \App\Models\StockCard::orderBy('id', 'desc')->limit(10)->get();
echo "Total StockCards: " . $cards->count() . "\n";
foreach ($cards as $card) {
    echo "  ID: {$card->id}, Product: {$card->product_id}, Type: {$card->type}, Qty: {$card->qty}, Ref: {$card->reference_type}#{$card->reference_id}\n";
}

echo "\n=== STOCK BATCHES ===\n";
$batches = \App\Models\StockBatch::where('product_id', 23)->get();
echo "Total for Product 23: " . $batches->count() . "\n";
foreach ($batches as $b) {
    echo "  ID: {$b->id}, location_type: " . ($b->location_type ?? 'NULL') . ", location_id: " . ($b->location_id ?? 'NULL') . ", Qty: {$b->qty}, Status: {$b->status}\n";
    // Try accessing all properties
    echo "    All Properties: ";
    foreach ((array)$b->getAttributes() as $key => $val) {
        echo "$key=" . (is_null($val) ? 'null' : $val) . " ";
    }
    echo "\n";
}

echo "\n=== STOCK ADJUSTMENTS ===\n";
$adjusts = \App\Models\StockAdjustment::where('product_id', 23)->get();
echo "Total for Product 23: " . $adjusts->count() . "\n";
foreach ($adjusts as $a) {
    echo "  ID: {$a->id}, Qty: {$a->quantity}, Store: {$a->store_id}, Warehouse: {$a->warehouse_id}\n";
}

echo "\n=== PRODUCTS ===\n";
$products = \App\Models\Product::select('id', 'nama_produk')->get();
echo "Total Products: " . $products->count() . "\n";

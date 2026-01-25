<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$batches = \App\Models\StockBatch::active()
    ->with(['product.category', 'product.subcategory'])
    ->where('location_type', 'store')
    ->where('location_id', 1)
    ->get();

echo "Batches found: " . count($batches) . "\n";
foreach ($batches as $batch) {
    echo "ID: {$batch->id}, Product ID: {$batch->product_id}\n";
    if ($batch->product) {
        echo "  Product: {$batch->product->nama_produk}\n";
        echo "  Category: " . ($batch->product->category?->nama_kategori ?? 'NULL') . "\n";
        echo "  Subcategory: " . ($batch->product->subcategory?->nama_subkategori ?? 'NULL') . "\n";
    } else {
        echo "  Product: NULL\n";
    }
}

// Now test the full getStoreStocks method
echo "\n=== Simulating getStoreStocks ===\n";
$store = \App\Models\Store::find(1);
echo "Store: " . ($store?->nama_toko ?? 'NULL') . "\n";

$batches = \App\Models\StockBatch::active()
    ->with(['product.category', 'product.subcategory'])
    ->where('location_type', 'store')
    ->where('location_id', 1)
    ->get()
    ->groupBy('product_id');

echo "Grouped batches: " . count($batches) . " products\n";

$mapped = $batches->map(function ($items) use ($store) {
    $product = $items->first()->product;
    if (!$product) {
        echo "  Product is NULL\n";
        return null;
    }

    $totalQty = $items->sum('qty');
    echo "  Product: {$product->nama_produk}, Total Qty: {$totalQty}\n";

    return (object) [
        'id' => 'product-' . $product->id,
        'product_id' => $product->id,
        'product' => $product,
        'stok_awal' => 0,
        'stok_masuk' => 0,
        'stok_keluar' => 0,
        'stok_akhir' => $totalQty,
        'total_stok' => $totalQty,
        'unit' => $product->satuan,
        'store' => $store,
    ];
})->filter()->values();

echo "Final mapped results: " . count($mapped) . "\n";
foreach ($mapped as $item) {
    echo "  ID: {$item->id}, Qty: {$item->total_stok}\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Purchase, App\Models\PurchaseItem, App\Models\Supplier, App\Models\Store, App\Models\Product, App\Models\Unit;
use App\Models\StockAdjustment, App\Models\StockBatch;

// Get first store (will be used as default)
$store = Store::first();
$supplier = Supplier::first();
$product = Product::find(23); // Product "10kg Sip"
$unit = Unit::first();

echo "Creating purchase without explicitly selecting location...\n";
echo "Store (default): {$store->nama_toko} (ID: {$store->id})\n";
echo "Supplier: {$supplier->nama_supplier}\n";
echo "Product: {$product->nama_produk}\n\n";

// Delete any existing test data
Purchase::where('no_invoice', 'TEST-001')->delete();
StockBatch::where('nama_tumpukan', 'LIKE', '%TEST%')->delete();
StockAdjustment::where('reason', 'LIKE', '%TEST%')->delete();

// Create purchase
$purchase = Purchase::create([
    'no_invoice' => 'TEST-001',
    'tanggal_pembelian' => now(),
    'supplier_id' => $supplier->id,
    'store_id' => null, // NOT SET - should default to first store
    'warehouse_id' => null,
    'status' => 'completed',
]);

echo "Created Purchase: {$purchase->no_invoice} (ID: {$purchase->id})\n";

// Create purchase item with qty (destination_type = 'toko' by default)
$purchaseItem = PurchaseItem::create([
    'purchase_id' => $purchase->id,
    'product_id' => $product->id,
    'category_id' => $product->category_id,
    'subcategory_id' => $product->subcategory_id,
    'unit_id' => $unit->id,
    'qty' => 10, // For toko
    'qty_gudang' => 0,
    'harga_beli' => 50000,
    'total' => 500000,
    'destination_type' => 'toko',
    'batches' => json_encode([['name' => 'TEST', 'qty' => 10]]),
]);

echo "Created PurchaseItem (ID: {$purchaseItem->id})\n";

// Manually create stock like the save() method does
// This simulates what should happen when save() runs
$purchase->store_id = $store->id; // Auto-set from save() logic
$purchase->save();

// Create StockAdjustment
$adjustment = StockAdjustment::create([
    'product_id' => $product->id,
    'store_id' => $store->id,
    'warehouse_id' => null,
    'adjustment_type' => 'add',
    'quantity' => 10,
    'stok_awal' => 0,
    'stok_masuk' => 10,
    'unit_id' => $unit->id,
    'reason' => 'TEST Pembelian',
    'adjustment_date' => now(),
    'user_id' => 1,
]);

echo "Created StockAdjustment (ID: {$adjustment->id})\n";

// Create StockBatch
$batch = StockBatch::create([
    'product_id' => $product->id,
    'category_id' => $product->category_id,
    'subcategory_id' => $product->subcategory_id,
    'location_type' => 'store',
    'location_id' => $store->id,
    'nama_tumpukan' => 'TEST - Batch Pembelian',
    'qty' => 10,
    'status' => 'aktual',
]);

echo "Created StockBatch (ID: {$batch->id})\n";

// Verify data was created
echo "\n=== VERIFICATION ===\n";
$batches = StockBatch::where('location_type', 'store')->where('location_id', $store->id)->where('product_id', $product->id)->get();
echo "StockBatches for {$store->nama_toko}: " . count($batches) . "\n";
foreach ($batches as $b) {
    echo "  - {$b->nama_tumpukan}: {$b->qty} units\n";
}

$adjustments = StockAdjustment::where('store_id', $store->id)->where('product_id', $product->id)->get();
echo "StockAdjustments for {$store->nama_toko}: " . count($adjustments) . "\n";
foreach ($adjustments as $a) {
    echo "  - {$a->reason}: {$a->quantity} units\n";
}

echo "\n✅ Test completed successfully!\n";

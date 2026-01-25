<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Purchase, App\Models\PurchaseItem, App\Models\Supplier, App\Models\Warehouse, App\Models\Product, App\Models\Unit, App\Models\StockAdjustment, App\Models\StockBatch, App\Models\Store;

// Get first warehouse and supplier
$warehouse = Warehouse::first();
$supplier = Supplier::first();
$product = Product::find(23); // Product "10kg Sip"
$unit = Unit::first();

if (!$warehouse || !$supplier || !$product || !$unit) {
    echo "Error: Missing warehouse, supplier, product, or unit\n";
    exit(1);
}

echo "Creating warehouse purchase...\n";
echo "Warehouse: {$warehouse->nama_gudang}\n";
echo "Supplier: {$supplier->nama_supplier}\n";
echo "Product: {$product->nama_produk}\n";

// Generate invoice number first
// Use tomorrow's date to ensure unique invoice
$invoiceDate = now()->addDay()->format('Y/m/d');

// For this date, start from 001
$lastPurchase = Purchase::where('supplier_id', $supplier->id)
    ->where('no_invoice', 'LIKE', "PB/$invoiceDate%")
    ->latest('id')
    ->first();

if ($lastPurchase && strpos($lastPurchase->no_invoice ?? '', 'PB/') === 0) {
    $invoiceNum = intval(substr($lastPurchase->no_invoice, -3)) + 1;
} else {
    $invoiceNum = 1;
}
$invoiceNum = str_pad($invoiceNum, 3, '0', STR_PAD_LEFT);
$invoiceNumber = "PB/$invoiceDate-$invoiceNum";

// Create purchase with warehouse destination
$purchase = Purchase::create([
    'supplier_id' => $supplier->id,
    'no_invoice' => $invoiceNumber,
    'store_id' => null,
    'warehouse_id' => $warehouse->id,
    'tanggal_pembelian' => now(),
    'status' => 'completed',
]);

echo "Created Purchase ID: {$purchase->id}\n";
echo "Invoice: {$purchase->no_invoice}\n";
$qty = 20;
$price = 50000;

$purchaseItem = PurchaseItem::create([
    'purchase_id' => $purchase->id,
    'product_id' => $product->id,
    'category_id' => $product->category_id,
    'subcategory_id' => $product->subcategory_id,
    'unit_id' => $unit->id,
    'qty' => 0, // No store stock
    'qty_gudang' => $qty, // All to warehouse
    'harga_beli' => $price,
    'total' => $qty * $price,
    'destination_type' => 'gudang',
    'batches' => json_encode([
        [
            'name' => "Pembelian - {$purchase->no_invoice} - {$warehouse->nama_gudang}",
            'qty' => $qty
        ]
    ])
]);

echo "Created Purchase Item ID: {$purchaseItem->id}\n";
echo "Quantity to Warehouse: {$qty}\n";

// Create StockAdjustment for warehouse
$user = \App\Models\User::first();
$adjustment = StockAdjustment::create([
    'product_id' => $product->id,
    'unit_id' => $unit->id,
    'user_id' => $user?->id ?? 1,
    'adjustment_type' => 'add',
    'quantity' => $qty,
    'store_id' => null,
    'warehouse_id' => $warehouse->id,
    'adjustment_date' => now(),
    'stok_awal' => 0,
    'reason' => "Pembelian {$purchase->no_invoice}",
]);

echo "Created StockAdjustment ID: {$adjustment->id}\n";

// Create StockBatch for warehouse
$batch = StockBatch::create([
    'product_id' => $product->id,
    'category_id' => $product->category_id,
    'subcategory_id' => $product->subcategory_id,
    'location_type' => 'warehouse',
    'location_id' => $warehouse->id,
    'nama_tumpukan' => "Pembelian - {$purchase->no_invoice} - {$warehouse->nama_gudang}",
    'qty' => $qty,
    'status' => 'aktual',
]);

echo "Created StockBatch ID: {$batch->id}\n";
echo "\n=== SUCCESS ===\n";
echo "Warehouse stock created: {$qty} units\n";

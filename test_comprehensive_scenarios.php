<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Purchase, App\Models\PurchaseItem, App\Models\Supplier, App\Models\Store, App\Models\Warehouse, App\Models\Product, App\Models\Unit;
use App\Models\StockAdjustment, App\Models\StockBatch, App\Models\StockCard;
use App\Services\StockBatchService;
use Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Auth;

Auth::login(\App\Models\User::first());

echo "=== COMPREHENSIVE SPLIT DESTINATION TEST ===\n\n";

$supplier = Supplier::first();
$product = Product::find(23);
$unit = Unit::first();
$store = Store::first();
$warehouse = Warehouse::first();

$testScenarios = [
    [
        'name' => 'SCENARIO 1: qty (Toko) only',
        'qty' => 10,
        'qty_gudang' => 0,
        'destination_type' => 'toko',
        'invoice' => 'SC1-TOKO-' . time(),
    ],
    [
        'name' => 'SCENARIO 2: qty_gudang (Gudang) only',
        'qty' => 0,
        'qty_gudang' => 20,
        'destination_type' => 'gudang',
        'invoice' => 'SC2-GUDANG-' . time(),
    ],
    [
        'name' => 'SCENARIO 3: BOTH qty (15) + qty_gudang (20)',
        'qty' => 15,
        'qty_gudang' => 20,
        'destination_type' => 'gudang',
        'invoice' => 'SC3-SPLIT-' . time(),
    ],
];

foreach ($testScenarios as $scenario) {
    echo "================================\n";
    echo $scenario['name'] . "\n";
    echo "================================\n";

    $tanggal_pembelian = now();
    $store_id = null;
    $warehouse_id = null;
    $hasToko = $scenario['qty'] > 0;
    $hasGudang = $scenario['qty_gudang'] > 0;

    if ($hasToko) $store_id = $store->id;
    if ($hasGudang) $warehouse_id = $warehouse->id;

    echo "Auto-default locations:\n";
    echo "  store_id: $store_id (hasToko: " . ($hasToko ? 'yes' : 'no') . ")\n";
    echo "  warehouse_id: $warehouse_id (hasGudang: " . ($hasGudang ? 'yes' : 'no') . ")\n\n";

    // Create purchase
    $purchase = Purchase::create([
        'no_invoice' => $scenario['invoice'],
        'tanggal_pembelian' => $tanggal_pembelian,
        'supplier_id' => $supplier->id,
        'store_id' => $store_id,
        'warehouse_id' => $warehouse_id,
        'status' => 'completed',
    ]);

    // Create item
    $item = [
        'category_id' => $product->category_id,
        'subcategory_id' => $product->subcategory_id,
        'product_id' => $product->id,
        'qty' => $scenario['qty'],
        'qty_gudang' => $scenario['qty_gudang'],
        'destination_type' => $scenario['destination_type'],
        'unit_id' => $unit->id,
        'harga_beli' => 50000,
        'total' => (($scenario['qty'] + $scenario['qty_gudang']) * 50000),
    ];

    $purchaseItem = $purchase->purchaseItems()->create($item);

    DB::beginTransaction();
    try {
        // Process TOKO if qty > 0
        if ($store_id && ($item['qty'] ?? 0) > 0) {
            StockAdjustment::create([
                'product_id' => $item['product_id'],
                'store_id' => $store_id,
                'warehouse_id' => null,
                'adjustment_type' => 'add',
                'quantity' => $item['qty'],
                'stok_awal' => 0,
                'stok_masuk' => $item['qty'],
                'unit_id' => $item['unit_id'],
                'reason' => 'Pembelian dari ' . $supplier->nama_supplier,
                'adjustment_date' => $tanggal_pembelian,
                'user_id' => Auth::id(),
            ]);

            app(StockBatchService::class)->addStock(
                $item['product_id'],
                'store',
                "SC Pembelian Toko - {$scenario['invoice']}",
                $item['qty'],
                $store_id,
                'Pembelian dari ' . $supplier->nama_supplier,
                $tanggal_pembelian
            );
        }

        // Process GUDANG if qty_gudang > 0
        if ($warehouse_id && ($item['qty_gudang'] ?? 0) > 0) {
            StockAdjustment::create([
                'product_id' => $item['product_id'],
                'store_id' => null,
                'warehouse_id' => $warehouse_id,
                'adjustment_type' => 'add',
                'quantity' => $item['qty_gudang'],
                'stok_awal' => 0,
                'stok_masuk' => $item['qty_gudang'],
                'unit_id' => $item['unit_id'],
                'reason' => 'Pembelian dari ' . $supplier->nama_supplier,
                'adjustment_date' => $tanggal_pembelian,
                'user_id' => Auth::id(),
            ]);

            app(StockBatchService::class)->addStock(
                $item['product_id'],
                'warehouse',
                "SC Pembelian Gudang - {$scenario['invoice']}",
                $item['qty_gudang'],
                $warehouse_id,
                'Pembelian dari ' . $supplier->nama_supplier,
                $tanggal_pembelian
            );
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        echo "❌ Error: " . $e->getMessage() . "\n";
        continue;
    }

    // Verify
    $storeBatches = StockBatch::where('location_type', 'store')
        ->where('product_id', $product->id)
        ->where('nama_tumpukan', 'LIKE', "%{$scenario['invoice']}%")
        ->get();

    $warehouseBatches = StockBatch::where('location_type', 'warehouse')
        ->where('product_id', $product->id)
        ->where('nama_tumpukan', 'LIKE', "%{$scenario['invoice']}%")
        ->get();

    echo "Results:\n";
    echo "  Store Batches: " . count($storeBatches);
    if (count($storeBatches) > 0) {
        echo " ✓";
        foreach ($storeBatches as $b) {
            echo " (ID: {$b->id}, Qty: {$b->qty})";
        }
    }
    echo "\n";

    echo "  Warehouse Batches: " . count($warehouseBatches);
    if (count($warehouseBatches) > 0) {
        echo " ✓";
        foreach ($warehouseBatches as $b) {
            echo " (ID: {$b->id}, Qty: {$b->qty})";
        }
    }
    echo "\n\n";
}

echo "=== ALL TESTS COMPLETED ===\n";

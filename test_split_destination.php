<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Purchase, App\Models\PurchaseItem, App\Models\Supplier, App\Models\Store, App\Models\Warehouse, App\Models\Product, App\Models\Unit;
use App\Models\StockAdjustment, App\Models\StockBatch, App\Models\StockCard;
use App\Services\StockBatchService;
use Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Auth;

Auth::login(\App\Models\User::first());

echo "=== TEST: QTY TOKO 15 + TUJUAN GUDANG 20 ===\n\n";

// Cleanup
StockCard::where('note', 'LIKE', '%SPLIT-TEST%')->delete();
StockBatch::where('nama_tumpukan', 'LIKE', '%SPLIT%')->delete();
StockAdjustment::where('reason', 'LIKE', '%SPLIT%')->delete();
Purchase::where('no_invoice', 'SPLIT-TEST-001')->delete();

$supplier = Supplier::first();
$product = Product::find(23);
$unit = Unit::first();
$store = Store::first();
$warehouse = Warehouse::first();

echo "Test Setup:\n";
echo "  Supplier: {$supplier->nama_supplier}\n";
echo "  Product: {$product->nama_produk}\n";
echo "  Store: {$store->nama_toko}\n";
echo "  Warehouse: {$warehouse->nama_gudang}\n\n";

echo "Input Pembelian:\n";
echo "  qty (Toko): 15\n";
echo "  destination_type: gudang (selected by user)\n";
echo "  qty_gudang: 20\n\n";

// Form data: User selects gudang destination but has both qty and qty_gudang
$no_invoice = 'SPLIT-TEST-001';
$tanggal_pembelian = now();
$supplier_id = $supplier->id;
$store_id = null;      // Not selected
$warehouse_id = null;  // Not selected

$purchaseItems = [
    [
        'category_id' => $product->category_id,
        'subcategory_id' => $product->subcategory_id,
        'product_id' => $product->id,
        'qty' => 15,                // Toko quantity
        'qty_gudang' => 20,         // Gudang quantity
        'destination_type' => 'gudang', // User selected gudang
        'unit_id' => $unit->id,
        'harga_beli' => 50000,
        'total' => (15 * 50000) + (20 * 50000), // Total for both
        'batches' => [['name' => '', 'qty' => 0]],
    ]
];

echo "Applying auto-default location logic...\n";
$hasToko = false;
$hasGudang = false;
foreach ($purchaseItems as $item) {
    // Check if qty > 0 for toko regardless of destination_type
    if (($item['qty'] ?? 0) > 0) {
        $hasToko = true;
    }
    // Check if qty_gudang > 0 for gudang regardless of destination_type
    if (($item['qty_gudang'] ?? 0) > 0) {
        $hasGudang = true;
    }
}

if ($hasToko && !$store_id) {
    $store_id = Store::first()?->id;
    echo "✓ Auto-set store_id: $store_id (qty > 0)\n";
}
if ($hasGudang && !$warehouse_id) {
    $warehouse_id = Warehouse::first()?->id;
    echo "✓ Auto-set warehouse_id: $warehouse_id (qty_gudang > 0)\n";
}

// Create purchase
$purchase = Purchase::create([
    'no_invoice' => $no_invoice,
    'tanggal_pembelian' => $tanggal_pembelian,
    'supplier_id' => $supplier_id,
    'store_id' => $store_id,
    'warehouse_id' => $warehouse_id,
    'status' => 'completed',
]);
echo "✓ Created Purchase ID: {$purchase->id}\n\n";

// Process items with current logic
DB::beginTransaction();
try {
    foreach ($purchaseItems as $item) {
        $purchaseItem = $purchase->purchaseItems()->create($item);
        echo "Created PurchaseItem ID: {$purchaseItem->id}\n";

        $destination = $item['destination_type'] ?? 'toko';
        echo "  destination_type: $destination\n";
        echo "  qty: {$item['qty']}\n";
        echo "  qty_gudang: {$item['qty_gudang']}\n\n";

        echo "Stock creation logic (NEW - if/if for split destinations):\n";

        // NEW LOGIC - if/if (both can be processed)
        if ($store_id && ($item['qty'] ?? 0) > 0) {
            echo "  ✓ Branch: TOKO processing (qty > 0)\n";
            $effectiveQty = $item['qty'];

            $adjustment = StockAdjustment::create([
                'product_id' => $item['product_id'],
                'store_id' => $store_id,
                'warehouse_id' => null,
                'adjustment_type' => 'add',
                'quantity' => $effectiveQty,
                'stok_awal' => 0,
                'stok_masuk' => $effectiveQty,
                'unit_id' => $item['unit_id'],
                'reason' => 'SPLIT Pembelian - Toko',
                'adjustment_date' => $tanggal_pembelian,
                'user_id' => Auth::id(),
            ]);

            $batch = app(StockBatchService::class)->addStock(
                $item['product_id'],
                'store',
                "SPLIT Pembelian Toko - {$no_invoice}",
                $effectiveQty,
                $store_id,
                'SPLIT Pembelian - Toko',
                $tanggal_pembelian
            );
            echo "     ✓ Created Toko stock: {$effectiveQty} units\n";
        }

        // NEW LOGIC - second if condition (not elseif)
        if ($warehouse_id && ($item['qty_gudang'] ?? 0) > 0) {
            echo "  ✓ Branch: GUDANG processing (qty_gudang > 0)\n";
            $effectiveQty = $item['qty_gudang'];

            $adjustment = StockAdjustment::create([
                'product_id' => $item['product_id'],
                'store_id' => null,
                'warehouse_id' => $warehouse_id,
                'adjustment_type' => 'add',
                'quantity' => $effectiveQty,
                'stok_awal' => 0,
                'stok_masuk' => $effectiveQty,
                'unit_id' => $item['unit_id'],
                'reason' => 'SPLIT Pembelian - Gudang',
                'adjustment_date' => $tanggal_pembelian,
                'user_id' => Auth::id(),
            ]);

            $batch = app(StockBatchService::class)->addStock(
                $item['product_id'],
                'warehouse',
                "SPLIT Pembelian Gudang - {$no_invoice}",
                $effectiveQty,
                $warehouse_id,
                'SPLIT Pembelian - Gudang',
                $tanggal_pembelian
            );
            echo "     ✓ Created Gudang stock: {$effectiveQty} units\n";
            echo "     ✗ TOKO stock NOT created (elseif skipped it)\n";
        } else {
            echo "  ✗ No condition matched!\n";
        }
    }

    DB::commit();
    echo "\n✓ Transaction committed\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Verify
echo "\n=== VERIFICATION ===\n";
$storeBatches = StockBatch::where('location_type', 'store')->where('product_id', $product->id)->where('nama_tumpukan', 'LIKE', '%SPLIT%')->get();
echo "Store Batches (SPLIT): " . count($storeBatches) . " found\n";
foreach ($storeBatches as $b) {
    echo "  ID: {$b->id}, Qty: {$b->qty}, Name: {$b->nama_tumpukan}\n";
}

$warehouseBatches = StockBatch::where('location_type', 'warehouse')->where('product_id', $product->id)->where('nama_tumpukan', 'LIKE', '%SPLIT%')->get();
echo "Warehouse Batches (SPLIT): " . count($warehouseBatches) . " found\n";
foreach ($warehouseBatches as $b) {
    echo "  ID: {$b->id}, Qty: {$b->qty}, Name: {$b->nama_tumpukan}\n";
}

$adjustments = StockAdjustment::where('reason', 'LIKE', '%SPLIT%')->get();
echo "Adjustments (SPLIT): " . count($adjustments) . " found\n";
foreach ($adjustments as $a) {
    $loc = $a->store_id ? "Toko #{$a->store_id}" : "Gudang #{$a->warehouse_id}";
    echo "  ID: {$a->id}, Qty: {$a->quantity}, Location: $loc\n";
}

echo "\n=== RESULT ===\n";
if (count($storeBatches) > 0 && count($warehouseBatches) > 0) {
    echo "✅ BOTH toko dan gudang stock created (EXPECTED - NEW LOGIC)\n";
} elseif (count($warehouseBatches) > 0 && count($storeBatches) == 0) {
    echo "⚠️  ONLY gudang stock created (old if/elseif behavior - NOT expected)\n";
} else {
    echo "❌ No stock created\n";
}

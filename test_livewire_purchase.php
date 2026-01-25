<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Purchase, App\Models\PurchaseItem, App\Models\Supplier, App\Models\Store, App\Models\Warehouse, App\Models\Product, App\Models\Unit;
use App\Models\StockAdjustment, App\Models\StockBatch, App\Models\StockCard;
use App\Services\StockBatchService;
use Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Auth;

// Fake auth
Auth::login(\App\Models\User::first());

echo "=== SIMULATING LIVEWIRE PURCHASE SAVE ===\n\n";

// Delete test data
StockCard::where('note', 'LIKE', '%LIVEWIRE-TEST%')->delete();
StockBatch::where('nama_tumpukan', 'LIKE', '%LIVEWIRE%')->delete();
StockAdjustment::where('reason', 'LIKE', '%LIVEWIRE%')->delete();
Purchase::where('no_invoice', 'LIVEWIRE-TEST-001')->delete();

$supplier = Supplier::first();
$product = Product::find(23);
$unit = Unit::first();
$store = Store::first();

// Simulate form data (as it would come from Livewire)
$no_invoice = 'LIVEWIRE-TEST-001';
$tanggal_pembelian = now();
$supplier_id = $supplier->id;
$store_id = null; // NOT SET - will be auto-set
$warehouse_id = null;
$status = 'completed';

$purchaseItems = [
    [
        'category_id' => $product->category_id,
        'subcategory_id' => $product->subcategory_id,
        'product_id' => $product->id,
        'qty' => 15,
        'qty_gudang' => 0,
        'destination_type' => 'toko', // Default
        'unit_id' => $unit->id,
        'harga_beli' => 50000,
        'total' => 750000,
        'batches' => [['name' => '', 'qty' => 0]],
    ]
];

echo "Form Input:\n";
echo "  no_invoice: $no_invoice\n";
echo "  supplier_id: $supplier_id\n";
echo "  store_id: " . ($store_id ?: 'NULL') . "\n";
echo "  warehouse_id: " . ($warehouse_id ?: 'NULL') . "\n";
echo "  purchaseItems[0].destination_type: {$purchaseItems[0]['destination_type']}\n";
echo "  purchaseItems[0].qty: {$purchaseItems[0]['qty']}\n\n";

// Simulate the auto-default logic from save()
echo "Applying auto-default location logic...\n";
$hasToko = false;
$hasGudang = false;
foreach ($purchaseItems as $item) {
    if (($item['destination_type'] ?? 'toko') === 'toko' && ($item['qty'] ?? 0) > 0) {
        $hasToko = true;
    }
    if (($item['destination_type'] ?? 'toko') === 'gudang' && ($item['qty_gudang'] ?? 0) > 0) {
        $hasGudang = true;
    }
}

if ($hasToko && !$store_id) {
    $store_id = Store::first()?->id;
    echo "✓ Auto-set store_id to: $store_id\n";
}
if ($hasGudang && !$warehouse_id) {
    $warehouse_id = Warehouse::first()?->id;
    echo "✓ Auto-set warehouse_id to: $warehouse_id\n";
}

// Create purchase
$purchase = Purchase::create([
    'no_invoice' => $no_invoice,
    'tanggal_pembelian' => $tanggal_pembelian,
    'supplier_id' => $supplier_id,
    'store_id' => $store_id,
    'warehouse_id' => $warehouse_id,
    'status' => $status,
]);
echo "\n✓ Created Purchase ID: {$purchase->id}\n";

// Create purchase items and stock
DB::beginTransaction();
try {
    foreach ($purchaseItems as $item) {
        // Create PurchaseItem
        $purchaseItem = $purchase->purchaseItems()->create($item);
        echo "✓ Created PurchaseItem ID: {$purchaseItem->id}\n";

        // Create stock based on destination_type
        $destination = $item['destination_type'] ?? 'toko';

        if ($destination === 'toko' && $store_id && ($item['qty'] ?? 0) > 0) {
            echo "\n  → Creating stock for TOKO\n";
            $batchedEntries = collect($item['batches'] ?? [])->filter(function ($b) {
                return isset($b['qty']) && (float) $b['qty'] > 0;
            })->values();
            $effectiveQty = $item['qty'] ?? 0;

            // Create StockAdjustment
            $adjustment = StockAdjustment::create([
                'product_id' => $item['product_id'],
                'store_id' => $store_id,
                'warehouse_id' => null,
                'adjustment_type' => 'add',
                'quantity' => $effectiveQty,
                'stok_awal' => 0,
                'stok_masuk' => $effectiveQty,
                'unit_id' => $item['unit_id'] ?? null,
                'reason' => 'LIVEWIRE Pembelian dari ' . $supplier->nama_supplier,
                'adjustment_date' => $tanggal_pembelian,
                'user_id' => Auth::id(),
            ]);
            echo "     ✓ StockAdjustment ID: {$adjustment->id}\n";

            // Create StockBatch
            $storeName = $store->nama_toko ?? 'Toko';
            $batchName = "LIVEWIRE Pembelian - {$no_invoice} - {$storeName}";

            $batch = app(StockBatchService::class)->addStock(
                $item['product_id'],
                'store',
                $batchName,
                $effectiveQty,
                $store_id,
                'LIVEWIRE Pembelian dari ' . $supplier->nama_supplier,
                \Carbon\Carbon::parse($tanggal_pembelian)
            );
            echo "     ✓ StockBatch ID: {$batch->id}\n";
            echo "     ✓ StockCard created (via addStock)\n";
        }
    }

    DB::commit();
    echo "\n✓ Transaction committed successfully\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Verify
echo "\n=== VERIFICATION ===\n";
$batches = StockBatch::where('location_type', 'store')->where('product_id', $product->id)->orderBy('id', 'desc')->limit(1)->get();
echo "Latest StockBatch: " . count($batches) . " found\n";
if ($batches->count() > 0) {
    $b = $batches->first();
    echo "  ID: {$b->id}, Name: {$b->nama_tumpukan}, Qty: {$b->qty}, Status: {$b->status}\n";
}

$adjustments = StockAdjustment::where('reason', 'LIKE', '%LIVEWIRE%')->get();
echo "StockAdjustments: " . count($adjustments) . " found\n";
foreach ($adjustments as $a) {
    echo "  ID: {$a->id}, Qty: {$a->quantity}, Reason: {$a->reason}\n";
}

$cards = StockCard::where('note', 'LIKE', '%LIVEWIRE%')->get();
echo "StockCards: " . count($cards) . " found\n";

echo "\n✅ Test completed!\n";

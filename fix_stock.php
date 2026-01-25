<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update existing purchase items to set destination_type
$updated = \App\Models\PurchaseItem::whereNull('destination_type')
    ->orWhere('destination_type', '')
    ->update(['destination_type' => 'toko']);

echo "Updated {$updated} existing purchase items with destination_type=toko\n";

// Now create stock adjustments for this purchase
$purchase = \App\Models\Purchase::find(1);
if ($purchase) {
    echo "Processing Purchase ID: {$purchase->id}\n";

    // Delete old stock adjustments first
    \App\Models\StockAdjustment::where('reason', 'like', '%Pembelian dari%')
        ->whereDate('adjustment_date', $purchase->tanggal_pembelian)
        ->delete();

    echo "Creating stock adjustments for purchase items...\n";

    foreach ($purchase->purchaseItems as $item) {
        $destination = $item->destination_type ?? 'toko';
        echo "  Item {$item->product_id}: destination={$destination}, qty={$item->qty}, qty_gudang={$item->qty_gudang}\n";

        if ($destination === 'toko' && $purchase->store_id && ($item->qty ?? 0) > 0) {
            \App\Models\StockAdjustment::create([
                'product_id' => $item->product_id,
                'store_id' => $purchase->store_id,
                'warehouse_id' => null,
                'adjustment_type' => 'add',
                'quantity' => $item->qty,
                'stok_awal' => 0,
                'stok_masuk' => $item->qty,
                'unit_id' => $item->unit_id,
                'reason' => 'Pembelian dari ' . $purchase->supplier->nama_supplier,
                'adjustment_date' => $purchase->tanggal_pembelian,
                'user_id' => 2,
            ]);
            echo "    → Created StockAdjustment for TOKO: qty={$item->qty}\n";
        } elseif ($destination === 'gudang' && $purchase->warehouse_id && ($item->qty_gudang ?? 0) > 0) {
            \App\Models\StockAdjustment::create([
                'product_id' => $item->product_id,
                'store_id' => null,
                'warehouse_id' => $purchase->warehouse_id,
                'adjustment_type' => 'add',
                'quantity' => $item->qty_gudang,
                'stok_awal' => 0,
                'stok_masuk' => $item->qty_gudang,
                'unit_id' => $item->unit_id,
                'reason' => 'Pembelian dari ' . $purchase->supplier->nama_supplier,
                'adjustment_date' => $purchase->tanggal_pembelian,
                'user_id' => 2,
            ]);
            echo "    → Created StockAdjustment for GUDANG: qty={$item->qty_gudang}\n";
        }
    }
}

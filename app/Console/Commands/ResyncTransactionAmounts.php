<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\TransactionHistory;
use Illuminate\Console\Command;

class ResyncTransactionAmounts extends Command
{
    protected $signature = 'transactions:resync-amounts';

    protected $description = 'Re-sync transaction amounts based on purchase items (qty + qty_gudang) × harga_beli';

    public function handle()
    {
        $purchases = Purchase::with('purchaseItems')->get();
        $this->info("Processing {$purchases->count()} purchases...\n");

        foreach ($purchases as $purchase) {
            // Delete old transactions
            $oldCount = TransactionHistory::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->count();

            TransactionHistory::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->delete();

            // Calculate amounts from items (qty + qty_gudang) × harga_beli
            $storeAmount = 0;
            $warehouseAmount = 0;

            foreach ($purchase->purchaseItems as $item) {
                $qtyToko = $item->qty ?? 0;
                $qtyGudang = $item->qty_gudang ?? 0;
                $harga = $item->harga_beli ?? 0;

                // Hitung per item untuk toko dan gudang
                $itemStoreAmount = $qtyToko * $harga;
                $itemWarehouseAmount = $qtyGudang * $harga;

                $storeAmount += $itemStoreAmount;
                $warehouseAmount += $itemWarehouseAmount;

                $this->line("  Item: {$item->product->nama_produk}");
                $this->line("    Toko: {$qtyToko} × {$harga} = ".number_format($itemStoreAmount, 0));
                $this->line("    Gudang: {$qtyGudang} × {$harga} = ".number_format($itemWarehouseAmount, 0));
            }

            // Create store transaction if amount > 0
            if ($storeAmount > 0) {
                TransactionHistory::create([
                    'transaction_code' => $purchase->no_invoice.'-TOKO',
                    'transaction_type' => 'pembelian',
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'transaction_date' => $purchase->tanggal_pembelian,
                    'amount' => $storeAmount,
                    'currency' => 'IDR',
                    'status' => 'completed',
                    'user_id' => 1,
                    'description' => 'Pembelian Toko - '.$purchase->no_invoice,
                ]);
            }

            // Create warehouse transaction if amount > 0
            if ($warehouseAmount > 0) {
                TransactionHistory::create([
                    'transaction_code' => $purchase->no_invoice.'-GUDANG',
                    'transaction_type' => 'pembelian',
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'transaction_date' => $purchase->tanggal_pembelian,
                    'amount' => $warehouseAmount,
                    'currency' => 'IDR',
                    'status' => 'completed',
                    'user_id' => 1,
                    'description' => 'Pembelian Gudang - '.$purchase->no_invoice,
                ]);
            }

            $storeStr = $storeAmount > 0 ? 'Toko: '.number_format($storeAmount, 0) : '';
            $warehouseStr = $warehouseAmount > 0 ? 'Gudang: '.number_format($warehouseAmount, 0) : '';
            $amounts = array_filter([$storeStr, $warehouseStr]);
            $this->line("✓ {$purchase->no_invoice} - ".implode(' + ', $amounts));
            $this->line('');
        }

        $this->info('Done! All transaction amounts re-synced from purchase items.');
    }
}

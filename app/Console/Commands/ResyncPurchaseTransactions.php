<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\TransactionHistory;
use Illuminate\Console\Command;

class ResyncPurchaseTransactions extends Command
{
    protected $signature = 'transactions:resync-purchases';

    protected $description = 'Re-sync all purchase transactions to split store/warehouse format';

    public function handle()
    {
        $purchases = Purchase::with('purchaseItems')->get();
        $this->info("Processing {$purchases->count()} purchases...\n");

        foreach ($purchases as $purchase) {
            // Delete old transactions for this purchase
            $oldCount = TransactionHistory::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->count();

            TransactionHistory::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->delete();

            // Calculate store and warehouse amounts
            $storeAmount = 0;
            $warehouseAmount = 0;

            foreach ($purchase->purchaseItems as $item) {
                $qtyToko = $item->qty ?? 0;
                $qtyGudang = $item->qty_gudang ?? 0;
                $harga = $item->harga_beli ?? 0;

                $storeAmount += $qtyToko * $harga;
                $warehouseAmount += $qtyGudang * $harga;
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

            $storeStr = $storeAmount > 0 ? '1' : '0';
            $warehouseStr = $warehouseAmount > 0 ? '1' : '0';
            $this->line("✓ {$purchase->no_invoice} (deleted {$oldCount} old, created {$storeStr}+{$warehouseStr} new)");
        }

        $this->info("\nDone! All purchase transactions re-synced.");
    }
}

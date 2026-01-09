<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\TransactionHistory;
use Illuminate\Console\Command;

class ResyncTransactionAmountsFromTotal extends Command
{
    protected $signature = 'transactions:resync-from-total';

    protected $description = 'Sync transaction amounts directly from purchase_items.total column';

    public function handle()
    {
        $purchases = Purchase::with('purchaseItems')->get();
        $this->info("Processing {$purchases->count()} purchases...\n");

        foreach ($purchases as $purchase) {
            // Delete old transactions for this purchase
            TransactionHistory::where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->delete();

            // Sum all purchase_items.total
            $totalAmount = 0;
            foreach ($purchase->purchaseItems as $item) {
                $totalAmount += $item->total ?? 0;
                if ($item->product) {
                    $this->line("  Item: {$item->product->nama_produk} - Total: ".number_format($item->total, 0));
                }
            }

            // Create single transaction entry with total amount from purchase_items.total
            if ($totalAmount > 0) {
                TransactionHistory::create([
                    'transaction_code' => $purchase->no_invoice,
                    'transaction_type' => 'pembelian',
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'transaction_date' => $purchase->tanggal_pembelian,
                    'amount' => $totalAmount,
                    'currency' => 'IDR',
                    'status' => 'completed',
                    'user_id' => 1,
                    'description' => 'Pembelian - '.$purchase->no_invoice,
                ]);
                $this->info("✓ {$purchase->no_invoice} - Total: ".number_format($totalAmount, 0));
            }
        }

        $this->newLine();
        $this->info('Done! All transaction amounts synced from purchase_items.total.');
    }
}

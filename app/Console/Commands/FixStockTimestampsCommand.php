<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\StockBatch;
use App\Models\StockCard;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStockTimestampsCommand extends Command
{
    protected $signature = 'fix:stock-timestamps {--apply : Actually apply changes (default is dry-run)}';

    protected $description = 'Perbaiki created_at/adjustment_date pada stock_cards dan stock_adjustments berdasarkan tanggal transaksi referensi';

    public function handle(): int
    {
        $apply = $this->option('apply');

        $this->info('Scanning stock_cards for candidates...');

        $cards = StockCard::whereNull('created_at')->orWhere('created_at', DB::raw('updated_at'))->get();

        // Also include cards where created_at exists but reference provides a different date
        $this->info("Found {$cards->count()} stock_card rows with null/identical timestamps to inspect (dry-run)");

        $changes = 0;

        foreach (StockCard::cursor() as $card) {
            $refType = $card->reference_type;
            $refId = $card->reference_id;

            $targetDate = null;

            try {
                if ($refType === 'sale' && $refId) {
                    $sale = Sale::find($refId);
                    if ($sale && $sale->tanggal_penjualan) {
                        $targetDate = Carbon::parse($sale->tanggal_penjualan);
                    }
                } elseif ($refType === 'purchase' && $refId) {
                    $purchase = Purchase::find($refId);
                    if ($purchase && $purchase->tanggal_pembelian) {
                        $targetDate = Carbon::parse($purchase->tanggal_pembelian);
                    }
                } elseif ($refType === 'stock_batch' && $refId) {
                    $batch = StockBatch::find($refId);
                    if ($batch && $batch->created_at) {
                        $targetDate = Carbon::parse($batch->created_at);
                    }
                }
            } catch (\Exception $e) {
                // ignore lookup errors
            }

            if ($targetDate) {
                // Compare date-only
                $current = Carbon::parse($card->created_at ?? $card->updated_at ?? now());
                if (! $current->isSameDay($targetDate)) {
                    $this->line("Would update StockCard #{$card->id}: {$current->toDateTimeString()} -> {$targetDate->toDateTimeString()}");
                    $changes++;
                    if ($apply) {
                        DB::transaction(function () use ($card, $targetDate) {
                            $card->timestamps = false;
                            $card->created_at = $targetDate;
                            $card->updated_at = $targetDate;
                            $card->save();
                            $card->timestamps = true;
                        });
                    }
                }
            }
        }

        $this->info("Scanned stock_cards. Candidate updates: {$changes}");

        // Now handle stock_adjustments: if reference type maps to a transaction date, update adjustment_date if different or null
        $this->info('Scanning stock_adjustments...');
        $adjChanges = 0;

        foreach (StockAdjustment::cursor() as $adj) {
            $targetDate = null;
            try {
                $refType = $adj->reference_type;
                $refId = $adj->reference_id;
                if ($refType === 'sale' && $refId) {
                    $sale = Sale::find($refId);
                    if ($sale && $sale->tanggal_penjualan) {
                        $targetDate = Carbon::parse($sale->tanggal_penjualan);
                    }
                } elseif ($refType === 'purchase' && $refId) {
                    $purchase = Purchase::find($refId);
                    if ($purchase && $purchase->tanggal_pembelian) {
                        $targetDate = Carbon::parse($purchase->tanggal_pembelian);
                    }
                }
            } catch (\Exception $e) {
            }

            if ($targetDate) {
                $current = $adj->adjustment_date ? Carbon::parse($adj->adjustment_date) : null;
                if (! $current || ! $current->isSameDay($targetDate)) {
                    $this->line("Would update StockAdjustment #{$adj->id}: ".($current?->toDateString() ?? 'NULL')." -> {$targetDate->toDateString()}");
                    $adjChanges++;
                    if ($apply) {
                        $adj->adjustment_date = $targetDate->toDateString();
                        $adj->save();
                    }
                }
            }
        }

        $this->info("Scanned stock_adjustments. Candidate updates: {$adjChanges}");

        if ($apply) {
            $this->info('Applied changes. Please backup DB before running again.');
        } else {
            $this->info('Dry-run complete. Run with --apply to commit changes.');
        }

        return 0;
    }
}

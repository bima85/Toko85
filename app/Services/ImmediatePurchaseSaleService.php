<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBatch;
use App\Models\StockCard;
use Illuminate\Support\Facades\DB;

class ImmediatePurchaseSaleService
{
    /**
     * Process an immediate purchase followed by sale in one atomic transaction.
     *
     * $purchaseData = [ 'meta' => [...], 'items' => [ ['product_id'=>..,'qty'=>..,'harga_beli'=>..], ... ] ]
     * $saleData = [ 'meta' => [...], 'items' => [ ['product_id'=>..,'qty'=>..,'harga_jual'=>..], ... ] ]
     *
     * Returns array with purchase and sale models.
     */
    public function process(array $purchaseData, array $saleData): array
    {
        return DB::transaction(function () use ($purchaseData, $saleData) {
            // Create Purchase
            $purchase = Purchase::create($purchaseData['meta'] ?? []);

            // For storing created batches by product id
            $createdBatches = [];

            // ensure a default unit exists
            $defaultUnit = \App\Models\Unit::first() ?? \App\Models\Unit::create([
                'kode_unit' => 'U1',
                'nama_unit' => 'Pcs',
                'is_base_unit' => true,
                'conversion_value' => 1,
            ]);

            foreach ($purchaseData['items'] as $it) {
                $product = \App\Models\Product::find($it['product_id']);
                $hargaBeli = $it['harga_beli'] ?? 0;
                $qty = $it['qty'];
                $purchase->purchaseItems()->create([
                    'product_id' => $it['product_id'],
                    'qty' => $qty,
                    'unit_id' => $defaultUnit->id,
                    'harga_beli' => $hargaBeli,
                    'total' => $hargaBeli * $qty,
                    'category_id' => $product?->category_id,
                    'subcategory_id' => $product?->subcategory_id,
                ]);

                // Create a batch for this immediate incoming stock
                $batch = StockBatch::create([
                    'product_id' => $it['product_id'],
                    'nama_tumpukan' => 'IMMEDIATE #' . $purchase->id,
                    'qty' => $it['qty'],
                ]);

                // StockCard IN
                StockCard::create([
                    'product_id' => $it['product_id'],
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'qty' => $it['qty'],
                    'cost' => $it['harga_beli'] ?? null,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'note' => 'Immediate purchase incoming',
                ]);

                $createdBatches[$it['product_id']][] = $batch;
            }

            // Create Sale
            $sale = Sale::create($saleData['meta'] ?? []);

            foreach ($saleData['items'] as $it) {
                $sale->saleItems()->create([
                    'product_id' => $it['product_id'],
                    'qty' => $it['qty'],
                    'unit_id' => $defaultUnit->id,
                    'harga_jual' => $it['harga_jual'] ?? 0,
                ]);

                // Allocate stock (FIFO) including the batches we just created
                $allocations = $this->allocateFromBatches($it['product_id'], $it['qty']);

                foreach ($allocations as $alloc) {
                    // reduce batch qty
                    StockBatch::where('id', $alloc['batch_id'])->decrement('qty', $alloc['qty']);

                    // StockCard OUT
                    StockCard::create([
                        'product_id' => $it['product_id'],
                        'batch_id' => $alloc['batch_id'],
                        'type' => 'out',
                        'qty' => $alloc['qty'],
                        'cost' => $alloc['cost'] ?? null,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'note' => 'Immediate sale outgoing',
                    ]);
                }
            }

            // Accounting/journal entries should be recorded by existing JournalService if present.
            return ['purchase' => $purchase, 'sale' => $sale];
        });
    }

    /**
     * Allocate from available batches FIFO. Returns array of allocations: [ ['batch_id'=>..,'qty'=>..,'cost'=>..], ... ]
     */
    protected function allocateFromBatches(int $productId, float $needQty): array
    {
        $remaining = $needQty;
        $allocs = [];

        // Lock available batches for update (FIFO by id)
        $batches = StockBatch::where('product_id', $productId)
            ->where('qty', '>', 0)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $take = min($batch->qty, $remaining);
            $allocs[] = [
                'batch_id' => $batch->id,
                'qty' => $take,
                'cost' => null, // optionally fetch last cost from StockCard IN for batch
            ];
            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new \Exception("Stok tidak mencukupi untuk produk {$productId}. Butuh {$needQty}, tersedia " . ($needQty - $remaining));
        }

        return $allocs;
    }
}

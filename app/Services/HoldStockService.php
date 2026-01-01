<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\StockBatch;
use App\Models\StockCard;
use Illuminate\Support\Facades\DB;

class HoldStockService
{
    /**
     * Pindahkan stok ke tumpukan HOLD
     * Ketika customer melakukan order tapi belum bayar/ambil
     */
    public function moveToHold(Sale $sale, StockBatch $batch, $qty)
    {
        return DB::transaction(function () use ($sale, $batch, $qty) {
            // 1. Validasi stok tersedia
            if ($batch->qty < $qty) {
                throw new \Exception("Stok tidak cukup. Tersedia: {$batch->qty}, Diminta: {$qty}");
            }

            // 2. Kurangi dari qty normal (aktual)
            $batch->decrement('qty', $qty);

            // 3. Ambil nama tumpukan original tanpa HOLD suffix
            $originalStackName = preg_replace('/ - HOLD #\d+$/', '', $batch->nama_tumpukan);

            // 4. Ciptakan/Update batch HOLD
            $holdBatchName = "{$originalStackName} - HOLD #{$sale->id}";
            $holdBatch = StockBatch::firstOrCreate(
                [
                    'product_id' => $batch->product_id,
                    'location_type' => $batch->location_type,
                    'location_id' => $batch->location_id,
                    'nama_tumpukan' => $holdBatchName,
                ],
                [
                    'qty' => 0,
                    'status' => 'hold',
                    'note' => "Hold untuk Order #{$sale->id}",
                ]
            );

            // 5. Tambah qty di batch HOLD
            $holdBatch->increment('qty', $qty);

            // 6. Catat di StockCard (tipe: hold)
            StockCard::create([
                'product_id' => $batch->product_id,
                'batch_id' => $batch->id,
                'type' => 'hold',
                'qty' => -$qty,
                'from_location' => $batch->nama_tumpukan,
                'to_location' => $holdBatchName,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'note' => "Stok ditahan untuk Order #{$sale->id} - {$sale->customer->name}",
            ]);

            // 7. Update Sale status
            $sale->update([
                'status' => 'hold',
                'held_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => "✅ Stok berhasil ditahan. Qty: {$qty}",
                'hold_batch' => $holdBatch,
            ];
        });
    }

    /**
     * Batalkan HOLD dan kembalikan stok ke tumpukan asli
     */
    public function cancelHold(Sale $sale)
    {
        return DB::transaction(function () use ($sale) {
            // 1. Ambil batch HOLD
            $holdBatch = StockBatch::where(
                'nama_tumpukan',
                'LIKE',
                "%HOLD #{$sale->id}%"
            )->first();

            if (!$holdBatch) {
                throw new \Exception("Tidak ada stok yang ditahan untuk order ini");
            }

            $qty = $holdBatch->qty;

            // 2. Ambil nama tumpukan original
            $originalStackName = preg_replace('/ - HOLD #\d+$/', '', $holdBatch->nama_tumpukan);

            // 3. Cari/Buat batch original
            $originalBatch = StockBatch::firstOrCreate(
                [
                    'product_id' => $holdBatch->product_id,
                    'location_type' => $holdBatch->location_type,
                    'location_id' => $holdBatch->location_id,
                    'nama_tumpukan' => $originalStackName,
                ],
                [
                    'qty' => 0,
                    'status' => 'aktual',
                ]
            );

            // 4. Kembalikan ke batch original
            $originalBatch->increment('qty', $qty);

            // 5. Hapus batch HOLD
            $holdBatch->delete();

            // 6. Catat di StockCard (tipe: cancel_hold)
            StockCard::create([
                'product_id' => $holdBatch->product_id,
                'type' => 'cancel_hold',
                'qty' => $qty,
                'from_location' => "{$originalStackName} - HOLD #{$sale->id}",
                'to_location' => $originalStackName,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'note' => "Hold dibatalkan untuk Order #{$sale->id}",
            ]);

            // 7. Update Sale status
            $sale->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => "✅ Hold dibatalkan. Stok dikembalikan: {$qty}",
            ];
        });
    }

    /**
     * Selesaikan transaksi HOLD → SOLD
     * Stok diambil dari batch HOLD dan masuk ke stok terjual
     */
    public function completeHold(Sale $sale)
    {
        return DB::transaction(function () use ($sale) {
            // 1. Ambil batch HOLD
            $holdBatch = StockBatch::where(
                'nama_tumpukan',
                'LIKE',
                "%HOLD #{$sale->id}%"
            )->first();

            if (!$holdBatch) {
                throw new \Exception("Tidak ada stok yang ditahan untuk order ini");
            }

            $qty = $holdBatch->qty;

            // 2. Kurangi dari batch HOLD (penghapusan resmi)
            $holdBatch->decrement('qty', $qty);

            // 3. Hapus batch HOLD jika kosong
            if ($holdBatch->qty <= 0) {
                $holdBatch->delete();
            }

            // 4. Catat di StockCard (tipe: sale dari hold)
            StockCard::create([
                'product_id' => $holdBatch->product_id,
                'batch_id' => $holdBatch->id,
                'type' => 'sale',
                'qty' => -$qty,
                'from_location' => "HOLD #{$sale->id}",
                'to_location' => "Customer: {$sale->customer->name}",
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'note' => "Penjualan selesai dari hold Order #{$sale->id}",
            ]);

            // 5. Update Sale status
            $sale->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => "✅ Transaksi selesai. Stok terjual: {$qty}",
            ];
        });
    }

    /**
     * Ambil ringkasan stok (Available vs Hold)
     */
    public function getStockSummary($productId, $locationData = null)
    {
        $query = StockBatch::where('product_id', $productId);

        // Filter lokasi jika diberikan
        if ($locationData) {
            $query->where('location_type', $locationData['type'])
                ->where('location_id', $locationData['id']);
        }

        $available = (clone $query)
            ->where('status', 'aktual')
            ->sum('qty');

        $hold = (clone $query)
            ->where('status', 'hold')
            ->sum('qty');

        $total = $available + $hold;

        return [
            'available' => $available,
            'hold' => $hold,
            'total' => $total,
            'percentage_hold' => $total > 0 ? round(($hold / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Ambil list order yang sedang HOLD
     */
    public function getActiveHolds($productId = null)
    {
        $query = Sale::where('status', 'hold');

        if ($productId) {
            $query->whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        return $query->with(['customer', 'items.product'])
            ->orderByDesc('held_at')
            ->get();
    }
}

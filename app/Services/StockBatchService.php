<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockAdjustment;
use App\Models\StockCard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class StockBatchService
{
    /**
     * Tambah stok ke batch baru atau batch yang sudah ada
     */
    public function addStock(
        int $productId,
        string $locationType,
        string $namaTumpukan,
        float $qty,
        ?int $locationId = null,
        ?string $note = null
    ): StockBatch {
        $batch = StockBatch::create([
            'product_id' => $productId,
            'location_type' => $locationType,
            'location_id' => $locationId,
            'nama_tumpukan' => $namaTumpukan,
            'qty' => $qty,
        ]);

        // Catat di stock_adjustments untuk riwayat penyesuaian stok
        $storeId = null;
        $warehouseId = null;
        if ($locationType === 'store') {
            $storeId = $locationId;
        } elseif ($locationType === 'warehouse') {
            $warehouseId = $locationId;
        }

        StockAdjustment::create([
            'product_id' => $productId,
            'store_id' => $storeId,
            'warehouse_id' => $warehouseId,
            'adjustment_type' => 'add',
            'quantity' => $qty,
            'stok_awal' => 0,
            'stok_masuk' => $qty,
            'unit_id' => Product::find($productId)?->unit_id,
            'reason' => $note ?? 'Penambahan stok dari tumpukan',
            'adjustment_date' => now()->toDateString(),
            'user_id' => Auth::id(),
        ]);

        // Catat di StockCard untuk tampilan di halaman stock-card
        StockCard::create([
            'product_id' => $productId,
            'batch_id' => $batch->id,
            'type' => 'in',
            'qty' => $qty,
            'from_location' => null,
            'to_location' => $locationType === 'store' ? "Toko #{$storeId}" : "Gudang #{$warehouseId}",
            'reference_type' => 'stock_batch',
            'reference_id' => $batch->id,
            'note' => $note ?? "Penambahan stok: {$namaTumpukan}",
        ]);

        return $batch;
    }

    /**
     * Kurangi stok dari batch
     */
    public function reduceStock(
        StockBatch $batch,
        float $qty,
        ?string $note = null
    ): bool {
        if ($batch->qty < $qty) {
            throw new \Exception('Jumlah pengurangan melebihi stok batch');
        }

        $newQty = $batch->qty - $qty;
        $batch->update(['qty' => $newQty]);

        // Catat di stock_adjustments untuk riwayat penyesuaian stok
        $storeId = null;
        $warehouseId = null;
        if ($batch->location_type === 'store') {
            $storeId = $batch->location_id;
        } elseif ($batch->location_type === 'warehouse') {
            $warehouseId = $batch->location_id;
        }

        StockAdjustment::create([
            'product_id' => $batch->product_id,
            'store_id' => $storeId,
            'warehouse_id' => $warehouseId,
            'adjustment_type' => 'reduce',
            'quantity' => $qty,
            'stok_awal' => $batch->qty + $qty,
            'stok_masuk' => -$qty,
            'unit_id' => $batch->product?->unit_id,
            'reason' => $note ?? 'Pengurangan stok dari tumpukan',
            'adjustment_date' => now()->toDateString(),
            'user_id' => Auth::id(),
        ]);

        // Catat di StockCard untuk tampilan di halaman stock-card
        $locationLabel = $batch->location_type === 'store' ? "Toko #{$storeId}" : "Gudang #{$warehouseId}";
        StockCard::create([
            'product_id' => $batch->product_id,
            'batch_id' => $batch->id,
            'type' => 'out',
            'qty' => $qty,
            'from_location' => $locationLabel,
            'to_location' => null,
            'reference_type' => 'stock_batch',
            'reference_id' => $batch->id,
            'note' => $note ?? "Pengurangan stok dari tumpukan",
        ]);

        // Jika qty <= 0, batch akan otomatis dihapus via boot method
        return true;
    }

    /**
     * Pindahkan stok dari satu batch ke lokasi lain
     */
    public function moveStock(
        StockBatch $fromBatch,
        string $toLocationType,
        string $toNamaTumpukan,
        float $qty,
        ?int $toLocationId = null,
        ?string $note = null
    ): array {
        if ($fromBatch->qty < $qty) {
            throw new \Exception('Jumlah pemindahan melebihi stok batch');
        }

        // Kurangi dari batch asal
        $fromBatch->update(['qty' => $fromBatch->qty - $qty]);

        // Catat pengurangan di stock_adjustments
        $storeId = null;
        $warehouseId = null;
        if ($fromBatch->location_type === 'store') {
            $storeId = $fromBatch->location_id;
        } elseif ($fromBatch->location_type === 'warehouse') {
            $warehouseId = $fromBatch->location_id;
        }

        $fromLocationLabel = $fromBatch->location_type === 'store' ? "Toko #{$storeId}" : "Gudang #{$warehouseId}";
        $toLocationLabel = $toLocationType === 'store' ? "Toko #{$toLocationId}" : "Gudang #{$toLocationId}";

        StockAdjustment::create([
            'product_id' => $fromBatch->product_id,
            'store_id' => $storeId,
            'warehouse_id' => $warehouseId,
            'adjustment_type' => 'reduce',
            'quantity' => $qty,
            'stok_awal' => $fromBatch->qty + $qty,
            'stok_masuk' => -$qty,
            'unit_id' => $fromBatch->product?->unit_id,
            'reason' => $note ?? 'Pemindahan stok dari tumpukan',
            'adjustment_date' => now()->toDateString(),
            'user_id' => Auth::id(),
        ]);

        // Catat pengurangan di StockCard
        StockCard::create([
            'product_id' => $fromBatch->product_id,
            'batch_id' => $fromBatch->id,
            'type' => 'out',
            'qty' => $qty,
            'from_location' => $fromLocationLabel,
            'to_location' => $toLocationLabel,
            'reference_type' => 'stock_batch',
            'reference_id' => $fromBatch->id,
            'note' => $note ?? "Pemindahan stok ke tumpukan",
        ]);

        // Buat batch baru di lokasi tujuan
        $toBatch = StockBatch::create([
            'product_id' => $fromBatch->product_id,
            'location_type' => $toLocationType,
            'location_id' => $toLocationId,
            'nama_tumpukan' => $toNamaTumpukan,
            'qty' => $qty,
        ]);

        // Catat penambahan di stock_adjustments
        $toStoreId = null;
        $toWarehouseId = null;
        if ($toLocationType === 'store') {
            $toStoreId = $toLocationId;
        } elseif ($toLocationType === 'warehouse') {
            $toWarehouseId = $toLocationId;
        }

        StockAdjustment::create([
            'product_id' => $fromBatch->product_id,
            'store_id' => $toStoreId,
            'warehouse_id' => $toWarehouseId,
            'adjustment_type' => 'add',
            'quantity' => $qty,
            'stok_awal' => 0,
            'stok_masuk' => $qty,
            'unit_id' => $fromBatch->product?->unit_id,
            'reason' => $note ?? 'Pemindahan stok ke tumpukan',
            'adjustment_date' => now()->toDateString(),
            'user_id' => Auth::id(),
        ]);

        // Catat penambahan di StockCard
        StockCard::create([
            'product_id' => $fromBatch->product_id,
            'batch_id' => $toBatch->id,
            'type' => 'in',
            'qty' => $qty,
            'from_location' => $fromLocationLabel,
            'to_location' => $toLocationLabel,
            'reference_type' => 'stock_batch',
            'reference_id' => $toBatch->id,
            'note' => $note ?? "Penerimaan stok dari pemindahan",
        ]);

        return [
            'from_batch' => $fromBatch,
            'to_batch' => $toBatch,
        ];
    }

    /**
     * Ambil semua batch berdasarkan lokasi
     */
    public function getBatchesByLocation(string $locationType, ?int $productId = null): Collection
    {
        $query = StockBatch::active()->byLocation($locationType)->latestFirst();

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->get();
    }

    /**
     * Hitung total stok per lokasi
     */
    public function getTotalStockByLocation(string $locationType): float
    {
        return StockBatch::active()
            ->byLocation($locationType)
            ->sum('qty');
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockCard;
use App\Repositories\StockCardRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class StockCardService
{
    protected $repository;

    public function __construct(StockCardRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create stock card dengan validasi
     */
    public function createStockCard(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                // Validasi product exists
                $product = Product::find($data['product_id']);
                if (!$product) {
                    throw new Exception('Produk tidak ditemukan');
                }

                // Validasi batch jika ada
                if (!empty($data['batch_id'])) {
                    $batch = StockBatch::find($data['batch_id']);
                    if (!$batch || $batch->product_id !== $product->id) {
                        throw new Exception('Batch tidak valid untuk produk ini');
                    }
                }

                return $this->repository->create($data);
            });
        } catch (Exception $e) {
            throw new Exception('Gagal membuat kartu stok: ' . $e->getMessage());
        }
    }

    /**
     * Update stock card
     */
    public function updateStockCard(int $id, array $data)
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $stockCard = $this->repository->getById($id);
                if (!$stockCard) {
                    throw new Exception('Kartu stok tidak ditemukan');
                }

                if (isset($data['batch_id']) && !empty($data['batch_id'])) {
                    $batch = StockBatch::find($data['batch_id']);
                    if (!$batch || $batch->product_id !== $stockCard->product_id) {
                        throw new Exception('Batch tidak valid');
                    }
                }

                return $this->repository->update($id, $data);
            });
        } catch (Exception $e) {
            throw new Exception('Gagal memperbarui kartu stok: ' . $e->getMessage());
        }
    }

    /**
     * Delete stock card
     */
    public function deleteStockCard(int $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $stockCard = $this->repository->getById($id);
                if (!$stockCard) {
                    throw new Exception('Kartu stok tidak ditemukan');
                }

                return $this->repository->delete($id);
            });
        } catch (Exception $e) {
            throw new Exception('Gagal menghapus kartu stok: ' . $e->getMessage());
        }
    }

    /**
     * Get filtered stock cards
     */
    public function getFiltered(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getFiltered($filters, $perPage);
    }

    /**
     * Get stock card by ID
     */
    public function getById(int $id)
    {
        return $this->repository->getById($id);
    }

    /**
     * Get stock summary
     */
    public function getSummary()
    {
        return $this->repository->getSummary();
    }

    /**
     * Get product history
     */
    public function getProductHistory(int $productId, int $limit = 50)
    {
        return $this->repository->getProductHistory($productId, $limit);
    }

    /**
     * Calculate stock balance for a product
     */
    public function calculateStockBalance(int $productId)
    {
        $stockCards = StockCard::where('product_id', $productId)->get();

        $balance = 0;
        foreach ($stockCards as $card) {
            if (in_array($card->type, ['in', 'return'])) {
                $balance += $card->qty;
            } else {
                $balance -= $card->qty;
            }
        }

        return $balance;
    }

    /**
     * Get transaction type label
     */
    public function getTransactionTypeLabel(string $type): string
    {
        return match ($type) {
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjustment' => 'Penyesuaian',
            'return' => 'Retur',
            default => ucfirst($type),
        };
    }

    /**
     * Get all transaction types
     */
    public function getTransactionTypes(): array
    {
        return [
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjustment' => 'Penyesuaian',
            'return' => 'Retur',
        ];
    }

    /**
     * Get reference type label
     */
    public function getReferenceTypeLabel(string $type): string
    {
        return match ($type) {
            'purchase' => 'Pembelian',
            'sale' => 'Penjualan',
            'adjustment' => 'Penyesuaian',
            'return' => 'Retur',
            'transfer' => 'Pemindahan',
            default => ucfirst($type) ?? '-',
        };
    }

    /**
     * Export stock cards
     */
    public function export(array $filters = [])
    {
        return $this->repository->export($filters);
    }
}

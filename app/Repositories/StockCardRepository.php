<?php

namespace App\Repositories;

use App\Models\StockCard;
use Illuminate\Pagination\Paginator;

class StockCardRepository
{
    /**
     * Get all stock cards with filters
     */
    public function getFiltered(array $filters = [], int $perPage = 15)
    {
        $query = StockCard::with(['product', 'batch'])->latest();

        if (!empty($filters['search'])) {
            $query->whereHas('product', function ($q) {
                $q->where('nama_produk', 'like', "%{$filters['search']}%")
                    ->orWhere('kode_produk', 'like', "%{$filters['search']}%");
            })->orWhere('note', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get stock card by ID
     */
    public function getById(int $id)
    {
        return StockCard::with(['product', 'batch'])->find($id);
    }

    /**
     * Create new stock card
     */
    public function create(array $data)
    {
        return StockCard::create($data);
    }

    /**
     * Update stock card
     */
    public function update(int $id, array $data)
    {
        $stockCard = $this->getById($id);
        if ($stockCard) {
            $stockCard->update($data);
            return $stockCard;
        }
        return null;
    }

    /**
     * Delete stock card
     */
    public function delete(int $id)
    {
        return StockCard::destroy($id);
    }

    /**
     * Get stock cards by product
     */
    public function getByProduct(int $productId, int $perPage = 10)
    {
        return StockCard::where('product_id', $productId)
            ->with(['batch'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get stock cards by type
     */
    public function getByType(string $type, int $perPage = 10)
    {
        return StockCard::where('type', $type)
            ->with(['product', 'batch'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get stock card summary for dashboard
     */
    public function getSummary()
    {
        return [
            'total_in' => StockCard::where('type', 'in')->sum('qty'),
            'total_out' => StockCard::where('type', 'out')->sum('qty'),
            'total_adjustment' => StockCard::where('type', 'adjustment')->sum('qty'),
            'total_return' => StockCard::where('type', 'return')->sum('qty'),
            'total_records' => StockCard::count(),
        ];
    }

    /**
     * Get stock movement history for a product
     */
    public function getProductHistory(int $productId, int $limit = 50)
    {
        return StockCard::where('product_id', $productId)
            ->with(['batch'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Export filtered stock cards
     */
    public function export(array $filters = [])
    {
        $query = StockCard::with(['product', 'batch']);

        if (!empty($filters['search'])) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->get();
    }
}

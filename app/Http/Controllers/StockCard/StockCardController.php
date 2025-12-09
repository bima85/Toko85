<?php

namespace App\Http\Controllers\StockCard;

use App\Services\StockCardService;
use Illuminate\Http\Request;

/**
 * Controller untuk API endpoint Stock Card
 * Dapat digunakan untuk integrasi dengan sistem lain
 */
class StockCardController
{
    protected $service;

    public function __construct(StockCardService $service)
    {
        $this->service = $service;
    }

    /**
     * Get stock card summary
     */
    public function summary()
    {
        try {
            $summary = $this->service->getSummary();
            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get product stock history
     */
    public function productHistory($productId)
    {
        try {
            $history = $this->service->getProductHistory($productId);
            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get product stock balance
     */
    public function productBalance($productId)
    {
        try {
            $balance = $this->service->calculateStockBalance($productId);
            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $productId,
                    'balance' => $balance,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

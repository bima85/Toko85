<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockCard;
use App\Services\ImmediatePurchaseSaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImmediatePurchaseSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_immediate_purchase_then_sale_creates_in_and_out_stockcards_and_updates_batches()
    {
        // create product
        $product = Product::factory()->create();

        $service = new ImmediatePurchaseSaleService();

        $purchaseData = [
            'meta' => [],
            'items' => [
                ['product_id' => $product->id, 'qty' => 10, 'harga_beli' => 5000],
            ],
        ];

        $saleData = [
            'meta' => [],
            'items' => [
                ['product_id' => $product->id, 'qty' => 7, 'harga_jual' => 8000],
            ],
        ];

        $result = $service->process($purchaseData, $saleData);

        $this->assertDatabaseHas('purchases', ['id' => $result['purchase']->id]);
        $this->assertDatabaseHas('sales', ['id' => $result['sale']->id]);

        // batch should exist with remaining qty 3
        $batch = StockBatch::firstWhere('product_id', $product->id);
        $this->assertNotNull($batch);
        $this->assertEquals(3, $batch->qty);

        // stock cards: one IN and one or more OUT entries totaling 7
        $in = StockCard::where('reference_type', 'purchase')->where('reference_id', $result['purchase']->id)->sum('qty');
        $out = StockCard::where('reference_type', 'sale')->where('reference_id', $result['sale']->id)->sum('qty');

        $this->assertEquals(10, $in);
        $this->assertEquals(7, $out);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\StockBatch;

class StockBatchTotalPerProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_total_per_product_json()
    {
        // Create category and subcategory
        $cat = Category::create(['nama_kategori' => 'Kat Test']);
        $sub = Subcategory::create(['nama_subkategori' => 'Sub Test', 'category_id' => $cat->id]);

        // Create product
        $product = Product::create([
            'kode_produk' => 'PRD-001',
            'nama_produk' => 'Produk Test',
            'description' => 'Deskripsi',
            'satuan' => 'pcs',
            'category_id' => $cat->id,
            'subcategory_id' => $sub->id,
        ]);

        // Create stock batches for the product
        StockBatch::create(['product_id' => $product->id, 'nama_tumpukan' => 'T1', 'qty' => 5, 'created_at' => now()->subDay()]);
        StockBatch::create(['product_id' => $product->id, 'nama_tumpukan' => 'T2', 'qty' => 3, 'created_at' => now()]);

        // Call the route
        $response = $this->getJson(route('admin.stock-batches.total-per-product'));

        $response->assertStatus(200);

        $json = $response->json();

        $this->assertArrayHasKey('data', $json);
        $this->assertNotEmpty($json['data']);

        $first = $json['data'][0];

        $this->assertEquals('PRD-001', $first['kode_produk']);
        $this->assertEquals('Produk Test', $first['nama_produk']);
        // total_qty diformat di controller sebagai string lokal (8,00)
        $this->assertEquals('8,00', $first['total_qty']);
        $this->assertArrayHasKey('latest_date', $first);
    }
}

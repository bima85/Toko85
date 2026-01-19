<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use Tests\TestCase;

class SalesBladeTest extends TestCase
{
    public function test_sales_index_view_renders_with_both_variables()
    {
        // Provide saleItems (form array) and saleItemsList (paginator/collection)
        $html = view('livewire.admin.sales.sales-index', [
            'saleItems' => [],
            'saleItemsList' => new Collection,
            'customers' => new Collection,
            'categories' => new Collection,
            'subcategories' => new Collection,
            'products' => new Collection,
            'units' => new Collection,
            'stores' => new Collection,
            'warehouses' => new Collection,
        ])->render();

        $this->assertStringContainsString('Daftar Penjualan', $html);
        $this->assertStringContainsString('Buat Penjualan', $html);
    }
}

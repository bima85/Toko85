<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use Tests\TestCase;

class SalesListTotalCalculationTest extends TestCase
{
    public function test_list_total_uses_unit_conversion()
    {
        // Create stub unit and item
        $unit = (object) ['conversion_value' => 2];
        $sale = (object) ['no_invoice' => 'INV/TEST/001', 'id' => 1, 'tanggal_penjualan' => null, 'customer' => null];
        $item = (object) [
            'harga_jual' => 10000,
            'qty' => 3,
            'unit' => $unit,
            'sale' => $sale,
            'product' => null,
        ];

        $html = view('livewire.admin.sales.sales-index', [
            'saleItems' => [],
            'saleItemsList' => new Collection([$item]),
            'customers' => new Collection,
            'categories' => new Collection,
            'subcategories' => new Collection,
            'products' => new Collection,
            'units' => new Collection,
            'stores' => new Collection,
            'warehouses' => new Collection,
            'showCreateForm' => false,
        ])->render();

        // Expected: 10000 * 3 * 2 = 60000 -> formatted as '60.000'
        $this->assertStringContainsString('Rp 60.000', $html);
    }
}

<?php

use PHPUnit\Framework\TestCase;

final class SalesRenderKeyTest extends TestCase
{
    public function test_render_passes_sale_items_list_key()
    {
        $content = file_get_contents(__DIR__.'/../../app/Livewire/Admin/Sales.php');

        $this->assertStringContainsString("'saleItemsList' => \$saleItems", $content);
    }
}

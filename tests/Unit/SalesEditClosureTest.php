<?php

use PHPUnit\Framework\TestCase;

final class SalesEditClosureTest extends TestCase
{
    public function test_edit_callback_uses_sale_variable()
    {
        $content = file_get_contents(__DIR__.'/../../app/Livewire/Admin/Sales.php');

        $this->assertStringContainsString('->map(function (', $content);
        $this->assertStringContainsString('use ($sale)', $content);
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, \Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Convenience helper to test Livewire components in tests via $this->livewire(...)
     */
    public function livewire(string $name, array $params = [])
    {
        return \Livewire\Livewire::test($name, $params);
    }
}

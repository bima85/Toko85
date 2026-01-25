<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the database with roles and permissions for testing
        $this->seed([
            \Database\Seeders\RoleSeeder::class,
            \Database\Seeders\AddTransactionTabPermissionSeeder::class,
        ]);
    }

    /**
     * Convenience helper to test Livewire components in tests via $this->livewire(...)
     */
    public function livewire(string $name, array $params = [])
    {
        return \Livewire\Livewire::test($name, $params);
    }
}

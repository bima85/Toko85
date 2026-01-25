<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchasesTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchases_table_renders_with_adminlte_classes_for_admin_user(): void
    {
        // create user and ensure it has admin role so Purchases::mount passes
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Admin\Purchases::class)
            ->assertSee('Manajemen Pembelian')
            ->assertSee('Daftar Pembelian');
    }
}

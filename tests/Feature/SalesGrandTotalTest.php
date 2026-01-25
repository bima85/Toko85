<?php

namespace Tests\Feature;

use App\Livewire\Admin\Sales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesGrandTotalTest extends TestCase
{
    use RefreshDatabase;

    public function test_grand_total_calculates_correctly()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'sales.view']);
        $user->givePermissionTo('sales.view');

        Livewire::actingAs($user)
            ->test(Sales::class)
            ->set('saleItems', [
                ['total' => 100000],
                ['total' => 50000],
            ])
            ->set('kuli', 25000)
            ->assertSet('grandTotal', 175000);
    }

    public function test_grand_total_updates_when_kuli_changes()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'sales.view']);
        $user->givePermissionTo('sales.view');

        Livewire::actingAs($user)
            ->test(Sales::class)
            ->set('saleItems', [
                ['total' => 100000],
            ])
            ->set('kuli', 10000)
            ->assertSet('grandTotal', 110000)
            ->set('kuli', 20000)
            ->assertSet('grandTotal', 120000);
    }
}

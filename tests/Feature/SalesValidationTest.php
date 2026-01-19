<?php

namespace Tests\Feature;

use App\Livewire\Admin\Sales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_requires_sale_items_and_no_invoice()
    {
        Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        Livewire::actingAs($user)
            ->test(Sales::class)
            ->set('saleItems', [])
            ->set('no_invoice', '')
            ->call('save')
            ->assertHasErrors(['saleItems', 'no_invoice']);
    }
}

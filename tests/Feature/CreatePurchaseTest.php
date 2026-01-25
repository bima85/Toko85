<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InlinePurchaseCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_inline_purchase_create_form_can_be_opened()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'purchases.create']);
        $user->givePermissionTo('purchases.create');

        // Create required data
        Supplier::factory()->create();
        Store::create(['kode_toko' => 'S1', 'nama_toko' => 'Toko Test']);
        Warehouse::create(['kode_gudang' => 'W1', 'nama_gudang' => 'Gudang Test']);
        $category = Category::factory()->create();
        Subcategory::create([
            'nama_subkategori' => 'Test Subcategory',
            'category_id' => $category->id,
            'kode_subkategori' => 'TS'
        ]);
        Product::factory()->create();
        Unit::create(['kode_unit' => 'PCS', 'nama_unit' => 'Pcs', 'conversion_value' => 1, 'is_base_unit' => true]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Purchases::class)
            ->call('create')
            ->assertSet('showCreateForm', true);
    }
}

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

class PurchasesFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_subcategories_filter_by_selected_category(): void
    {
        // Setup
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        Supplier::factory()->create();
        Store::create(['kode_toko' => 'S1', 'nama_toko' => 'Toko Test']);
        Warehouse::create(['kode_gudang' => 'W1', 'nama_gudang' => 'Gudang Test']);
        Unit::create(['kode_unit' => 'PCS', 'nama_unit' => 'Pcs', 'conversion_value' => 1, 'is_base_unit' => true]);

        // Create categories with subcategories
        $category1 = Category::factory()->create(['nama_kategori' => 'Makanan']);
        $category2 = Category::factory()->create(['nama_kategori' => 'Minuman']);

        $subcategory1_1 = Subcategory::create([
            'nama_subkategori' => 'Roti',
            'category_id' => $category1->id,
            'kode_subkategori' => 'RT',
        ]);
        $subcategory1_2 = Subcategory::create([
            'nama_subkategori' => 'Daging',
            'category_id' => $category1->id,
            'kode_subkategori' => 'DG',
        ]);
        $subcategory2_1 = Subcategory::create([
            'nama_subkategori' => 'Soda',
            'category_id' => $category2->id,
            'kode_subkategori' => 'SD',
        ]);

        // Test
        $this->actingAs($user);
        Livewire::test(\App\Livewire\Admin\Purchases::class)
            ->call('create')
            ->call('addItem')
            // Select category1
            ->set('purchaseItems.0.category_id', $category1->id)
            // Verify subcategories are filtered
            ->assertSee('Roti')
            ->assertSee('Daging')
            // Category2 subcategories should not be visible
            ->assertDontSee('Soda'); // This assertion may not work if Soda is in initial list, but should work after filtering
    }

    public function test_products_filter_by_selected_subcategory(): void
    {
        // Setup
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        Supplier::factory()->create();
        Store::create(['kode_toko' => 'S1', 'nama_toko' => 'Toko Test']);
        Warehouse::create(['kode_gudang' => 'W1', 'nama_gudang' => 'Gudang Test']);
        Unit::create(['kode_unit' => 'PCS', 'nama_unit' => 'Pcs', 'conversion_value' => 1, 'is_base_unit' => true]);

        // Create categories with subcategories and products
        $category = Category::factory()->create(['nama_kategori' => 'Makanan']);
        $subcategory1 = Subcategory::create([
            'nama_subkategori' => 'Roti',
            'category_id' => $category->id,
            'kode_subkategori' => 'RT',
        ]);
        $subcategory2 = Subcategory::create([
            'nama_subkategori' => 'Daging',
            'category_id' => $category->id,
            'kode_subkategori' => 'DG',
        ]);

        $product1 = Product::factory()->create([
            'nama_produk' => 'Roti Putih',
            'category_id' => $category->id,
            'subcategory_id' => $subcategory1->id,
        ]);
        $product2 = Product::factory()->create([
            'nama_produk' => 'Daging Sapi',
            'category_id' => $category->id,
            'subcategory_id' => $subcategory2->id,
        ]);

        // Test
        $this->actingAs($user);
        $component = new \App\Livewire\Admin\Purchases();
        
        // Verify method returns correct products
        $products1 = $component->getProductsBySubcategory($subcategory1->id);
        $products2 = $component->getProductsBySubcategory($subcategory2->id);

        $this->assertCount(1, $products1);
        $this->assertCount(1, $products2);
        $this->assertEquals($product1->id, $products1->first()->id);
        $this->assertEquals($product2->id, $products2->first()->id);
    }

    public function test_category_change_resets_subcategory(): void
    {
        // Setup
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        Supplier::factory()->create();
        Store::create(['kode_toko' => 'S1', 'nama_toko' => 'Toko Test']);
        Warehouse::create(['kode_gudang' => 'W1', 'nama_gudang' => 'Gudang Test']);
        Unit::create(['kode_unit' => 'PCS', 'nama_unit' => 'Pcs', 'conversion_value' => 1, 'is_base_unit' => true]);

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        // Test
        $this->actingAs($user);
        Livewire::test(\App\Livewire\Admin\Purchases::class)
            ->call('create')
            ->call('addItem')
            ->set('purchaseItems.0.category_id', $category1->id)
            ->set('purchaseItems.0.subcategory_id', 'some_value')
            // Change category with wire:change trigger
            ->call('updateCategoryFilter', 0)
            ->assertSet('purchaseItems.0.subcategory_id', null);
    }
}

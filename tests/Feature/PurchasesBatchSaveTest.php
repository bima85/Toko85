<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasesBatchSaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_with_batches_creates_stock_batches()
    {
        // Arrange
        $user = User::factory()->create();
        // Ensure required permissions exist and grant create/manage permissions to the user
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'purchases.create']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'purchases.view']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'transactions.manage']);
        $user->givePermissionTo(['purchases.create', 'transactions.manage']);

        // Give admin role to pass mount() check
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');

        $supplier = Supplier::factory()->create();
        $store = Store::create(['kode_toko' => 'S1', 'nama_toko' => 'Toko Test']);
        $unit = Unit::create(['kode_unit' => 'PCS', 'nama_unit' => 'Pcs', 'conversion_value' => 1, 'is_base_unit' => true]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $payload = [
            [
                'category_id' => $category->id,
                'subcategory_id' => null,
                'product_id' => $product->id,
                'product_search' => $product->nama_produk,
                'qty' => 5,
                'qty_gudang' => 0,
                'unit_id' => $unit->id,
                'harga_beli' => 10000,
                'batches' => [
                    ['name' => 'T1', 'qty' => 2],
                    ['name' => 'T2', 'qty' => 3],
                ],
                'force_manual' => false,
            ],
        ];

        // Act - instantiate component directly and call save to avoid rendering the view in test
        $this->actingAs($user);
        $component = new \App\Livewire\Admin\Purchases;
        $component->mount(); // Call mount to initialize
        $component->supplier_id = $supplier->id;
        $component->store_id = $store->id;
        $component->tanggal_pembelian = date('Y-m-d');
        $component->purchaseItems = $payload;
        $component->batch_enabled = true;

        // Set no_invoice directly before save to ensure test passes
        $component->no_invoice = 'PB/TEST-001';
        $component->save();

        // Assert
        $this->assertDatabaseHas('purchases', ['no_invoice' => 'PB/TEST-001']);

        // Debug: check if purchase items exist
        $purchase = \App\Models\Purchase::where('no_invoice', 'PB/TEST-001')->first();
        $this->assertNotNull($purchase, 'Purchase should be created');
        $this->assertDatabaseHas('purchase_items', ['purchase_id' => $purchase->id]);

        $this->assertDatabaseHas('stock_batches', ['nama_tumpukan' => 'T1', 'qty' => 2]);
        $this->assertDatabaseHas('stock_batches', ['nama_tumpukan' => 'T2', 'qty' => 3]);
    }
}

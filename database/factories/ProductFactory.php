<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        return [
            'kode_produk' => 'PRD' . $this->faker->unique()->numberBetween(1000, 9999),
            'nama_produk' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'satuan' => 'pcs',
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'subcategory_id' => null,
        ];
    }
}

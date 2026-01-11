<?php

namespace Database\Factories;

use App\Models\StockCard;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockCardFactory extends Factory
{
    protected $model = StockCard::class;

    public function definition()
    {
        $product = Product::factory()->create();

        return [
            'product_id' => $product->id,
            'batch_id' => null,
            'cost' => $this->faker->randomFloat(2, 1, 100),
            'type' => 'in',
            'qty' => $this->faker->numberBetween(1, 100),
            'from_location' => null,
            'to_location' => null,
            'reference_type' => null,
            'reference_id' => null,
            'note' => null,
        ];
    }
}

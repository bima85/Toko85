<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition()
    {
        return [
            'kode_supplier' => 'SUP' . $this->faker->unique()->numberBetween(1000, 9999),
            'nama_supplier' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'keterangan' => null,
            'owner' => $this->faker->name(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->words(3, true), // e.g. "Awesome Steel Chair"
            'sku'         => strtoupper($this->faker->bothify('SKU-#####')),
            'description' => $this->faker->optional()->sentence(10),
            'price'       => $this->faker->randomFloat(2, 5, 200), // 5.00–200.00
            'stock'       => $this->faker->numberBetween(0, 100),
            'is_active'   => true,
        ];
    }
}

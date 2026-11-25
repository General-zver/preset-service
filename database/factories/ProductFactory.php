<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word().' '.fake()->word(),
            'price' => fake()->randomFloat(3, 10, 9999),
            'composition' => fake()->realTextBetween(40, 160),
            'preset_id' => random_int(0,10000),
        ];
    }
}

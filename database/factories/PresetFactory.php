<?php

namespace Database\Factories;

use App\Models\Preset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Preset>
 */
class PresetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'status' => fake()->boolean(),
            'preset_price' => rand(100,10000),
        ];
    }
}

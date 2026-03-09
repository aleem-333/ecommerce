<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $categories = ['electronics', 'clothing', 'home', 'books', 'sports'];

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'category' => $this->faker->randomElement($categories),
            'stock' => $this->faker->numberBetween(0, 100),
            'sku' => strtoupper($this->faker->unique()->bothify('???-####')),
            'image' => null,
        ];
    }
}

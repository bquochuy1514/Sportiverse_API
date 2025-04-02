<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sport_id' => fake()->randomElement([1,2,3,4,5,6]),
            'name' => fake()->randomElement([
                'Shoes',
                'Balls',
                'Rackets',
                'Clothing',
                'Accessories',
                'Equipment',
                'Protection Gear',
                'Training Tools'
            ])
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sport>
 */
class SportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sports = [
            'Bóng Đá',
            'Bơi Lội',
            'Bóng Rổ',
            'Bóng Chuyền',
            'Gym',
            'Cầu Lông'
        ];
        
        return [
            'name' => fake()->randomElement($sports)
        ];
    }
}

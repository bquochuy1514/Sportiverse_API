<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sport::factory(6)->create();

        $sports = [
            ['name' => 'Bóng đá'],
            ['name' => 'Bóng rổ'],
            ['name' => 'Cầu lông'],
            ['name' => 'Bơi lội'],
            ['name' => 'Gym'],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }
}

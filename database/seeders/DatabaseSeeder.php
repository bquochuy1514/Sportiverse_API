<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $sports = [
        //     'Bóng Đá',
        //     'Bơi Lội',
        //     'Bóng Rổ',
        //     'Bóng Chuyền',
        //     'Gym',
        //     'Cầu Lông'
        // ];
    
        // foreach ($sports as $sport) {
        //     Sport::factory()->create(['name' => $sport]);
        // }

        // Call other seeders
        $this->call([
            SportSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            // DiscountSeeder::class,
        ]);
    }
}

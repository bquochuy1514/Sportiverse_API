<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '0342637682',
            'address' => '12B Trương Hán Siêu - Nha Trang - Khánh Hoà'
        ]);

        User::factory()->create([
            'name' => 'Bùi Quốc Huy',
            'email' => 'banavip12nt@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'customer',
            'phone' => '0342637682',
            'address' => '12B Trương Hán Siêu - Nha Trang - Khánh Hoà'
        ]);

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
            DiscountSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Discount::create([
            'code' => 'SPORT10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_value' => 500000,
            'max_discount' => 200000,
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'usage_limit' => 100,
        ]);

        // Mã giảm cố định 100.000đ
        Discount::create([
            'code' => 'WELCOME100K',
            'discount_type' => 'fixed',
            'discount_value' => 100000,
            'min_order_value' => 1000000,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'usage_limit' => 50,
        ]);

        // Mã giảm 20% không giới hạn số tiền giảm tối đa
        Discount::create([
            'code' => 'SUPER20',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'min_order_value' => 2000000,
            'start_date' => now(),
            'end_date' => now()->addDays(7), // Chỉ có hiệu lực 7 ngày
            'usage_limit' => 20,
        ]);
    }
}

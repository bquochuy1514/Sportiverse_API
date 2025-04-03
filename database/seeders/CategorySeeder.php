<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Sport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category::factory(10)->create();

        // Danh mục cho từng môn thể thao
        $categories = [
            // Bóng đá
            'Bóng đá' => [
                'Đồ bóng đá',
                'Giày bóng đá',
                'Bóng',
                'Găng tay thủ môn',
                'Bảo vệ ống chân',
                'Tất bóng đá',
                'Túi đựng đồ',
                'Phụ kiện bóng đá khác'
            ],
            
            // Bóng rổ
            'Bóng rổ' => [
                'Đồ bóng rổ',
                'Giày bóng rổ',
                'Bóng rổ',
                'Băng bảo vệ',
                'Vớ bóng rổ',
                'Phụ kiện bóng rổ khác'
            ],
            
            // Cầu lông
            'Cầu lông' => [
                'Vợt cầu lông',
                'Quả cầu lông',
                'Giày cầu lông',
                'Đồ cầu lông',
                'Túi đựng vợt',
                'Phụ kiện cầu lông khác'
            ],
            
            // Bơi lội
            'Bơi lội' => [
                'Đồ bơi nam',
                'Đồ bơi nữ',
                'Kính bơi',
                'Mũ bơi',
                'Phao tay',
                'Ván tập bơi',
                'Kẹp mũi',
                'Nút tai',
                'Phụ kiện bơi lội khác'
            ],
            
            // Gym
            'Gym' => [
                'Đồ gym nam',
                'Đồ gym nữ',
                'Găng tay tập gym',
                'Đai lưng tập gym',
                'Giày tập gym',
                'Dụng cụ tập',
                'Thực phẩm bổ sung',
                'Phụ kiện gym khác'
            ],
        ];
        
        // Thêm danh mục vào cơ sở dữ liệu
        foreach ($categories as $sportName => $categoryNames) {
            $sport = Sport::where('name', $sportName)->first();
            
            if ($sport) {
                foreach ($categoryNames as $categoryName) {
                    Category::create([
                        'sport_id' => $sport->id,
                        'name' => $categoryName
                    ]);
                }
            }
        }
    }
}

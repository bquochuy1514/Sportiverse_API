<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product::factory(10)->create();
        // Lấy một số category_id để tạo sản phẩm mẫu
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            echo "Không có danh mục nào trong cơ sở dữ liệu. Vui lòng chạy CategoriesTableSeeder trước.\n";
            return;
        }
        
        // Danh sách sản phẩm mẫu
        $products = [
            // Sản phẩm cho danh mục Áo bóng đá
            [
                'category_name' => 'Đồ bóng đá',
                'products' => [
                    [
                        'name' => 'Áo bóng đá Manchester United sân nhà 2023/24',
                        'description' => 'Áo bóng đá chính hãng Manchester United sân nhà mùa giải 2023/24, chất liệu cao cấp, thoáng khí.',
                        'price' => 799000,
                        'stock' => 50,
                        'image' => 'manchester_united_home.jpg'
                    ],
                    [
                        'name' => 'Áo bóng đá Barcelona sân khách 2023/24',
                        'description' => 'Áo bóng đá chính hãng Barcelona sân khách mùa giải 2023/24, thiết kế độc đáo, thoải mái khi mặc.',
                        'price' => 750000,
                        'stock' => 45,
                        'image' => 'barcelona_away.jpg'
                    ],
                ]
            ],
            
            // Sản phẩm cho danh mục Giày bóng đá
            [
                'category_name' => 'Giày bóng đá',
                'products' => [
                    [
                        'name' => 'Giày bóng đá Nike Mercurial Vapor 15',
                        'description' => 'Giày bóng đá Nike Mercurial Vapor 15 Elite FG, nhẹ và nhanh, phù hợp với sân cỏ tự nhiên.',
                        'price' => 2500000,
                        'stock' => 30,
                        'image' => 'nike_mercurial.jpg'
                    ],
                    [
                        'name' => 'Giày bóng đá Adidas X Speedportal',
                        'description' => 'Giày bóng đá Adidas X Speedportal, thiết kế hiện đại, tăng tốc độ và sự linh hoạt.',
                        'price' => 2300000,
                        'stock' => 25,
                        'image' => 'adidas_x.jpg'
                    ],
                ]
            ],
            
            // Sản phẩm cho danh mục Bóng rổ
            [
                'category_name' => 'Bóng rổ',
                'products' => [
                    [
                        'name' => 'Bóng rổ Spalding NBA Official',
                        'description' => 'Bóng rổ chính hãng Spalding NBA Official, kích thước số 7, sử dụng trong các trận đấu chuyên nghiệp.',
                        'price' => 1200000,
                        'stock' => 40,
                        'image' => 'spalding_nba.jpg'
                    ],
                    [
                        'name' => 'Bóng rổ Wilson Evolution',
                        'description' => 'Bóng rổ Wilson Evolution, kích thước số 7, cảm giác tốt, độ bám cao, phù hợp thi đấu trong nhà.',
                        'price' => 950000,
                        'stock' => 35,
                        'image' => 'wilson_evolution.jpg'
                    ],
                ]
            ],
            
            // Sản phẩm cho danh mục Vợt cầu lông
            [
                'category_name' => 'Vợt cầu lông',
                'products' => [
                    [
                        'name' => 'Vợt cầu lông Yonex Astrox 88D Pro',
                        'description' => 'Vợt cầu lông Yonex Astrox 88D Pro, thiết kế cho người chơi tấn công, khung carbon cao cấp.',
                        'price' => 4500000,
                        'stock' => 20,
                        'image' => 'yonex_astrox.jpg'
                    ],
                    [
                        'name' => 'Vợt cầu lông Li-Ning Aeronaut 9000i',
                        'description' => 'Vợt cầu lông Li-Ning Aeronaut 9000i, cân bằng đầu nặng, lực đánh mạnh, phù hợp cho người chơi tấn công.',
                        'price' => 3800000,
                        'stock' => 15,
                        'image' => 'lining_aeronaut.jpg'
                    ],
                ]
            ],
            
            // Sản phẩm cho danh mục Đồ bơi nam
            [
                'category_name' => 'Đồ bơi nam',
                'products' => [
                    [
                        'name' => 'Quần bơi nam Speedo Endurance+',
                        'description' => 'Quần bơi nam Speedo Endurance+, chất liệu bền, kháng chlorine, phù hợp bơi lội thường xuyên.',
                        'price' => 650000,
                        'stock' => 40,
                        'image' => 'speedo_endurance.jpg'
                    ],
                    [
                        'name' => 'Quần bơi nam Arena Solid',
                        'description' => 'Quần bơi nam Arena Solid, thiết kế thể thao, thoải mái khi bơi, màu xanh navy.',
                        'price' => 550000,
                        'stock' => 35,
                        'image' => 'arena_solid.jpg'
                    ],
                ]
            ],
            
            // Sản phẩm cho danh mục Găng tay tập gym
            [
                'category_name' => 'Găng tay tập gym',
                'products' => [
                    [
                        'name' => 'Găng tay tập gym Under Armour',
                        'description' => 'Găng tay tập gym Under Armour, bảo vệ lòng bàn tay, tăng độ bám khi tập với tạ.',
                        'price' => 450000,
                        'stock' => 50,
                        'image' => 'ua_gloves.jpg'
                    ],
                    [
                        'name' => 'Găng tay tập gym Adidas Essential',
                        'description' => 'Găng tay tập gym Adidas Essential, chất liệu thoáng khí, đệm lòng bàn tay dày, phù hợp tập nặng.',
                        'price' => 400000,
                        'stock' => 45,
                        'image' => 'adidas_gloves.jpg'
                    ],
                ]
            ],
        ];
        
        // Tạo sản phẩm
        foreach ($products as $categoryProducts) {
            $category = Category::where('name', $categoryProducts['category_name'])->first();
            
            if ($category) {
                foreach ($categoryProducts['products'] as $productData) {
                    Product::create([
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock' => $productData['stock'],
                        'image' => $productData['image'],
                    ]);
                    
                    echo "Đã tạo sản phẩm: {$productData['name']}\n";
                }
            } else {
                echo "Không tìm thấy danh mục: {$categoryProducts['category_name']}\n";
            }
        }
    }
}

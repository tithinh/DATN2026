<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $news = [
            [
                'title' => 'Top 10 ốp lưng iPhone 15 Pro Max đáng mua nhất năm 2026',
                'slug' => 'top-10-op-lung-iphone-15-pro-max-2026',
                'excerpt' => 'Khám phá những mẫu ốp lưng đẹp, bền, bảo vệ tốt nhất cho iPhone 15 Pro Max với đa dạng phong cách từ trong suốt, da cao cấp đến chống sốc quân sự.',
                'content' => 'Nội dung chi tiết về top 10 ốp lưng iPhone 15 Pro Max...',
                'image' => 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=800&h=450&fit=crop',
                'category' => 'Đánh giá',
                'author' => 'Nguyễn Văn An',
                'views' => 2500,
                'status' => 'published'
            ],
            [
                'title' => 'Cách chọn tai nghe Bluetooth phù hợp với nhu cầu sử dụng',
                'slug' => 'cach-chon-tai-nghe-bluetooth',
                'excerpt' => 'Hướng dẫn chi tiết giúp bạn chọn tai nghe không dây phù hợp nhất với lifestyle của mình.',
                'content' => 'Hướng dẫn chi tiết cách chọn tai nghe Bluetooth...',
                'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=250&fit=crop',
                'category' => 'Hướng dẫn',
                'author' => 'Trần Thị B',
                'views' => 1800,
                'status' => 'published'
            ],
            [
                'title' => 'Apple ra mắt chuẩn sạc nhanh mới cho iPhone 16 Series',
                'slug' => 'apple-sac-nhanh-iphone-16',
                'excerpt' => 'Tìm hiểu về công nghệ sạc nhanh mới nhất từ Apple hỗ trợ lên đến 50W.',
                'content' => 'Apple vừa công bố...',
                'image' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=250&fit=crop',
                'category' => 'Tin tức',
                'author' => 'Lê Văn C',
                'views' => 1500,
                'status' => 'published'
            ],
            [
                'title' => 'So sánh pin dự phòng Anker vs Xiaomi: Đâu là lựa chọn tốt?',
                'slug' => 'so-sanh-pin-du-phong-anker-xiaomi',
                'excerpt' => 'Đánh giá chi tiết hai thương hiệu pin sạc dự phòng phổ biến nhất hiện nay.',
                'content' => 'So sánh chi tiết...',
                'image' => 'https://images.unsplash.com/photo-1585338107529-13afc5f02586?w=400&h=250&fit=crop',
                'category' => 'So sánh',
                'author' => 'Phạm Văn D',
                'views' => 1200,
                'status' => 'published'
            ],
            [
                'title' => '5 mẹo sử dụng sạc không dây hiệu quả nhất',
                'slug' => 'meo-su-dung-sac-khong-day',
                'excerpt' => 'Những mẹo đơn giản giúp bạn sạc không dây nhanh hơn và an toàn hơn.',
                'content' => 'Mẹo sử dụng sạc không dây...',
                'image' => 'https://images.unsplash.com/photo-1628815113969-0487917f26eb?w=400&h=250&fit=crop',
                'category' => 'Mẹo hay',
                'author' => 'Nguyễn Thị E',
                'views' => 900,
                'status' => 'published'
            ],
            [
                'title' => 'Review dây đeo Apple Watch Ultra: Có đáng tiền?',
                'slug' => 'review-day-deo-apple-watch-ultra',
                'excerpt' => 'Đánh giá chi tiết dây đeo chính hãng Apple sau 3 tháng sử dụng.',
                'content' => 'Đánh giá dây đeo Apple Watch Ultra...',
                'image' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&h=250&fit=crop',
                'category' => 'Đánh giá',
                'author' => 'Trần Văn F',
                'views' => 800,
                'status' => 'published'
            ],
            [
                'title' => 'JBL ra mắt loa Bluetooth Flip 7 với pin 15 giờ',
                'slug' => 'jbl-flip-7-ra-mat',
                'excerpt' => 'Phiên bản nâng cấp của dòng loa di động bán chạy nhất thế giới.',
                'content' => 'JBL vừa giới thiệu...',
                'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&h=250&fit=crop',
                'category' => 'Tin tức',
                'author' => 'Lê Thị G',
                'views' => 750,
                'status' => 'published'
            ],
            [
                'title' => 'Hướng dẫn sạc iPhone đúng cách để kéo dài tuổi thọ pin',
                'slug' => 'huong-dan-sac-iphone-dung-cach',
                'excerpt' => 'Những sai lầm khi sạc iPhone có thể làm giảm tuổi thọ pin nhanh chóng.',
                'content' => 'Hướng dẫn chi tiết...',
                'image' => 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=400&h=250&fit=crop',
                'category' => 'Hướng dẫn',
                'author' => 'Nguyễn Văn H',
                'views' => 2000,
                'status' => 'published'
            ]
        ];

        foreach ($news as $item) {
            News::create($item);
        }
    }
}

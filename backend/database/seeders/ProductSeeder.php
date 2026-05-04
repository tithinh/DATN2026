<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample categories if none
        $categories = Category::pluck('category_id')->toArray();
        if (empty($categories)) {
            $cat1 = Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai']);
            $cat2 = Category::create(['name' => 'Laptop', 'slug' => 'laptop']);
            $categories = [$cat1->category_id, $cat2->category_id];
        }

        $products = [
            ['iPhone 15 Pro', 'iphone-15-pro', 25000000, 20],
            ['Samsung S24 Ultra', 'samsung-s24-ultra', 28000000, 15],
            ['MacBook Pro M3', 'macbook-pro-m3', 45000000, 8], // low stock
            ['Dell XPS 13', 'dell-xps-13', 32000000, 5], // low stock
            ['AirPods Pro 2', 'airpods-pro-2', 6500000, 30],
            ['Sony WH-1000XM5', 'sony-wh-1000xm5', 12000000, 12],
            ['iPad Air M2', 'ipad-air-m2', 18000000, 25],
            ['Pixel 8 Pro', 'pixel-8-pro', 22000000, 3], // low stock
            ['Surface Laptop 6', 'surface-laptop-6', 35000000, 18],
            ['Galaxy Watch 6', 'galaxy-watch-6', 8500000, 7], // low stock
            ['ThinkPad X1 Carbon', 'thinkpad-x1-carbon', 38000000, 22],
            ['OnePlus 12', 'oneplus-12', 20000000, 40],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['slug' => $p[1]],
                [
                    'name' => $p[0],
                    'category_id' => $categories[array_rand($categories)],
                    'base_price' => $p[2],
                    'stock_total' => $p[3],
                    'is_visible' => true,
                    'is_featured' => rand(0, 1),
                ]
            );
        }

        echo "✅ Seeded 12 sample products (4 low stock <10)\n";
    }
}


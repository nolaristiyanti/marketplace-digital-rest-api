<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProductCategory;
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
        $sellerA = User::where('email', 'sellerA@mail.com')->first();
        $sellerB = User::where('email', 'sellerB@mail.com')->first();

        $web = ProductCategory::where('name', 'Web Development')->first();
        $ui = ProductCategory::where('name', 'UI Kit')->first();

        // ======================
        // Seller A Products
        // ======================
        Product::create([
            'seller_id' => $sellerA->id,
            'category_id' => $web->id,
            'title' => 'Laravel Mastery',
            'description' => 'Belajar Laravel dari basic sampai advance',
            'stock' => 999,
            'price' => 120000,
            'rating' => 9.0,
            'download_count' => 300,
            'file_path' => 'products/laravel.pdf',
            'thumbnail' => 'thumb/laravel.png',
            'status' => 'active',
        ]);
        Product::create([
            'seller_id' => $sellerA->id,
            'category_id' => $ui->id,
            'title' => 'Admin Dashboard UI Kit',
            'description' => 'UI kit siap pakai',
            'stock' => 999,
            'price' => 80000,
            'rating' => 8.0,
            'download_count' => 150,
            'file_path' => 'products/ui.fig',
            'thumbnail' => 'thumb/ui.png',
            'status' => 'active',
        ]);

        // ======================
        // Seller B Products
        // ======================

        Product::create([
            'seller_id' => $sellerB->id,
            'category_id' => $web->id,
            'title' => 'Spring Boot API Guide',
            'description' => 'Belajar REST API dengan Spring Boot',
            'stock' => 999,
            'price' => 100000,
            'rating' => 7.5,
            'download_count' => 200,
            'file_path' => 'products/spring.pdf',
            'thumbnail' => 'thumb/spring.png',
            'status' => 'active',
        ]);
        Product::create([
            'seller_id' => $sellerB->id,
            'category_id' => $ui->id,
            'title' => 'Mobile App UI Kit',
            'description' => 'UI kit untuk aplikasi mobile',
            'stock' => 999,
            'price' => 60000,
            'rating' => 6.5,
            'download_count' => 50,
            'file_path' => 'products/mobile.fig',
            'thumbnail' => 'thumb/mobile.png',
            'status' => 'active',
        ]);
    }
}

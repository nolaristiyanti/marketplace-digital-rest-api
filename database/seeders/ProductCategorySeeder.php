<?php

namespace Database\Seeders;

use App\Models\ProductCategory;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategory::create([
            'name' => 'Web Development',
            'description' => 'Programming & coding resources',
            'icon' => 'code.png',
        ]);

        ProductCategory::create([
            'name' => 'UI Kit',
            'description' => 'Design assets',
            'icon' => 'figma.png',
        ]);
    }
}

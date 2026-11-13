<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and accessories',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Phones',
            'slug' => 'phones',
            'description' => 'Smartphones and mobile devices',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Laptops and portable computers',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}

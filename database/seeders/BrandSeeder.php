<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
            'description' => 'Apple technology products',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Brand::create([
            'name' => 'Samsung',
            'slug' => 'samsung',
            'description' => 'Samsung electronics and mobile devices',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Brand::create([
            'name' => 'Dell',
            'slug' => 'dell',
            'description' => 'Dell computers and technology products',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
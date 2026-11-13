<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $iphone = Product::where('slug', 'iphone-17')->firstOrFail();
        $galaxy = Product::where('slug', 'galaxy-s26')->firstOrFail();
        $dell = Product::where('slug', 'dell-xps-15')->firstOrFail();

        ProductImage::create([
            'product_id' => $iphone->id,
            'path' => 'products/iphone-17-1.jpg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        ProductImage::create([
            'product_id' => $iphone->id,
            'path' => 'products/iphone-17-2.jpg',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        ProductImage::create([
            'product_id' => $galaxy->id,
            'path' => 'products/galaxy-s26-1.jpg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        ProductImage::create([
            'product_id' => $galaxy->id,
            'path' => 'products/galaxy-s26-2.jpg',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        ProductImage::create([
            'product_id' => $dell->id,
            'path' => 'products/dell-xps-15-1.jpg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $phones = Category::where('slug', 'phones')->firstOrFail();
        $laptops = Category::where('slug', 'laptops')->firstOrFail();

        $apple = Brand::where('slug', 'apple')->firstOrFail();
        $samsung = Brand::where('slug', 'samsung')->firstOrFail();
        $dell = Brand::where('slug', 'dell')->firstOrFail();

        Product::create([
            'name' => 'iPhone 17',
            'short_description' => 'Latest Apple smartphone',
            'description' => 'A powerful smartphone with advanced features.',
            'price' => 999.99,
            'stock' => 25,
            'sku' => 'APL-IP17-001',
            'slug' => 'iphone-17',
            'status' => 'active',
            'category_id' => $phones->id,
            'brand_id' => $apple->id,
        ]);

        Product::create([
            'name' => 'Galaxy S26',
            'short_description' => 'Samsung flagship smartphone',
            'description' => 'A premium Samsung smartphone.',
            'price' => 899.99,
            'stock' => 30,
            'sku' => 'SAM-GS26-001',
            'slug' => 'galaxy-s26',
            'status' => 'active',
            'category_id' => $phones->id,
            'brand_id' => $samsung->id,
        ]);

        Product::create([
            'name' => 'Dell XPS 15',
            'short_description' => 'Premium Dell laptop',
            'description' => 'A powerful laptop for professional use.',
            'price' => 1499.99,
            'stock' => 15,
            'sku' => 'DEL-XPS15-001',
            'slug' => 'dell-xps-15',
            'status' => 'active',
            'category_id' => $laptops->id,
            'brand_id' => $dell->id,
        ]);
    }
}
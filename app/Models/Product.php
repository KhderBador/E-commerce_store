<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;


class Product extends Model
{
    //
    protected $fillable = [
    'name',
    'short_description',
    'description',
    'price',
    'stock',
    'sku',
    'slug',
    'status',
    'category_id',
    'brand_id',
];
protected function casts(): array
{
    return [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];
}

public function category()
{
    return $this->belongsTo(Category::class);
}

public function brand()
{
    return $this->belongsTo(Brand::class);
}
public function images()
{
    return $this->hasMany(ProductImage::class);
}

public function cartItems()
{
    return $this->hasMany(CartItem::class);
}

public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

}

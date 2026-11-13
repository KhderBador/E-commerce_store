<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
    'name',
    'slug',
    'description',
    'parent_id',
    'is_active',
    'sort_order',
];
protected function casts(): array
{
    return [
        'parent_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
    public function products()
{
    return $this->hasMany(Product::class);
}
public function parent()
{
    return $this->belongsTo(Category::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(Category::class, 'parent_id');
}
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductIndexRequest $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with([
                'category',
                'brand',
                'images',
            ])
            ->where('status', 'active')
            ->when(
                $request->category_id,
                fn($query, $categoryId) =>
                $query->where('category_id', $categoryId)
            )
            ->when(
                $request->brand_id,
                fn($query, $brandId) =>
                $query->where('brand_id', $brandId)
            )
            ->when(
                $request->search,
                fn($query, $search) =>
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->min_price,
                fn($query, $price) =>
                $query->where('price', '>=', $price)
            )
            ->when(
                $request->max_price,
                fn($query, $price) =>
                $query->where('price', '<=', $price)
            )
            ->when(
                $request->sort,
                function ($query, $sort) {
                    match ($sort) {
                        'newest' => $query->orderBy('id', 'desc'),
                        'oldest' => $query->orderBy('id', 'asc'),
                        'price_asc' => $query->orderBy('price', 'asc'),
                        'price_desc' => $query->orderBy('price', 'desc'),
                        'name_asc' => $query->orderBy('name', 'asc'),
                        'name_desc' => $query->orderBy('name', 'desc'),
                    };
                },
                fn($query) => $query->orderBy('id', 'desc')
            )
            ->paginate(
                $request->integer('per_page', 20)
            );

        return ProductResource::collection($products);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): ProductResource
    {
        

        $product = Product::create(
            $request->validated()
        );

        $product->load([
            'category.parent',
            'brand',
            'images',
        ]);

        return new ProductResource($product);
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product): ProductResource
    {
        $product->load([
            'category.parent',
            'brand',
            'images',
        ]);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateProductRequest $request,
        Product $product
    ): ProductResource {
        $product->update(
            $request->validated()
        );

        $product->load([
            'category.parent',
            'brand',
            'images',
        ]);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Product $product): JsonResponse
{
    foreach ($product->images as $image) {
        Storage::disk('public')->delete($image->path);
    }
    $product->delete();

    return response()->json([
        'message' => 'Product deleted successfully.',
    ]);
}
}

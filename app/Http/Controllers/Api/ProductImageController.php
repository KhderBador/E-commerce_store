<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\UpdateProductImageRequest;
use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product): AnonymousResourceCollection
    {
        $images = $product->images()
            ->orderBy('sort_order')
            ->get();

        return ProductImageResource::collection($images);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreProductImageRequest $request,
        Product $product
    ): ProductImageResource {
        if ($request->boolean('is_primary')) {
            $product->images()->update([
                'is_primary' => false,
            ]);
        }

        $path = $request->file('image')->store(
            'products',
            'public'
        );

        $image = $product->images()->create([
            'path' => $path,
            'is_primary' => $request->boolean('is_primary'),
            'sort_order' => $request->integer('sort_order', 0),
        ]);

        return new ProductImageResource($image);
    }
    /**
     * Display the specified resource.
     */
    public function show(
        Product $product,
        ProductImage $image
    ): ProductImageResource {
        return new ProductImageResource($image);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateProductImageRequest $request,
        Product $product,
        ProductImage $image
    ): ProductImageResource {
        if ($request->boolean('is_primary')) {
            $product->images()
                ->whereKeyNot($image->id)
                ->update([
                    'is_primary' => false,
                ]);
        }

        $image->update(
            $request->validated()
        );

        return new ProductImageResource($image);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Product $product,
        ProductImage $image
    ): JsonResponse {
        $wasPrimary = $image->is_primary;

        Storage::disk('public')->delete($image->path);

        $image->delete();

        if ($wasPrimary) {
            $nextPrimary = $product->images()
                ->orderBy('sort_order')
                ->first();
                
            if ($nextPrimary) {
                $product->images()
                    ->whereKey($nextPrimary->id)
                    ->update([
                        'is_primary' => true,
                    ]);
            }
        }

        return response()->json([
            'message' => 'Product image deleted successfully.',
        ]);
    }
}

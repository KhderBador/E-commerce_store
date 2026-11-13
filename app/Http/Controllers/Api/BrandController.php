<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;

class BrandController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return BrandResource::collection($brands);
    }

    public function show(Brand $brand): BrandResource
    {
        return new BrandResource($brand);
    }

    public function store(StoreBrandRequest $request): BrandResource
{
    $brand = Brand::create($request->validated());

    return new BrandResource($brand);

}

public function update(
    UpdateBrandRequest $request,
    Brand $brand
): BrandResource {
    $brand->update($request->validated());
   

    return new BrandResource($brand);
}


public function destroy(Brand $brand)
{
    // Prevent deleting a brand that has products
    if ($brand->products()->exists()) {
        return response()->json([
            'message' => 'Cannot delete a brand that has products.',
        ], 422);
    }

    $brand->delete();

    return response()->json([
        'message' => 'Brand deleted successfully.',
    ]);
}

       
    }

    // Prevent deleting a category that has products
  


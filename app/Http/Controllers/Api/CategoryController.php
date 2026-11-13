<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }


    public function show(Category $category): CategoryResource
{
    $category->load([
        'children',
        'parent',
    ]);

    return new CategoryResource($category);
}

public function store(StoreCategoryRequest $request): CategoryResource
{
    $category = Category::create($request->validated());

    return new CategoryResource($category);
}

public function update(
    UpdateCategoryRequest $request,
    Category $category
): CategoryResource {
    $category->update($request->validated());
   

    return new CategoryResource($category);
}

public function destroy(Category $category)
{
    // Prevent deleting a category that has children
    if ($category->children()->exists()) {
        return response()->json([
            'message' => 'Cannot delete a category that has child categories.',
        ], 422);
    }

    // Prevent deleting a category that has products
    if ($category->products()->exists()) {
        return response()->json([
            'message' => 'Cannot delete a category that has products.',
        ], 422);
    }

    $category->delete();

    return response()->json([
        'message' => 'Category deleted successfully.',
    ]);
}
}
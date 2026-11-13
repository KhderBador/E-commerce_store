<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;






//  Category Routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store'])
    ->middleware([
        'auth:sanctum',
        'can:create,App\Models\Category',
    ]);

Route::patch('/categories/{category}', [CategoryController::class, 'update'])
    ->middleware([
        'auth:sanctum',
        'can:update,category',
    ]);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
    ->middleware([
        'auth:sanctum',
        'can:delete,category',
    ]);


// Brand Routes
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{brand}', [BrandController::class, 'show']);
Route::post('/brands', [BrandController::class, 'store'])
    ->middleware([
        'auth:sanctum',
        'can:create,App\Models\Brand',
    ]);

Route::patch('/brands/{brand}', [BrandController::class, 'update'])
    ->middleware([
        'auth:sanctum',
        'can:update,brand',
    ]);

Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])
    ->middleware([
        'auth:sanctum',
        'can:delete,brand',
    ]);


// Product Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store'])->middleware([
    'auth:sanctum',
    'can:create,App\Models\Product',
]);
Route::patch('/products/{product}', [ProductController::class, 'update'])->middleware([
    'auth:sanctum',
    'can:update,product',
]);
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware([
    'auth:sanctum',
    'can:delete,product',
]);


//Product Image Routes
Route::scopeBindings()->group(function () {

    Route::get(
        '/products/{product}/images',
        [ProductImageController::class, 'index']
    );
    Route::get(
        '/products/{product}/images/{image}',
        [ProductImageController::class, 'show']
    );
    Route::post(
        '/products/{product}/images',
        [ProductImageController::class, 'store']
    )->middleware([
                'auth:sanctum',
                'can:create,App\Models\ProductImage',
            ]);

    Route::patch(
        '/products/{product}/images/{image}',
        [ProductImageController::class, 'update']
    )->middleware([
                'auth:sanctum',
                'can:update,image',
            ]);

    Route::delete(
        '/products/{product}/images/{image}',
        [ProductImageController::class, 'destroy']
    )->middleware([
                'auth:sanctum',
                'can:delete,image',
            ]);
});


// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me'])
    ->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');


// Cart Routes
Route::get('/cart', [CartController::class, 'index'])
    ->middleware('auth:sanctum');
Route::post('/cart/items', [CartController::class, 'store'])
    ->middleware('auth:sanctum');
Route::patch('/cart/items/{item}', [CartController::class, 'update'])
    ->middleware('auth:sanctum');
Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])
    ->middleware('auth:sanctum');
Route::delete('/cart', [CartController::class, 'clear'])
    ->middleware('auth:sanctum');
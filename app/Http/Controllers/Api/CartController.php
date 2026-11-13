<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\AddToCartRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    public function index(Request $request): CartResource
    {
        $cart = $request->user()
            ->cart()
            ->with([
                'items.product',
            ])
            ->first();

        if (!$cart) {
            $cart = $request->user()->cart()->create();
        }

        return new CartResource($cart);
    }



    public function store(AddToCartRequest $request): CartResource
{
    $user = $request->user();

    $cart = $user->cart()->firstOrCreate([]);

    $product = Product::findOrFail($request->product_id);

    if ($product->status !== 'active') {
        abort(422, 'This product is not available.');
    }

    $quantity = $request->integer('quantity');

    if ($quantity > $product->stock) {
        abort(422, 'The requested quantity is not available.');
    }

    $item = $cart->items()
        ->where('product_id', $product->id)
        ->first();

    if ($item) {
        $newQuantity = $item->quantity + $quantity;

        if ($newQuantity > $product->stock) {
            abort(422, 'The requested quantity is not available.');
        }

        $item->update([
            'quantity' => $newQuantity,
        ]);
    } else {
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }

    $cart->load('items.product');

    return new CartResource($cart);
}

public function update(
    UpdateCartItemRequest $request,
    CartItem $item
): CartResource {
    $cart = $request->user()
        ->cart()
        ->firstOrCreate([]);

    if ($item->cart_id !== $cart->id) {
        abort(403, 'You are not allowed to modify this cart item.');
    }

    $product = $item->product;

    if ($product->status !== 'active') {
        abort(422, 'This product is not available.');
    }

    $quantity = $request->integer('quantity');

    if ($quantity > $product->stock) {
        abort(422, 'The requested quantity is not available.');
    }

    $item->update([
        'quantity' => $quantity,
    ]);

    $cart->load('items.product');

    return new CartResource($cart);
}

public function destroy(
    Request $request,
    CartItem $item
): CartResource {
    $cart = $request->user()
        ->cart()
        ->firstOrCreate([]);

    if ($item->cart_id !== $cart->id) {
        abort(403, 'You are not allowed to modify this cart item.');
    }

    $item->delete();

    $cart->load('items.product');

    return new CartResource($cart);
}




public function clear(Request $request): CartResource
{
    $cart = $request->user()
        ->cart()
        ->firstOrCreate([]);

    $cart->items()->delete();

    $cart->load('items.product');

    return new CartResource($cart);
}
}

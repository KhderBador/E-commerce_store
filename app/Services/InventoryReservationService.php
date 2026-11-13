<?php

namespace App\Services;

use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryReservationService
{
    private const RESERVATION_MINUTES = 15;

 public function reserve(Order $order): void
{
    DB::transaction(function () use ($order) {

        // Lock the order itself to prevent concurrent reservation attempts
        $order = Order::query()
            ->whereKey($order->id)
            ->lockForUpdate()
            ->firstOrFail();

        $order->load('items');

        // An order must contain at least one item
        if ($order->items->isEmpty()) {
            throw ValidationException::withMessages([
                'order' => 'The order must contain at least one item.',
            ]);
        }

        // An order can only have its inventory reserved once
        if ($order->inventoryReservations()->exists()) {
            throw ValidationException::withMessages([
                'reservation' => 'Inventory has already been reserved for this order.',
            ]);
        }

        // Inventory reservation is only valid for pending orders
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'order' => 'Inventory can only be reserved for a pending order.',
            ]);
        }

        $items = $order->items
            ->sortBy('product_id')
            ->values();

        $reservedAt = now();

        $expiresAt = $reservedAt
            ->copy()
            ->addMinutes(self::RESERVATION_MINUTES);

        foreach ($items as $item) {

            if (!$item->product_id) {
                throw ValidationException::withMessages([
                    'product' => 'One of the order items is no longer available.',
                ]);
            }

            if ($item->quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Order item quantity must be at least 1.',
                ]);
            }

            $product = Product::query()
                ->whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw ValidationException::withMessages([
                    'product' => 'One of the products is no longer available.',
                ]);
            }

            if ($product->status !== 'active') {
                throw ValidationException::withMessages([
                    'product' => "Product {$product->id} is no longer available.",
                ]);
            }

            if ($product->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'product' => "Insufficient stock for product {$product->id}.",
                ]);
            }

            $product->decrement('stock', $item->quantity);

            InventoryReservation::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item->quantity,
                'status' => 'active',
                'expires_at' => $expiresAt,
                'reserved_at' => $reservedAt,
            ]);
        }
    });
}
    public function release(InventoryReservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {

            $reservation = InventoryReservation::query()
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * A reservation can only release stock once.
             */
            if ($reservation->status !== 'active') {
                return;
            }

            /*
             * Lock the product so another inventory operation
             * cannot modify its stock at the same time.
             */
            $product = Product::query()
                ->whereKey($reservation->product_id)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw ValidationException::withMessages([
                    'product' => 'The reserved product no longer exists.',
                ]);
            }

            /*
             * Return the reserved quantity to available stock.
             */
            $product->increment('stock', $reservation->quantity);

            /*
             * Mark the reservation as released.
             */
            $reservation->update([
                'status' => 'released',
                'released_at' => now(),
            ]);
        });
    }

    public function confirm(InventoryReservation $reservation): void
{
    DB::transaction(function () use ($reservation) {

        $reservation = InventoryReservation::query()
            ->whereKey($reservation->id)
            ->lockForUpdate()
            ->firstOrFail();

        /*
         * A reservation can only be confirmed while it is active.
         */
        if ($reservation->status !== 'active') {
            throw ValidationException::withMessages([
                'reservation' => 'This reservation cannot be confirmed.',
            ]);
        }

        /*
         * The reservation is already holding the stock.
         *
         * Therefore, confirming it does NOT change products.stock.
         */
        $reservation->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    });
}
public function expire(InventoryReservation $reservation): void
{
    DB::transaction(function () use ($reservation) {

        $reservation = InventoryReservation::query()
            ->whereKey($reservation->id)
            ->lockForUpdate()
            ->firstOrFail();

        /*
         * Only active reservations can expire.
         */
        if ($reservation->status !== 'active') {
            return;
        }

        /*
         * Make sure the reservation has actually expired.
         */
        if ($reservation->expires_at->isFuture()) {
            return;
        }

        /*
         * Lock the product before changing its stock.
         */
        $product = Product::query()
            ->whereKey($reservation->product_id)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw ValidationException::withMessages([
                'product' => 'The expired product no longer exists.',
            ]);
        }

        /*
         * Return the reserved quantity to available stock.
         */
        $product->increment('stock', $reservation->quantity);

        /*
         * Mark the reservation as expired.
         */
        $reservation->update([
            'status' => 'expired',
            'released_at' => now(),
        ]);
    });
}
}




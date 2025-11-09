<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Return a collection of orders with customer and items.
     */
    public function listOrders(int $perPage = 10)
    {
        return Order::with(['customer', 'items.product'])
            ->orderByDesc('ordered_at')
            ->paginate($perPage);
    }

    public function getOrder(int $id): Order
    {
        return Order::with(['customer', 'items.product'])
            ->findOrFail($id);
    }

    /**
     * Create a new order from validated data.
     *
     * @param  array  $data  (from StoreOrderRequest::validated())
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_id'  => $data['customer_id'],
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'status'       => $data['status'] ?? 'pending',
                'ordered_at'   => now(),
                'total'        => 0,
            ]);

            $total = 0;

            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                $quantity  = $itemData['quantity'];
                $unitPrice = $product->price;
                $lineTotal = $unitPrice * $quantity;

                $total += $lineTotal;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update(['total' => $total]);

            return $order->fresh(['customer', 'items.product']);
        });
    }

    public function updateStatus(int $id, string $status): Order
    {
        $order = Order::findOrFail($id);

        $order->status = $status;
        $order->save();

        return $order->fresh(['customer', 'items.product']);
    }

    public function deleteOrder(int $id): void
    {
        $order = Order::findOrFail($id);

        // This will also delete related order_items because of cascadeOnDelete()
        $order->delete();
    }
}

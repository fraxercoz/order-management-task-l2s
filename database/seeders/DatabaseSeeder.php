<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Customers
        Customer::factory()
            ->count(20)
            ->create();

        // Products
        Product::factory()
            ->count(30)
            ->create();

        // Orders + items
        $customers = Customer::all();
        $products  = Product::all();

        // create 30 demo orders
        foreach (range(1, 30) as $i) {
            DB::transaction(function () use ($customers, $products) {
                $customer = $customers->random();

                $order = Order::create([
                    'customer_id'  => $customer->id,
                    'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                    'status'       => 'pending',
                    'ordered_at'   => now()->subDays(rand(0, 30)),
                    'total'        => 0, // will update after items
                ]);

                $total = 0;

                // each order gets 1–5 items
                $itemsCount = rand(1, 5);
                $usedProductIds = [];

                foreach (range(1, $itemsCount) as $j) {
                    $product = $products->whereNotIn('id', $usedProductIds)->random();
                    $usedProductIds[] = $product->id;

                    $quantity  = rand(1, 5);
                    $unitPrice = $product->price;
                    $lineTotal = $unitPrice * $quantity;
                    $total    += $lineTotal;

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'unit_price' => $unitPrice,
                        'line_total' => $lineTotal,
                    ]);
                }

                $order->update(['total' => $total]);
            });
        }
    }
}

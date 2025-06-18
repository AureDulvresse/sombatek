<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $shops = Shop::all();
        $shippingMethods = ['standard', 'express', 'pickup'];
        $paymentMethods = ['card', 'paypal', 'bank_transfer'];
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        foreach ($customers as $customer) {
            // Créer 1-3 commandes par client
            $orderCount = rand(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $shop = $shops->random();
                $products = Product::where('shop_id', $shop->id)->inRandomOrder()->take(rand(1, 5))->get();

                $subtotal = 0;
                $orderItems = [];

                foreach ($products as $product) {
                    $quantity = rand(1, 3);
                    $price = $product->sale_price ?? $product->price;
                    $itemSubtotal = $price * $quantity;
                    $subtotal += $itemSubtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $itemSubtotal,
                        'options' => null
                    ];
                }

                $shippingCost = rand(5, 20);
                $tax = $subtotal * 0.20; // 20% TVA
                $total = $subtotal + $tax + $shippingCost;
                $commissionAmount = $total * ($shop->commission_rate / 100);
                $shippingMethod = $shippingMethods[array_rand($shippingMethods)];
                $status = $orderStatuses[array_rand($orderStatuses)];
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                $order = Order::create([
                    'user_id' => $customer->id,
                    'shop_id' => $shop->id,
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shippingCost,
                    'total' => $total,
                    'commission_amount' => $commissionAmount,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'shipping_method' => $shippingMethod,
                    'shipping_cost' => $shippingCost,
                    'shipping_address' => fake()->address(),
                    'billing_address' => fake()->address(),
                    'tracking_number' => $status === 'shipped' ? 'TRK-' . strtoupper(Str::random(10)) : null,
                    'notes' => fake()->optional()->sentence()
                ]);

                // Ajouter les produits à la commande
                foreach ($orderItems as $item) {
                    $order->items()->create($item);
                }
            }
        }
    }
}

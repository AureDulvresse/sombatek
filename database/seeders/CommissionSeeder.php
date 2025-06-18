<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Database\Seeder;

class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = Shop::all();
        $orders = Order::where('status', 'completed')->get();

        foreach ($shops as $shop) {
            // Créer des commissions pour les commandes complétées
            $shopOrders = $orders->where('shop_id', $shop->id);

            foreach ($shopOrders as $order) {
                Commission::create([
                    'shop_id' => $shop->id,
                    'order_id' => $order->id,
                    'amount' => $order->commission_amount,
                    'rate' => $shop->commission_rate,
                    'status' => 'paid',
                    'payment_date' => now(),
                    'payment_method' => 'bank_transfer',
                    'reference' => 'COM-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'notes' => 'Commission pour la commande ' . $order->order_number
                ]);
            }
        }
    }
}

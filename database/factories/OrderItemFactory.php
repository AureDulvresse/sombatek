<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $this->faker->randomFloat(2, 10, 100);
        $subtotal = $quantity * $price;

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name' => $this->faker->words(3, true),
            'product_sku' => strtoupper($this->faker->bothify('SKU-####-????')),
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal,
            'options' => $this->faker->boolean(30) ? [
                'color' => $this->faker->randomElement(['Rouge', 'Bleu', 'Vert', 'Noir', 'Blanc']),
                'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            ] : null,
        ];
    }
}

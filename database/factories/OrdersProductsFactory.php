<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProducts>
 */
class OrdersProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),// Crea una instancia de Order para order_id
            'product_id' => Product::factory(),
            'cantidad' => $this->faker->numberBetween(1, 1000),// Genera una cantidad aleatoria
        ];
    }
}

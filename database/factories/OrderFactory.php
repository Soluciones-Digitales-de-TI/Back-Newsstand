<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->numberBetween(1, 3),
            'total' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}

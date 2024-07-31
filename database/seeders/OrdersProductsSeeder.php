<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrdersProducts;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        foreach ($orders as $order) {
            foreach ($products->random(5) as $product) {
                OrdersProducts::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'cantidad' => rand(1, 10), // Cantidad aleatoria entre 1 y 10
                ]);
            }
        }
    }
}

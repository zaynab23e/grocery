<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderImage;



class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
    $order = Order::create([
        'user_id' => 1,
        'total_price' => 200.00,
        'delivery_price' => 20.00,
        'order_status' => 'pending',
        'order_date' => now(),
    ]);

    OrderImage::create([
        'order_id' => $order->id,
        'image_path' => 'orders/example.jpg',
    ]);
    }
}

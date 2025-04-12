<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Product::create([
            'name' => 'SHIRT',
    'category_id' => 1, // تأكدي أن لديك فئة (Category) أو عدّليها
    'price' => 100,
    'quantity' => 10,
    'stock_status' => 'in_stock',
    'image_path' => 'products/OIP.jpg', // تأكدي أن لديك الصورة في المسار الصحيح
        ]);
    }
}

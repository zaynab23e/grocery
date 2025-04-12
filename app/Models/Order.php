<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'delivery_price',
        'order_status',
        'order_date',
        'paymob_product_url', // إضافة العمود الجديد
        'pay_status',          // إضافة العمود الجديد
        'pay_type',            // إضافة العمود الجديد
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(OrderImage::class);
    }

       // protected $casts = [
    //     'order_date' => 'datetime',
    //     'total_price' => 'decimal:2',
    //     'delivery_price' => 'decimal:2',
    // ];

}

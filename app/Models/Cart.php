<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'quantity', 'price', 'status'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getProductImageAttribute()
    {
        return $this->product && $this->product->images()->exists()
            ? asset('storage/' . $this->product->images()->first()->image_path)
            : null;
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->price;
    }

    protected $appends = ['product_image', 'total_price'];
}

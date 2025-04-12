<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'name',
    //     'category_id',
    //     'price',
    //     'quantity',
    //     'stock_status',
    
    // ];

    protected $fillable = ['name', 'category_id', 'price', 'quantity', 'stock_status', 'image_path'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
{
    return $this->hasMany(ProductImage::class);
}




public function FavoriteProduct()
{
    return $this->hasMany(FavoriteProduct::class, 'product_id');
}

      //  protected $casts = [
    //     'price' => 'decimal:2',
    // ]; 

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
    

}

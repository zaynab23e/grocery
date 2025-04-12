<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'address',
        'image',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ]; 

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function favourites()
{
    return $this->belongsToMany(Product::class, 'favourites', 'user_id', 'product_id')->withTimestamps();
}

    public function favoriteProducts()
    {
        return $this->hasMany(FavoriteProduct::class, 'user_id');
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'user_id');
    }
    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'user_id');
    // }
//     public function categories()
//     {
//         return $this->hasMany(Category::class, 'user_id');
//     }
}

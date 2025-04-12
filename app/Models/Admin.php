<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'address',
        'image',
        'password',
        'plain_password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

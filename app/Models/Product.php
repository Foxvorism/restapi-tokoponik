<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'description', 'type'];

    public function product_pics()
    {
        return $this->hasMany(ProductPic::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    
    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}

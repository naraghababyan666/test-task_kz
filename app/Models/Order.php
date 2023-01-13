<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'customer_name',
        'email',
        'phone'
    ];

    public function product(){
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
}

<?php

namespace App\Models\ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_order_products';

    public $timestamps = false;

    public $fillable = ['order_id', 'product_id', 'quantity', 'unit_price'];


}

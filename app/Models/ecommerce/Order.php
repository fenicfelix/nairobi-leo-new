<?php

namespace App\Models\ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_orders';

    public $fillable = [
        'order_number', 'requested_by', 'phone_number', 'email', 'order_value', 'requested_on', 'status_id', 'completed_on', 'completed_by'
    ];
}

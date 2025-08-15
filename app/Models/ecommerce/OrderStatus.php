<?php

namespace App\Models\ecommerce;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatus extends Status
{
    use HasFactory;

    public $table = 'ecommerce_order_statuses';
}

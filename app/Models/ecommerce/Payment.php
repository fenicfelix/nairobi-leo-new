<?php

namespace App\Models\ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public $fillable = [
        "order_id", "identifier", "requested_amount", "amount_paid", "merchant_request_id", "checkout_request_id", "request_response", "customer_request_response", "trx_result_response", "trx_result_message", "trx_receipt"
    ];
}

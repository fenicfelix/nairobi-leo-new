<?php

use App\Models\ecommerce\Order;

function get_order_number($order)
{
    $previous_order = Order::where('id', '<', $order->id)->orderBy('id', 'DESC')->first();
    if ($previous_order) {
        $order_number = $previous_order->order_number;
        return ++$order_number;
    } else return 'AAA0001';
}

function clean_phone_number($phone_number)
{
    $phone_number = "254" . substr(str_replace(" ", "", $phone_number), -9);
    return $phone_number;
}
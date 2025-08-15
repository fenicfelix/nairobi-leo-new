<?php

namespace App\Http\Livewire;

use App\Models\ecommerce\Order;
use App\Models\ecommerce\Payment;
use App\Models\Post;
use Livewire\Component;

class PodcastPaymentComponent extends Component
{
    public $podcast;
    public $title;
    public $mpesa_number;
    public $unit_measurement;
    public $unit_price;
    public $quantity;
    public $total_price;
    public $confirmed;
    public $order_number;

    public $order;

    public $identifier;
    public $response_code;
    public $response_message;


    public $listeners = [
        'selectPodcast', 'updateTotalPrice', 'paymentCallbackReceived'
    ];

    public function mount()
    {
        $this->quantity = 1;
        $this->total_price = 0;
        $this->confirmed = false;
    }

    public function selectPodcast($podcastId)
    {
        $this->podcast = Post::with('product')->where('id', $podcastId)->first();
        if ($this->podcast) {
            $product = $this->podcast->product;

            $this->title = $this->podcast->title;
            $this->unit_measurement = $product->unit_measurement;
            $this->unit_price = $product->discounted_price ?? $product->unit_price;
            $this->total_price = ($this->unit_price * $this->quantity);
        }
    }

    public function updateTotalPrice()
    {
        $this->total_price = ($this->unit_price * $this->quantity);
    }

    public function store()
    {
        if ($this->confirmed) {
            $this->response_message = "Your order is being placed.";
            //Place order and trigger mpesa prompt
            $this->order = Order::query()->create(
                [
                    'phone_number' => $this->mpesa_number,
                    'order_value' => $this->total_price,
                    'requested_on' => date('Y-m-d H:i:s'),
                    'status_id' => 1,
                ]
            );
            $this->order_number = get_order_number($this->order);
            $this->order->order_number = $this->order_number;
            $this->order->save();

            $this->response_message = "Sending payment request to M-Pesa.";
            $this->identifier = generate_identifier();
            $payment = Payment::query()->create(
                [
                    "identifier" => $this->identifier,
                    "order_id" => $this->order->id,
                    "requested_amount" => $this->total_price,
                ]
            );
            
            //daraja_stk($phone_number, $amount, $order_number, $trx_reference, $trx_description)
            $stk_push = json_decode(daraja_stk(clean_phone_number($this->mpesa_number), "1", $this->order_number, $this->identifier, "Test"));
            
            if ($stk_push) {
                $payment->merchant_request_id = $stk_push->MerchantRequestID;
                $payment->checkout_request_id = $stk_push->CheckoutRequestID;
                $payment->request_response = $stk_push->ResponseDescription;
                $payment->customer_request_response = $stk_push->CustomerMessage;

                $payment->save();
            }

            $this->response_code = $stk_push->ResponseCode;
            if ($stk_push->ResponseCode == 0) $this->response_message = "Payment request sent to your phone.";
            else $this->response_message = $stk_push->CustomerMessage;
        } else {
            //Change confirmed status to true
            $this->confirmed = true;
        }
    }

    public function paymentCallbackReceived($identifier, $result_code, $result_desc)
    {
        if ($this->identifier == $identifier) {
            if ($result_code == "0") {
                $this->response_message = "Payment has been made successfully.";
                $this->order->status_id = 2;
                $this->order->save();
            } else $this->response_message = $result_desc;
        }
    }

    public function render()
    {
        return view('livewire.podcast-payment-component');
    }
}

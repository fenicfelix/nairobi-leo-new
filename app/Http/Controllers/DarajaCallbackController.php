<?php

namespace App\Http\Controllers;

use App\Models\ecommerce\Order;
use App\Models\ecommerce\Payment;
use Illuminate\Http\Request;

class DarajaCallbackController extends Controller
{
    public function skt_callback(Request $request, $identifier)
    {
        $json = $request->json()->all();
        info("STK-PUSH: " . json_encode($request->all()));

        $result = $json["Body"]["stkCallback"];
        $payment = Payment::where("identifier", $identifier)->first();
        if ($payment) {
            $payment->trx_result_response = $result["ResultCode"];
            $payment->trx_result_message = $result["ResultDesc"];

            if ($payment->trx_result_response == "0") {
                foreach ($result["CallbackMetadata"]["Item"] as $item) {
                    if ($item["Name"] == "MpesaReceiptNumber") $payment->trx_receipt = $item["Value"];
                    if ($item["Name"] == "Amount") $payment->amount_paid = $item["Value"];
                }
            }

            $payment->save();
        }

        // $this->emit('paymentCallbackReceived', $identifier, $payment->trx_result_response, $payment->trx_result_message);
    }
}

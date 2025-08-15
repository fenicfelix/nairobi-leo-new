<?php

function daraja_generate_key()
{
    $url    =     config('ecommerce.daraja.token_link');
    $curl   =     curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode(config('ecommerce.consumer_key') . ':' . config('ecommerce.consumer_secret'))));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $curl_response = json_decode(curl_exec($curl));

    curl_close($curl);

    return $curl_response->access_token;
}

function daraja_get_identifier($type)
{
    $type = strtolower($type);
    switch ($type) {
        case "msisdn":
            $x = 1;
            break;
        case "tillnumber":
            $x = 2;
            break;
        case "shortcode":
            $x = 4;
            break;
    }
    return $x;
}

function daraja_stk($phone_number, $amount, $order_number, $trx_reference, $trx_description)
{
    info("STK REQUEST: " . $phone_number . " | " . $amount . " | " . $order_number . " | " . $trx_reference . " | " . $trx_description);
    $shortcode = "174379";
    $url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
    $timestamp = date('Ymdhis');
    $data = [
        "BusinessShortCode" => $shortcode,
        "Password" => base64_encode($shortcode . config('ecommerce.passkey') . $timestamp),
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone_number,
        "PartyB" => $shortcode,
        "PhoneNumber" => $phone_number,
        "CallBackURL" => route('daraja-stk-callback', $trx_reference),
        "AccountReference" => $order_number,
        "TransactionDesc" => $trx_description
    ];

    info(json_encode($data));

    return daraja_curl($url, $data);
}

function transaction_status($transaction)
{
    $url     =    config('ecommerce.daraja.trx_status_link');
    $data = [
        'Initiator'                 =>  $transaction->account->daraja_initiator,
        'SecurityCredential'        =>  daraja_cert($transaction->account),
        'CommandID'                 =>  'TransactionStatusQuery',
        'TransactionID'             =>  $transaction->receipt_number, //Organization Receiving the funds.
        'PartyA'                    =>   $transaction->account->merchant_code,
        'IdentifierType'            =>  daraja_get_identifier("shortcode"),
        'ResultURL'                 =>  route('trx_status_result_url', $transaction->identifier),
        'QueueTimeOutURL'           =>  route('trx_status_timeout_url'),
        'Remarks'                   =>  $transaction->description,
        'Occasion'                  =>  NULL,
        'OriginalConversationID'    =>  $transaction->original_conversation_id
    ];
    return daraja_curl($url, $data, $transaction->account);
}

function reverse_transaction($transaction)
{
    $url     =    config('ecommerce.daraja.trx_status_link');
    $data = [
        'Initiator'                 =>  $transaction->service->account->daraja_initiator,
        'SecurityCredential'        =>  daraja_cert($transaction->service->account),
        'CommandID'                 =>  'TransactionReversal',
        "TransactionID"             =>  $transaction->receipt_number,
        "Amount"                    =>  $transaction->disbursed_amount,
        "ReceiverParty"             =>  $transaction->service->account->merchant_code,
        "RecieverIdentifierType"    =>  daraja_get_identifier("shortcode"),
        "ResultURL"                 =>  route('trx_reversal_result_url'),
        "QueueTimeOutURL"           =>  route('trx_reversal_timeout_url'),
        "Remarks"                   =>  "please",
        "Occasion"                  =>  "work"
    ];
    return daraja_curl($url, $data, $transaction->service->account);
}

function daraja_cert($account)
{
    $certificatePath = public_path('Cert/daraja/cert.cer');
    $fp = fopen($certificatePath, "r");
    $publicKey = fread($fp, filesize($certificatePath));
    fclose($fp);
    openssl_get_publickey($publicKey);
    openssl_public_encrypt($account->daraja_credential, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
    return  base64_encode($encrypted);
}

function daraja_curl($url, $data)
{
    $curl     =     curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . daraja_generate_key()));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $curl_response = curl_exec($curl);

    curl_close($curl);

    return $curl_response;
}

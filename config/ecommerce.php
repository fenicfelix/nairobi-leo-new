<?php

return [
    'consumer_key' => 'HlAC1h6aBJRFAFtOKYYqCin7D6fldmr2',
    'consumer_secret' => 'M7NJ4Xbp46KKfAKk',
    'passkey' => 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919',
    'shortcode' => '174379',
    'initiator_name' => 'testapi',
    "daraja" => [
        "token_link" => env('DARAJA_TOKEN_LINK', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'),
        "balance_link" => env('DARAJA_BALANCE_LINK', 'https://sandbox.safaricom.co.ke/mpesa/accountbalance/v1/query'),
        "b2c_link" => env('DARAJA_B2C_LINK', 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest'),
        "b2b_link" => env('DARAJA_B2B_LINK', 'https://sandbox.safaricom.co.ke/mpesa/b2b/v1/paymentrequest'),
        "trx_status_link" => env('DARAJA_TRX_STATUS_LINK', 'https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query'),
    ]
];

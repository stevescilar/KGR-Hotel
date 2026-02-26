<?php

// config/mpesa.php

return [
    'consumer_key'    => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'shortcode'       => env('MPESA_SHORTCODE', '174379'),
    'passkey'         => env('MPESA_PASSKEY'),
    'callback_url'    => env('MPESA_CALLBACK_URL'),
    'sandbox'         => env('MPESA_SANDBOX', true),
    'timeout_url'     => env('MPESA_TIMEOUT_URL'),
];


// config/africastalking.php

return [
    'username' => env('AT_USERNAME', 'sandbox'),
    'api_key'  => env('AT_API_KEY'),
    'from'     => 'KGR',
];

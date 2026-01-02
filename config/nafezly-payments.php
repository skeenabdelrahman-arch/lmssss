<?php

return [
    #KASHIER
    'KASHIER_ACCOUNT_KEY' => env('KASHIER_ACCOUNT_KEY', env('KASHIER_API_KEY')),
    'KASHIER_MERCHANT_ID' => env('KASHIER_MERCHANT_ID'), // Should be in format MID-XXX-XXX
    'KASHIER_IFRAME_KEY' => env('KASHIER_IFRAME_KEY'),
    'KASHIER_TOKEN' => env('KASHIER_TOKEN', env('KASHIER_API_KEY')),
    'KASHIER_URL' => env('KASHIER_URL', 'https://checkout.kashier.io'),
    'KASHIER_MODE' => env('KASHIER_MODE', 'test'), //live or test
    'KASHIER_CURRENCY' => env('KASHIER_CURRENCY', 'EGP'),
    'KASHIER_WEBHOOK_URL' => env('KASHIER_WEBHOOK_URL'),

    'VERIFY_ROUTE_NAME' => 'verify-payment',
    'APP_NAME' => env('APP_NAME', 'Laravel'),
];


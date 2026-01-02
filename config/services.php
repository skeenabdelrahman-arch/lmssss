<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'kashier' => [
        'api_key' => env('KASHIER_API_KEY'), // API Key from Kashier
        'secret_key' => env('KASHIER_SECRET_KEY'), // Secret Key from Kashier (used for hash generation)
        'merchant_id' => env('KASHIER_MERCHANT_ID'), // Merchant ID (format: MID-XXX-XXX)
        'mode' => env('KASHIER_MODE', 'test'), // test or live
    ],

];

<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Default Payment Gateway
     |--------------------------------------------------------------------------
     |
     | This option controls the default payment gateway that will be used.
     | By default we will use Stripe, but you may change this to any provider.
     |
     */

    'default' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),

    /*
     |--------------------------------------------------------------------------
     | Payment Gateways Properties
     |--------------------------------------------------------------------------
     |
     | Here you may configure the credentials and settings for each supported
     | payment gateway. It is recommended to store credentials in the .env file.
     |
     */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'usd'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
    ],

    'bank_transfer' => [
        'account_name' => env('BANK_ACCOUNT_NAME', 'TrafficVai LLC'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '123456789'),
        'routing_number' => env('BANK_ROUTING_NUMBER', '987654321'),
        'bank_name' => env('BANK_NAME', 'Global Bank'),
    ]
];

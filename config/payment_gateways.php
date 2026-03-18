<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Modular Payment Gateways Configuration
     |--------------------------------------------------------------------------
     |
     | This file defines all available payment gateways for the TrafficVai system.
     | The Admin Settings UI dynamically reads this array to generate config boxes.
     | To add a new gateway, simply define it here with its required fields.
     |
     */

    'global' => [
        'stripe' => [
            'name' => 'Stripe',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg',
            'description' => 'Accept major credit and debit cards globally.',
            'fields' => [
                'public_key' => ['type' => 'text', 'label' => 'Publishable Key'],
                'secret_key' => ['type' => 'password', 'label' => 'Secret Key'],
                'webhook_secret' => ['type' => 'password', 'label' => 'Webhook Signing Secret', 'hint' => 'Endpoint: /webhook/stripe'],
                'currency' => ['type' => 'text', 'label' => 'Default Currency (e.g., usd)'],
            ],
            'has_test_mode' => true,
            'type' => 'automatic',
        ],
        'paypal' => [
            'name' => 'PayPal',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg',
            'description' => 'Fast and secure payments via PayPal accounts.',
            'fields' => [
                'client_id' => ['type' => 'text', 'label' => 'Client ID'],
                'client_secret' => ['type' => 'password', 'label' => 'Client Secret'],
                'currency' => ['type' => 'text', 'label' => 'Default Currency (e.g., usd)'],
            ],
            'has_test_mode' => true,
            'type' => 'automatic',
        ],
        'bank_transfer' => [
            'name' => 'Manual Bank Transfer',
            'logo' => 'https://cdn-icons-png.flaticon.com/512/2830/2830284.png',
            'description' => 'Allow clients to transfer funds directly to your bank account.',
            'fields' => [
                'details' => ['type' => 'textarea', 'label' => 'Bank Account Details', 'hint' => 'Bank Name, Account Number, Routing/Swift Code.'],
                'instructions' => ['type' => 'textarea', 'label' => 'Client Instructions', 'hint' => 'Tell clients what reference to use.'],
            ],
            'has_test_mode' => false,
            'type' => 'manual',
        ],
        'wallet' => [
            'name' => 'Account Balance',
            'logo' => 'https://cdn-icons-png.flaticon.com/512/2933/2933116.png',
            'description' => 'Pay using your internal wallet balance.',
            'fields' => [], // No fields needed
            'has_test_mode' => false,
            'type' => 'automatic',
        ],
    ],

    'crypto' => [
        'cryptomus' => [
            'name' => 'Cryptomus',
            'logo' => 'https://cryptomus.com/assets/img/logo.svg', // Update with actual URL later
            'fields' => [
                'merchant_id' => ['type' => 'text', 'label' => 'Merchant ID'],
                'payment_key' => ['type' => 'password', 'label' => 'Payment Key'],
            ],
            'type' => 'automatic',
        ],
        'plisio' => [
            'name' => 'Plisio',
            'logo' => 'https://plisio.net/favicon-96x96.png',
            'description' => 'Accept cryptocurrency payments via Plisio.',
            'fields' => [
                'api_key' => [
                    'type' => 'password',
                    'label' => 'Secret API Key',
                    'hint' => 'Callback endpoint to set in Plisio dashboard: {APP_URL}/plisio/callback',
                ],
            ],
            'type' => 'automatic',
        ],
        'coinbase' => [
            'name' => 'Coinbase Commerce',
            'logo' => 'https://images.ctfassets.net/q5ulk4bp65r7/3TBS4oVkD1ghowTqVQJlqj/2dfd4ea3b623a7c0d8dea22f2814b74e/Consumer_Wordmark.svg',
            'fields' => [
                'api_key' => ['type' => 'password', 'label' => 'API Key'],
                'webhook_secret' => ['type' => 'password', 'label' => 'Webhook Shared Secret'],
            ],
            'type' => 'automatic',
        ],
    ],

    'bangladesh' => [
        'sslcommerz' => [
            'name' => 'SSLCOMMERZ',
            'logo' => 'https://securepay.sslcommerz.com/public/image/SSLCommerz-Pay-With-logo-All-Size-05.png',
            'description' => 'Popular payment gateway in Bangladesh supporting local cards and Mobile Banking.',
            'fields' => [
                'store_id' => ['type' => 'text', 'label' => 'Store ID'],
                'store_password' => ['type' => 'password', 'label' => 'Store Password'],
            ],
            'has_test_mode' => true,
            'type' => 'automatic',
        ],
        'shurjopay' => [
            'name' => 'ShurjoPay',
            'logo' => 'https://shurjopay.com.bd/assets/main/img/logo.png',
            'description' => 'Secure payment gateway for BDT transactions.',
            'fields' => [
                'merchant_username' => ['type' => 'text', 'label' => 'Merchant Username'],
                'merchant_password' => ['type' => 'password', 'label' => 'Merchant Password'],
                'prefix' => ['type' => 'text', 'label' => 'Order Prefix'],
                'return_url' => ['type' => 'text', 'label' => 'Return URL (Optional)'],
            ],
            'has_test_mode' => true,
            'type' => 'automatic',
        ],
        'aamarpay' => [
            'name' => 'AamarPay',
            'logo' => 'https://book.aamarpay.com/images/logo/aamarpay_logo.png',
            'description' => 'Fast and reliable gateway for Bangladeshi businesses.',
            'fields' => [
                'store_id' => ['type' => 'text', 'label' => 'Store ID'],
                'signature_key' => ['type' => 'password', 'label' => 'Signature Key'],
            ],
            'has_test_mode' => true,
            'type' => 'automatic',
        ],
        'bkash' => [
            'name' => 'bKash (Manual)',
            'logo' => '/images/gateways/bkash.png',
            'description' => 'Accept manual payments via bKash personal or agent numbers.',
            'fields' => [
                'account_number' => ['type' => 'text', 'label' => 'bKash Number'],
                'account_type' => ['type' => 'text', 'label' => 'Account Type (Personal/Agent)', 'hint' => 'e.g., Personal'],
                'ussd_instruction' => ['type' => 'text', 'label' => 'Step 1 (USSD/App)', 'hint' => 'e.g., *247# ডায়াল করে...'],
                'action_instruction' => ['type' => 'text', 'label' => 'Step 2 (Action)', 'hint' => 'e.g., "Send Money" -এ ক্লিক করুন।'],
                'instructions' => ['type' => 'textarea', 'label' => 'Extra Payment Instructions', 'hint' => 'Any additional instructions.'],
            ],
            'has_test_mode' => false,
            'type' => 'manual',
        ],
        'nagad' => [
            'name' => 'Nagad (Manual)',
            'logo' => '/images/gateways/nagad.png',
            'description' => 'Accept manual payments via Nagad.',
            'fields' => [
                'account_number' => ['type' => 'text', 'label' => 'Nagad Number'],
                'account_type' => ['type' => 'text', 'label' => 'Account Type (Personal/Agent)', 'hint' => 'e.g., Personal'],
                'ussd_instruction' => ['type' => 'text', 'label' => 'Step 1 (USSD/App)', 'hint' => 'e.g., *167# ডায়াল করে...'],
                'action_instruction' => ['type' => 'text', 'label' => 'Step 2 (Action)', 'hint' => 'e.g., "Send Money" -এ ক্লিক করুন।'],
                'instructions' => ['type' => 'textarea', 'label' => 'Extra Payment Instructions'],
            ],
            'has_test_mode' => false,
            'type' => 'manual',
        ],
        'rocket' => [
            'name' => 'Rocket (Manual)',
            'logo' => 'https://seeklogo.com/images/D/dutch-bangla-rocket-logo-B4D104E752-seeklogo.com.png',
            'description' => 'Accept manual payments via DBBL Rocket.',
            'fields' => [
                'account_number' => ['type' => 'text', 'label' => 'Rocket Number'],
                'account_type' => ['type' => 'text', 'label' => 'Account Type (Personal/Agent)'],
                'ussd_instruction' => ['type' => 'text', 'label' => 'Step 1 (USSD/App)', 'hint' => 'e.g., *322# ডায়াল করে...'],
                'action_instruction' => ['type' => 'text', 'label' => 'Step 2 (Action)', 'hint' => 'e.g., "Send Money" -এ ক্লিক করুন।'],
                'instructions' => ['type' => 'textarea', 'label' => 'Extra Payment Instructions'],
            ],
            'has_test_mode' => false,
            'type' => 'manual',
        ],
    ],

];

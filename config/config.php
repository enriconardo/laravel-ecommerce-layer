<?php

return [
    'http' => [

        'routes' => [

            'enabled' => env('ECOMMERCE_LAYER_ROUTES_ENABLED', true),

            'prefix' => env('ECOMMERCE_LAYER_ROUTES_PREFIX', 'ecommerce-layer'),

            /*
            |--------------------------------------------------------------------------
            | Routes middlewares
            |--------------------------------------------------------------------------
            |
            | Set here the list of the middlewares you want to attach to the Laravel
            | Ecommerce Layer roues.
            |
            */
            'middlewares' => [
                'api'
                // ...
            ],

        ]
    ],

    'payment_methods' => [

        /*
        |--------------------------------------------------------------------------
        | Payment methods: protected types
        |--------------------------------------------------------------------------
        |
        | A list of payment method type that should not be saved in the database.
        |
        */
        'protected_types' => [

            'card',
            // ...

        ]

    ],

    'gateways' => [

        'stripe' => [

            'secret_key' => env('STRIPE_SECRET_KEY'),

            'endpoint_secret' => env('STRIPE_ENDPOINT_SECRET')

        ],

        'sardex' => [

            'api_url' => env('SARDEX_API_URL', 'https://cyclos-sandbox.sardlab.io'),

            'api_username' => env('SARDEX_API_USERNAME'),

            'api_password' => env('SARDEX_API_PASSWORD'),

            'payment_url' => env('SARDEX_PAYMENT_URL')
        
        ]

    ],

    'webhooks' => [

        /* Example
        'service-name' => [
            'url' => 'https://yourservice.com/webhook',
            'headers' => [
                'key' => 'value',
                // ...
            ]
        ]
        */
        
    ]
];

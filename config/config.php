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

    'gateways' => [

        'stripe' => [

            'secret_key' => env('STRIPE_SECRET_KEY')

        ]

    ]
];

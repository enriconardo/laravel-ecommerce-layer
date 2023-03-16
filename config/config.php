<?php

return [
    'http' => [

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

        'routes' => [

            'prefix' => env('ECOMMERCE_LAYER_ROUTES_PREFIX', 'ecommerce-layer')

        ]
    ],

    'gateways' => [

        'stripe' => [

            'secret_key' => env('STRIPE_SECRET_KEY')

        ]

    ]
];
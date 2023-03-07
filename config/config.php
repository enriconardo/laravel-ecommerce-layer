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
        ]
    ],

    'gateways' => [

        'stripe' => [

            'secret_key' => env('STRIPE_SECRET_KEY')

        ]

    ]
];
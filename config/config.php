<?php

return [
    'auth' => [

        /*
        |--------------------------------------------------------------------------
        | Authentication Guard
        |--------------------------------------------------------------------------
        |
        | The auth configuration is managed in the base auth.php config file
        | in your Laravel application.
        |
        */
        'guard' => env('ECOMMERCE_LAYER_AUTH_GUARD', 'web')

    ],

    'gateways' => [

        'stripe' => [

            'secret_key' => env('STRIPE_SECRET_KEY')

        ]

    ]
];
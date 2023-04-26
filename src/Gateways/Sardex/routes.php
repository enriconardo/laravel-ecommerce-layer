<?php

use Illuminate\Support\Facades\Route;

Route::post('sardex/webhook', '\EcommerceLayer\Gateways\Sardex\WebhookController@handle')
    ->name('sardex.webhook');

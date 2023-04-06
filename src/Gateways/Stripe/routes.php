<?php

use Illuminate\Support\Facades\Route;

Route::post('stripe/webhook', '\EcommerceLayer\Gateways\Stripe\WebhookController@handle');

<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.routes.middlewares');
$prefix = config('ecommerce-layer.http.routes.prefix');

Route::prefix($prefix)
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('gateways', 'GatewayController@list')->name('ecommerce-layer.gateways.list');

        Route::get('gateways/{id}', 'GatewayController@find')->name('ecommerce-layer.gateways.find');

    });

<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.middlewares');

Route::prefix('ecommerce-layer/prices')
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'PriceController@list')->name('ecommerce-layer.prices.list');

        Route::get('{id}', 'PriceController@find')->name('ecommerce-layer.prices.find');

        Route::post('/', 'PriceController@create')->name('ecommerce-layer.prices.create');

        Route::patch('{id}', 'PriceController@update')->name('ecommerce-layer.prices.update');

        Route::delete('{id}', 'PriceController@delete')->name('ecommerce-layer.prices.delete');

    });

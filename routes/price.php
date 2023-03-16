<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.middlewares');
$prefix = config('ecommerce-layer.http.routes.prefix');

Route::prefix($prefix)
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('prices', 'PriceController@list')->name('ecommerce-layer.prices.list');

        Route::get('prices/{id}', 'PriceController@find')->name('ecommerce-layer.prices.find');

        Route::post('prices', 'PriceController@create')->name('ecommerce-layer.prices.create');

        Route::patch('prices/{id}', 'PriceController@update')->name('ecommerce-layer.prices.update');

        Route::delete('prices/{id}', 'PriceController@delete')->name('ecommerce-layer.prices.delete');

    });

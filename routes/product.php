<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.middlewares');

Route::prefix('ecommerce-layer/products')
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'ProductController@list')->name('ecommerce-layer.products.list');

        Route::get('{id}', 'ProductController@find')->name('ecommerce-layer.products.find');

        Route::post('/', 'ProductController@create')->name('ecommerce-layer.products.create');

        Route::patch('{id}', 'ProductController@update')->name('ecommerce-layer.products.update');

        Route::delete('{id}', 'ProductController@delete')->name('ecommerce-layer.products.delete');

    });

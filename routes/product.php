<?php

use Illuminate\Support\Facades\Route;

$guard = config('ecommerce-layer.auth.guard');

Route::prefix('ecommerce-layer/products')
    ->middleware(["api", "auth:$guard"])
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'ProductController@list')->name('ecommerce-layer.products.list');

        Route::get('{id}', 'ProductController@find')->name('ecommerce-layer.products.find');

        Route::post('/', 'ProductController@create')->name('ecommerce-layer.products.create');

        Route::patch('{id}', 'ProductController@update')->name('ecommerce-layer.products.update');

        Route::delete('{id}', 'ProductController@delete')->name('ecommerce-layer.products.delete');

    });

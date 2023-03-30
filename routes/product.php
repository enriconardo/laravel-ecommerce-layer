<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.routes.middlewares');
$prefix = config('ecommerce-layer.http.routes.prefix');

Route::prefix($prefix)
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('products', 'ProductController@list')->name('ecommerce-layer.products.list');

        Route::get('products/{id}', 'ProductController@find')->name('ecommerce-layer.products.find');

        Route::post('products', 'ProductController@create')->name('ecommerce-layer.products.create');

        Route::patch('products/{id}', 'ProductController@update')->name('ecommerce-layer.products.update');

        Route::delete('products/{id}', 'ProductController@delete')->name('ecommerce-layer.products.delete');

    });

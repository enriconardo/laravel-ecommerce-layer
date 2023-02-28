<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('line-items', 'LineItemController@list')->name('ecommerce-layer.line-items.list');

        Route::get('line-items/{id}', 'LineItemController@find')->name('ecommerce-layer.line-items.find');

        Route::post('line-items', 'LineItemController@create')->name('ecommerce-layer.line-items.create');

        Route::patch('line-items/{id}', 'LineItemController@update')->name('ecommerce-layer.line-items.update');

        Route::delete('line-items/{id}', 'LineItemController@delete')->name('ecommerce-layer.line-items.delete');

    });

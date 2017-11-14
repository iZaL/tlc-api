<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function () {

    Route::middleware(['auth:api'])->group(function () {

        Route::get('loads/create','LoadsController@createLoad')->name('loads.create');
        Route::post('loads', 'LoadsController@storeLoad')->name('loads.store');
        Route::get('loads', 'LoadsController@getLoads')->name('loads.index');

    });

});


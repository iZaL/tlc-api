<?php

/**
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 */
Route::group(['namespace' => 'Api','middleware' => 'locale'], function () {


    /**
     * |--------------------------------------------------------------------------
     * | Authenticated Routes
     * |--------------------------------------------------------------------------
     */

    Route::middleware(['auth:api'])->group(function () {

        /**
        |--------------------------------------------------------------------------
        | Load Routes
        |--------------------------------------------------------------------------
        */

        Route::get('loads/create','LoadsController@createLoad')->name('loads.create');
        Route::post('loads', 'LoadsController@storeLoad')->name('loads.store');
        Route::get('loads', 'LoadsController@getLoads')->name('loads.index');


        /**
         * |--------------------------------------------------------------------------
         * | Country Routes
         * |--------------------------------------------------------------------------
         */

        Route::get('countries','CountriesController@getAll');


        /**
         * |--------------------------------------------------------------------------
         * | Driver Routes
         * |--------------------------------------------------------------------------
         */

        Route::group(['prefix' => 'driver','namespace' => 'Driver','middleware' => 'driver'], function () {

            Route::post('profile/update','ProfileController@update');

        });

    });

    /**
     * |--------------------------------------------------------------------------
     * | Auth Routes
     * |--------------------------------------------------------------------------
     */

    Route::group(['prefix' => 'auth','namespace' => 'Auth'], function () {
        Route::get('logout', 'LoginController@logout');
        Route::post('login', 'LoginController@login');
        Route::post('register', 'LoginController@register');
        Route::post('password/forgot', 'LoginController@forgotPassword'); // send email
        Route::post('password/recover', 'LoginController@recoverPassword'); // send email
        Route::post('password/update', 'LoginController@updatePassword'); // send email
        Route::post('otp/confirm', 'LoginController@confirmOTP'); // send email
    });

});


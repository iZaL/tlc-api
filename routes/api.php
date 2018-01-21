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
         * |--------------------------------------------------------------------------
         * | Truck Routes
         * |--------------------------------------------------------------------------
         */

        Route::get('trucks/makes','TrucksController@getMakesModels');
        Route::get('trailers','TrucksController@getTrailers');
        Route::get('trailers/makes','TrucksController@getTrailerMakes');


        Route::group(['prefix' => 'shipper','namespace' => 'Shipper','middleware' => 'shipper'], function () {

            Route::get('profile','ProfileController@getProfile');
            Route::post('profile/update','ProfileController@update');

            /**
             * |--------------------------------------------------------------------------
             * | Load Routes
             * |--------------------------------------------------------------------------
             */

            Route::get('loads/create', 'LoadsController@createLoad')->name('loads.create');
            Route::post('loads', 'LoadsController@storeLoad')->name('loads.store');
            Route::get('loads', 'LoadsController@getLoads')->name('loads.index');


            /**
             * |--------------------------------------------------------------------------
             * | Employees
             * |--------------------------------------------------------------------------
             */
            Route::get('employees','EmployeesController@index');
            Route::post('employees','EmployeesController@store');


            /**
             * |--------------------------------------------------------------------------
             * | Locations
             * |--------------------------------------------------------------------------
             */
            Route::get('locations','LocationsController@index');
            Route::post('locations','LocationsController@store');

        });

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

            Route::get('profile','ProfileController@getProfile');
            Route::post('profile/update','ProfileController@update');


            Route::post('trucks','TrucksController@saveTruck');

            Route::get('routes','RoutesController@getRoutes');
            Route::post('routes','RoutesController@saveRoute');

            Route::get('routes/{id}/transits','RoutesController@getRouteTransits');

//            Route::get('jobs','LoadsController@getLoadRequests');
            Route::get('trips/upcoming','TripsController@getUpcomingTrips');
            Route::post('trips/{id}/confirm','TripsController@confirmTrip');

            Route::get('loads/{id}/details','LoadsController@getLoadDetails');

            Route::get('loads', 'LoadsController@getLoads')->name('loads.index');


//            Route::get('jobs','JobsController@getUpcomingJobs');
//            Route::get('loads/{id}/details','LoadsController@getLoadDetails');

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


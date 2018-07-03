<?php

/**
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 */
Route::group(['namespace' => 'Api','middleware' => 'locale'], function () {


    Route::post('trips/{id}/location/update','\App\Http\Controllers\Api\Driver\TripsController@updateLocation');

    Route::get('security_passes','\App\Http\Controllers\Api\RoutesController@getSecurityPasses');
    Route::post('upload/images','\App\Http\Controllers\Api\UploadsController@uploadImages');

    /**
     * |--------------------------------------------------------------------------
     * | Country Routes
     * |--------------------------------------------------------------------------
     */

    Route::get('countries','CountriesController@getAll');

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
        Route::get('trailers/types','TrucksController@getTrailerTypes');


        Route::group(['prefix' => 'customer','namespace' => 'Customer','middleware' => 'customer'], function () {

            Route::get('profile','ProfileController@getProfile');
            Route::post('profile/update','ProfileController@update');

            /**
             * |--------------------------------------------------------------------------
             * | Load Routes
             * |--------------------------------------------------------------------------
             */

            Route::get('loads/create', 'LoadsController@createLoad');
            Route::post('loads', 'LoadsController@storeLoad');
            Route::get('loads', 'LoadsController@getLoads');
            Route::get('loads/status/{status}', 'LoadsController@getLoadsByStatus');
            Route::get('loads/add/data', 'LoadsController@getLoadAddData');
            Route::get('loads/{id}/details','LoadsController@getLoadDetails');
            Route::get('loads/current', 'LoadsController@getCurrentLoad');

//            Route::get('loads/{id}/drivers','LoadsController@getLoadDrivers');//matching drivers

            Route::get('loads/{id}/drivers/search','LoadDriversController@searchDriversForLoad');
            Route::get('loads/{id}/drivers/bookable','LoadDriversController@getBookableDriversForLoad');

            /**
             * |--------------------------------------------------------------------------
             * | TRIPS
             * |--------------------------------------------------------------------------
             */
            Route::get('trips/{id}/details','TripsController@getTripDetails');

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
            Route::post('addresses','LocationsController@store');

            Route::get('drivers', 'DriversController@getDrivers');
            Route::get('drivers/blocked', 'DriversController@getBlockedDrivers');

        });


        /**
         * |--------------------------------------------------------------------------
         * | Driver Routes
         * |--------------------------------------------------------------------------
         */

        Route::group(['prefix' => 'driver','namespace' => 'Driver','middleware' => 'driver'], function () {

            Route::get('security_passes', 'ProfileController@getSecurityPasses');
            Route::get('profile','ProfileController@getProfile');
            Route::post('profile/update','ProfileController@update');

            Route::post('trucks','TrucksController@saveTruck');

            Route::get('routes','RoutesController@getRoutes');
            Route::get('routes/{id}/transits','RoutesController@getRouteTransits');
            Route::post('routes','RoutesController@saveRoute');

//            Route::get('trips/upcoming','TripsController@getUpcomingTrips');
//            Route::post('trips/{id}/confirm','TripsController@confirmTrip');
            Route::get('trips/{id}/details','TripsController@getTripDetails');
            Route::post('trips/{id}/status/update', 'TripsController@updateStatus');

            Route::get('loads', 'LoadsController@getLoads')->name('loads.index');
            Route::get('loads/status/{status}', 'LoadsController@getLoadsByStatus');
            Route::get('loads/current', 'LoadsController@getCurrentLoad');
            Route::get('loads/requests', 'LoadsController@getLoadRequests');
            Route::get('loads/{id}/details','LoadsController@getLoadDetails');

            Route::get('documents/types', 'DocumentsController@getTypes');

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


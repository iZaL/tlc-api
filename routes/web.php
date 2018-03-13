<?php


Route::get('/', function () {
});

Route::get('loads/{id}/drivers','Api\Customer\LoadDriversController@getDriversForLoad');

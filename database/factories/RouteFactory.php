<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Route::class, function (Faker $faker) {
    return [
        'origin_country_id'      => \App\Models\Country::all()->count() ? \App\Models\Country::first()->id : 1,
        'destination_country_id' => \App\Models\Country::all()->count() ? \App\Models\Country::first()->id : 1,
        'active'                 => 1
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\ShipperLocation::class, function (Faker $faker) {
    return [
        'shipper_id'  => \App\Models\Shipper::all()->count() ? \App\Models\Shipper::all()->random()->first()->id : 1,
        'country_id'  => \App\Models\Country::all()->count() ?  \App\Models\Country::all()->random()->first()->id : 1,
        "latitude" => mt_rand(29.10,29.90).'.'.mt_rand(10,100),
        "longitude" => mt_rand(47.10,47.90).'.'.mt_rand(10,100),
        'type' => 'origin',
    ];
});

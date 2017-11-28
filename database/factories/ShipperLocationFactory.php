<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\ShipperLocation::class, function (Faker $faker) {
    return [
        'shipper_id'  => \App\Models\Shipper::all()->count() ? \App\Models\Shipper::all()->random()->first()->id : 1,
        'country_id'  => \App\Models\Country::all()->count() ?  \App\Models\Country::all()->random()->first()->id : 1,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'type' => 'origin',
    ];
});

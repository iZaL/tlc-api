<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Location::class, function (Faker $faker) {
    return [
        'shipper_id'  => \App\Models\Shipper::all()->random()->first()->id,
        'country_id'  => \App\Models\Country::all()->random()->first()->id,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'type' => 'origin',
    ];
});

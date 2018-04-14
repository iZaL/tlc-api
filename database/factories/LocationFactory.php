<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Location::class, function (Faker $faker) {
    return [
        'country_id' => \App\Models\Country::first() ? \App\Models\Country::all()->random()->first()->id : 1,
        'parent_id' => null,
        'name_en' => $faker->city,
        'name_ar' => $faker->city,
        'name_hi' => $faker->city,
        'abbr' => $faker->city,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'active' => 1
    ];
});

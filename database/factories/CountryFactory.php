<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Country::class, function (Faker $faker) {
    $country = $faker->country;
    return [
        'name_en' => $country,
        'name_ar' => $country,
        'name_hi' => $country,
        'abbr' => $faker->countryCode,
        'show_route_locations' => $faker->boolean(90)
    ];
});

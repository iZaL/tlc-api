<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Country::class, function (Faker $faker) {
    $country = $faker->country;
    return [
        'name_en' => $country,
        'name_ar' => $country,
        'name_hi' => $country,
        'country_code' => $faker->countryCode
    ];
});

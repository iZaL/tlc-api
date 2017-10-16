<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Pass::class, function (Faker $faker) {
    return [
        'country_id'    => \App\Models\Country::all()->random()->first()->id,
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
    ];
});


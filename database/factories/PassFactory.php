<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\SecurityPass::class, function (Faker $faker) {
    return [
        'country_id'    =>  \App\Models\Country::first() ? \App\Models\Country::all()->random()->first()->id : 1,
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
    ];
});


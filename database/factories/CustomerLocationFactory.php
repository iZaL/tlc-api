<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\CustomerLocation::class, function (Faker $faker) {
    return [
        'customer_id'  => \App\Models\Customer::all()->count() ? \App\Models\Customer::all()->random()->first()->id : 1,
        'country_id'  => \App\Models\Country::all()->count() ?  \App\Models\Country::all()->random()->first()->id : 1,
        "latitude" => mt_rand(29.10,29.90).'.'.mt_rand(10,100),
        "longitude" => mt_rand(47.10,47.90).'.'.mt_rand(10,100),
        'type' => array_rand(['origin'=>'origin','destination' => 'destination']),
        'city_en' => $faker->city,
        'city_ar' => $faker->city,
        'state_ar' => $faker->city,
        'state_en' => $faker->city,
        'address_en' => $faker->address,
        'address_ar' => $faker->address,
    ];
});

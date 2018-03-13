<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Customer::class, function (Faker $faker) {
    return [
        'user_id' => \App\Models\User::all()->count() > 0 ? \App\Models\User::all()->random()->first()->id : 1,
        'address_en' => $faker->address,
        'address_ar' => $faker->address,
        'address_hi' => $faker->address,
        'book_direct' => $faker->boolean(40),
//        'use_own_truck' => $faker->boolean(50),
        'available_credit' => rand(100,1000),
        'cancellation_fee' => rand(10,50),
        'active' => 1
    ];
});

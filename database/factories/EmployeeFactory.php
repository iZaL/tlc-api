<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Employee::class, function (Faker $faker) {
    return [
        'customer_id' => \App\Models\Customer::all()->random()->first()->id,
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
        'mobile' => $faker->phoneNumber,
        'phone' => $faker->phoneNumber,
        'email' => $faker->safeEmail,
        'driver_interaction' => $faker->boolean(40)
    ];
});

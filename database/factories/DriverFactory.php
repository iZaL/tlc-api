<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Driver::class, function (Faker $faker) {
    return [
        'user_id'              => \App\Models\User::all()->random()->first()->id,
        'nationality'          => \App\Models\Country::all()->random()->first()->id,
        'mobile'               => $faker->phoneNumber,
        'residence_country_id' => \App\Models\Country::all()->random()->first()->id,
        'licence_number'       => str_random(10),
        'license_expiry_date'  => \Carbon\Carbon::now()->addDays(rand(10, 40))->addYear(rand(1, 4))->toDateString(),
        'status'               => 'available',
    ];
});

$factory->define(\App\Models\DriverVisas::class, function (Faker $faker) {
    return [
        'driver_id'  => \App\Models\Driver::all()->random()->first()->id,
        'country_id' => \App\Models\Country::all()->random()->first()->id,
    ];
});

$factory->define(\App\Models\DriverPass::class, function (Faker $faker) {
    return [
        'driver_id' => \App\Models\Driver::all()->random()->first()->id,
        'pass_id'   => \App\Models\Pass::all()->random()->first()->id,
    ];
});

$factory->define(\App\Models\DriverLanguage::class, function (Faker $faker) {
    return [
        'driver_id' => \App\Models\Driver::all()->random()->first()->id,
        'language_id'  => \App\Models\Language::all()->random()->first()->id,
    ];
});

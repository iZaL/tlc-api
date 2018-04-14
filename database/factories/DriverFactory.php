<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Driver::class, function (Faker $faker) {
    return [
        'user_id'                => \App\Models\User::all()->count() ? \App\Models\User::first()->id : 1,
        'truck_id'               => \App\Models\Truck::all()->count() ? \App\Models\Truck::all()->random()->first()->id : 1,
        'customer_id'             => \App\Models\Customer::all()->count() ? \App\Models\Customer::all()->random()->first()->id : 1,
        'mobile'                 => '99' . rand(111111, 999999),
        'offline'                => 0,
        'active'                 => 1,
        'book_direct'            => 1,
        'blocked'                => 0,
    ];
});

$factory->define(\App\Models\DriverDocument::class, function (Faker $faker) {
    return [
        'driver_id'   => \App\Models\Driver::first()->id,
        'country_id'  => \App\Models\Country::first()->id,
        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString(),
        'number'      => str_random(15),
        'image'      =>  'https://source.unsplash.com/800x400/?files',
        'type' => 'visa'
    ];
});

$factory->define(\App\Models\DriverSecurityPass::class, function (Faker $faker) {
    return [
        'driver_id' => \App\Models\Driver::first()->id,
        'security_pass_id'   => \App\Models\SecurityPass::first()->id,
        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString(),
        'number'      => str_random(15),
        'image'      =>  'https://source.unsplash.com/800x400/?files'
    ];
});

$factory->define(\App\Models\DriverLanguage::class, function (Faker $faker) {
    return [
        'driver_id'   => \App\Models\Driver::first()->id,
        'language_id' => \App\Models\Language::first()->id,
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Driver::class, function (Faker $faker) {
    return [
        'user_id'                => \App\Models\User::all()->count() ? \App\Models\User::first()->id : 1,
        'nationality_country_id' => \App\Models\Country::all()->count() ? \App\Models\Country::first()->id : 1,
        'truck_id'               => \App\Models\Truck::all()->count() ? \App\Models\Truck::all()->random()->first()->id : 1,
        'customer_id'             => \App\Models\Customer::all()->count() ? \App\Models\Customer::all()->random()->first()->id : 1,
        'mobile'                 => '99' . rand(111111, 999999),
        'offline'                => 0,
        'active'                 => 1,
        'book_direct'            => 1,
        'blocked'                => 0,
    ];
});

$factory->define(\App\Models\DriverVisas::class, function (Faker $faker) {
    return [
        'driver_id'   => \App\Models\Driver::first()->id,
        'country_id'  => \App\Models\Country::first()->id,
        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString(),
        'number'      => str_random(15),
        'image'      =>  'https://source.unsplash.com/800x400/?files'
    ];
});

$factory->define(\App\Models\DriverPass::class, function (Faker $faker) {
    return [
        'driver_id' => \App\Models\Driver::first()->id,
        'pass_id'   => \App\Models\Pass::first()->id,
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
//
$factory->define(\App\Models\DriverLicense::class, function (Faker $faker) {
    return [
        'driver_id'   => \App\Models\Driver::first()->id,
        'country_id'  => \App\Models\Country::first()->id,
        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString(),
        'number'      => str_random(15),
        'image'      =>  'https://source.unsplash.com/800x400/?files'
    ];
});
$factory->define(\App\Models\DriverResidency::class, function (Faker $faker) {
    return [
        'driver_id'   => \App\Models\Driver::first()->id,
        'country_id'  => \App\Models\Country::first()->id,
        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString(),
        'number'      => str_random(15),
        'image'      =>  'https://source.unsplash.com/800x400/?files'
    ];
});
//
//$factory->define(\App\Models\DriverLicense::class, function (Faker $faker) {
//    return [
//        'driver_id'   => \App\Models\Driver::all()->count() ? \App\Models\Driver::first()->id : 1,
//        'country_id' =>  \App\Models\Country::all()->count()  ? \App\Models\Country::first()->id : 1,
//        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString(),
//        'number'  => str_random(10)
//
//    ];
//});
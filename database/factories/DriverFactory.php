<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Driver::class, function (Faker $faker) {
    return [
        'user_id'              => \App\Models\User::all()->count() ? \App\Models\User::first()->id : 1,
        'truck_id'             => \App\Models\Truck::all()->count() ? \App\Models\Truck::first()->id : 1,
        'nationality'          => \App\Models\Country::all()->count() ? \App\Models\Country::first()->id : 1,
        'shipper_id'           => \App\Models\Shipper::all()->count() ? \App\Models\Shipper::all()->random()->first()->id :1,
        'residence_country_id' => \App\Models\Country::all()->count() ? \App\Models\Country::first()->id : 1,
//        'licence_number'       => str_random(10),
//        'license_expiry_date'  => \Carbon\Carbon::now()->addDays(rand(10, 40))->addYear(rand(1, 4))->toDateString(),
        'mobile'               => $faker->phoneNumber,
        'status'               => 'available',
        'latitude'             => $faker->latitude,
        'longitude'            => $faker->longitude,
    ];
});

$factory->define(\App\Models\DriverVisas::class, function (Faker $faker) {
    return [
        'driver_id'  => \App\Models\Driver::first()->id,
        'country_id' => \App\Models\Country::first()->id,
        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString()
    ];
});

$factory->define(\App\Models\DriverPass::class, function (Faker $faker) {
    return [
        'driver_id' => \App\Models\Driver::first()->id,
        'pass_id'   => \App\Models\Pass::first()->id,
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
        'driver_id'   => \App\Models\Driver::all()->count() ? \App\Models\Driver::first()->id : 1,
        'country_id' =>  \App\Models\Country::all()->count()  ? \App\Models\Country::first()->id : 1,
        'expiry_date' => \Carbon\Carbon::now()->addYear(1)->toDateTimeString(),
        'number'  => str_random(10)

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
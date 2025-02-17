<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    static $password;
    return [
//        'name_en' => $faker->name,
//        'name_ar' => $faker->name,
//        'name_hi' => $faker->name,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'mobile' => rand(9000000,9999999),
        'admin' => 0,
        'image' => $faker->imageUrl(500,500),
        'password' => $password ?: $password = bcrypt('password'),
        'remember_token' => str_random(16),
        'api_token' => str_random(16),
        'otp' => rand(1000,9000),
        'active' => 1
    ];
});

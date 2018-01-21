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
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'mobile' => $faker->phoneNumber,
        'admin' => 0,
        'image' => $faker->imageUrl(500,500),
        'password' => $password ?: $password = bcrypt('password'),
        'remember_token' => str_random(10),
        'otp' => rand(1000,9000),
        'api_token' => str_random(12),
        'active' => 1
    ];
});

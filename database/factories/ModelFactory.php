<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt('Test!'),
        'remember_token' => str_random(10),
        'timezone' => $faker->timezone,
        'currency' => $faker->currencyCode,
        'status' => 'Active',
        'category' => 'General User'
    ];
});

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

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    $minLevel = 1;
    $maxLevel = 9;
    $minPoint = 0;
    $maxPoint = 10000;

    return [
        'name' => $faker->name,
        'nick' => $faker->name,
        'level' => $faker->numberBetween($minLevel, $maxLevel),
        'point' => $faker->numberBetween($minPoint, $maxPoint),
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'mailing' => $faker->numberBetween(0, 1),
        'open' => $faker->numberBetween(0, 1),
        'certify' => $faker->numberBetween(0, 1),
        'adult' => $faker->numberBetween(0, 1),
        'created_at' => $faker->dateTimeBetween($startDate = '-2 years', $endDate = '-1 years'),
        'updated_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now'),
    ];
});

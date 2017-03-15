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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
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
        'sms' => $faker->numberBetween(0, 1),
        'open' => $faker->numberBetween(0, 1),
        'certify' => $faker->numberBetween(0, 1),
        'adult' => $faker->numberBetween(0, 1),
        'created_at' => $faker->dateTimeBetween($startDate = '-2 years', $endDate = '-1 years'),
        'updated_at' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now'),
    ];
});

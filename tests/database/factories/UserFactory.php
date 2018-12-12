<?php

declare(strict_types=1);

use Altek\Accountant\Tests\Models\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| User Factories
|--------------------------------------------------------------------------
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'is_admin'   => $faker->randomElement([0, 1]),
        'first_name' => $faker->firstName,
        'last_name'  => $faker->lastName,
        'email'      => $faker->unique()->safeEmail,
    ];
});

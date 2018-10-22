<?php

use Altek\Accountant\Models\Ledger;
use Altek\Accountant\Tests\Models\Article;
use Altek\Accountant\Tests\Models\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Ledger Factories
|--------------------------------------------------------------------------
|
*/

$factory->define(Ledger::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'user_type'     => User::class,
        'event'         => 'updated',
        'recordable_id' => function () {
            return factory(Article::class)->create()->id;
        },
        'recordable_type' => Article::class,
        'properties'      => [],
        'modified'        => [],
        'url'             => $faker->url,
        'ip_address'      => $faker->ipv4,
        'user_agent'      => $faker->userAgent,
    ];
});

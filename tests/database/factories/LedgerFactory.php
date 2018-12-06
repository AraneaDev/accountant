<?php

use Altek\Accountant\Models\Ledger;
use Altek\Accountant\Notary;
use Altek\Accountant\Tests\Models\Article;
use Altek\Accountant\Tests\Models\User;
use Carbon\Carbon;
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
        'extra'           => [],
        'url'             => $faker->url,
        'ip_address'      => $faker->ipv4,
        'user_agent'      => $faker->userAgent,
        'signature'       => function (array $properties) {
            unset($properties['signature']);

            return Notary::sign($properties);
        },
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => Carbon::now()->toDateTimeString(),
    ];
});

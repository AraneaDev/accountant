<?php

use Altek\Accountant\Tests\Models\Article;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Article Factories
|--------------------------------------------------------------------------
|
*/

$factory->define(Article::class, function (Faker $faker) {
    return [
        'title'        => $faker->unique()->sentence,
        'content'      => $faker->unique()->paragraph(6),
        'published_at' => null,
        'reviewed'     => $faker->randomElement([0, 1]),
    ];
});

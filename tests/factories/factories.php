<?php

use Yoeunes\Rateable\Models\Rating;
use Yoeunes\Rateable\Tests\Stubs\Models\Lesson;
use Yoeunes\Rateable\Tests\Stubs\Models\User;

$factory(Lesson::class, [
    'title'   => $faker->sentence,
    'subject' => $faker->words(2),
]);

$factory(Rating::class, [
    'value'         => $faker->numberBetween(config('rating.min_rating'), config('rating.max_rating')),
    'user_id'       => 'factory:Yoeunes\Rateable\Tests\Stubs\Models\User',
    'rateable_id'   => 'factory:Yoeunes\Rateable\Tests\Stubs\Models\Lesson',
    'rateable_type' => Lesson::class,
]);

$factory(User::class, [
    'name'           => $faker->name,
    'email'          => $faker->unique()->safeEmail,
    'password'       => bcrypt('secret'),
    'remember_token' => str_random(10),
]);

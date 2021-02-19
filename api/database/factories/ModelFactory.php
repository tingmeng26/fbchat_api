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

$factory->define(App\User::class, function (Faker\Generator $faker) {
  return [
    'name' => $faker->name,
    'email' => $faker->email,
  ];
});

$factory->define(App\Model\Member::class, function (Faker\Generator $faker) {
  $string = str_shuffle('abcdefghijklmnpqrstuvwxyz123456789');
  return [
    'account' => substr($string, 0, 4),
    'access_token' => $faker->sha256,
    'is_public' => 1,
    'access_token_expire_time' => date('Y-m-d H:i:s', strtotime('+ 3 months'))
  ];
});
$factory->define(App\Model\Mission::class, function (Faker\Generator $faker) {
  return [
    'mission1' => 0
  ];
});

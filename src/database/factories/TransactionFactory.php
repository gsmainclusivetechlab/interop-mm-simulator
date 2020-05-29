<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Transaction;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'trace_id' => str_replace('-', '', Str::uuid()),
        'callback_url' => $faker->url,
        'amount' => $faker->numberBetween(1, 101),
        'currency' => $faker->currencyCode,
        'type' => $faker->randomElement(Transaction::TYPES),
        'debitParty' => \GuzzleHttp\json_encode(["key" => "msisdn", "value" => $faker->e164PhoneNumber]),
        'creditParty' => \GuzzleHttp\json_encode(["key" => "msisdn", "value" => $faker->e164PhoneNumber]),
        'transactionStatus' => $faker->randomElement(Transaction::STATUSES),
    ];
});

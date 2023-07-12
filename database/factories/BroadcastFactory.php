<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Account;
use App\Models\Broadcast;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(Broadcast::class, function (Faker $faker) {

    // Create an account
    $this->account = factory(Account::class)->create();

    return [
        'id' => (string) Str::uuid(),
        'user_id' => $this->account->user_id,
        'account_id' => $this->account->id,
        'message' => $faker->sentence(9),
        'pictures' => [$faker->imageUrl(400, 400, 'food'),$faker->imageUrl(400, 400, 'food')],
        'videos' => [$faker->url(),$faker->url()],
        'preview_phone'=>  $faker->numerify('#############'),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(Broadcast::class, function (Broadcast $broadcast, Faker $faker) {

    $broadcast->user = $this->account->user;
    $broadcast->account = $this->account;
    return $broadcast;
});
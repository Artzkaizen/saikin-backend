<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Group;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(Group::class, function (Faker $faker) {

    // Create a user without triggering any associated creation events
    $this->user = User::withoutEvents(function () {
        return factory(User::class)->create();
    });

    return [
        'user_id' => $this->user->id,
        'title' => $faker->word,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(Group::class, function (Group $group, Faker $faker) {

    $group->user = $this->user;
    return $group;
});

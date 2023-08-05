<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EmbeddedForm;
use App\Models\Group;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(EmbeddedForm::class, function (Faker $faker) {

    // Create a group
    $this->group = factory(Group::class)->create();

    return [
        'user_id' => $this->group->user_id,
        'name' => $faker->unique()->word,
        'group_id' => $this->group->id,

        'custom_short_url' => $faker->url(),
        'description' => $faker->sentence(9),
        'input_fields' => [],
        'form_header_text' => $faker->sentence(1),
        'form_header_image' => [$faker->imageUrl(400, 400)],
        'form_footer_text' => $faker->sentence(1),
        'form_footer_image' => [$faker->imageUrl(400, 400)],
        'form_background_color' => $faker->hexColor(),
        'form_width' => $faker->numberBetween(200, 1000),
        'form_border_radius' => $faker->numberBetween(1, 10),

        'submit_button_color' => $faker->hexColor(),
        'submit_button_text' => $faker->word(),
        'submit_button_text_color' => $faker->hexColor(),
        'submit_button_text_before' => $faker->word(),
        'submit_button_text_after' => $faker->word(),
        'thank_you_message' => $faker->sentence(3),
        'thank_you_message_url' => [$faker->url(),null][array_rand([0,1])],

        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(EmbeddedForm::class, function (EmbeddedForm $embedded_form, Faker $faker) {

    $embedded_form->user = $this->group->user;
    $embedded_form->group = $this->group;
    return $embedded_form;
});

<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create users
        User::firstOrCreate(
            [
                'email' => 'blue@gmail.com'
            ],
            [
                'first_name' => 'blue',
                'last_name' => 'blue',
                'email_verified_at' => now(),
                'password' => bcrypt('blueblue'),
            ]
        );

        User::firstOrCreate(
            [
                'email' => 'green@gmail.com'
            ],
            [
                'first_name' => 'green',
                'last_name' => 'green',
                'email_verified_at' => now(),
                'password' => bcrypt('greengreen'),
            ]
        );

        // Run user factory
        if (config('app.env') === 'local') {
            factory(User::class, 10)->create();
        }
    }
}

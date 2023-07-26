<?php

use App\Models\ContactGroup;
use Illuminate\Database\Seeder;

class ContactGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run Broadcast factory
        if (config('app.env') === 'local') {
            factory(ContactGroup::class, 10)->create();
        }
    }
}

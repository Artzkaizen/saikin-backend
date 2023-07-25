<?php

use Illuminate\Database\Seeder;

class BroadcastTemplateTableSeeder extends Seeder
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
            factory(BroadcastTemplate::class, 10)->create();
        }
    }
}

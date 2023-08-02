<?php

use App\Models\PaymentPlan;
use Illuminate\Database\Seeder;

class PaymentPlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run payment plan factory
        if (config('app.env') === 'local') {
            factory(PaymentPlan::class, 10)->create();
        }
    }
}

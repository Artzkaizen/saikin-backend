<?php

namespace App\Observers;

use App\Models\Benefit;
use App\Models\PaymentPlan;

class PaymentPlanObserver
{
    /**
     * Handle the payment plan "created" event.
     *
     * @param  \App\Models\PaymentPlan  $paymentPlan
     * @return void
     */
    public function created(PaymentPlan $paymentPlan)
    {
        if (!empty($payment_plan->payment_plan_benefits)) {

            // Check if the given benefits are valid
            $payment_plan_benefits = collect($payment_plan->payment_plan_benefits ?? [])->keys();
            $payment_plan_benefits = Benefit::where('user_id',$payment_plan->user_id)->whereIn('id', $payment_plan_benefits)->get();

            // Sync valid payment plan benefits
            $payment_plan->benefits()->sync($payment_plan_benefits);

            // Update payment plan benefits
            PaymentPlan::withoutEvents(function () use ($payment_plan, $payment_plan_benefits) {
                PaymentPlan::where('id',$payment_plan->id)->update(['payment_plan_benefits'=>$payment_plan_benefits->pluck('id')->toArray()]);
            });
        }
    }

    /**
     * Handle the payment plan "updated" event.
     *
     * @param  \App\Models\PaymentPlan  $paymentPlan
     * @return void
     */
    public function updated(PaymentPlan $paymentPlan)
    {
        if ($payment_plan->wasChanged('payment_plan_benefits')) {

            // Check if the given benefits are valid
            $payment_plan_benefits = collect($payment_plan->payment_plan_benefits ?? [])->keys();
            $payment_plan_benefits = Benefit::where('user_id',$payment_plan->user_id)->whereIn('id', $payment_plan_benefits)->get();

            // Sync valid payment plan benefits
            $payment_plan->benefits()->sync($payment_plan_benefits);

            // Update payment plan benefits
            PaymentPlan::withoutEvents(function () use ($payment_plan, $payment_plan_benefits) { 
                PaymentPlan::where('id',$payment_plan->id)->update(['payment_plan_benefits'=>$payment_plan_benefits->pluck('id')->toArray()]);
            });
        }
    }

    /**
     * Handle the payment plan "deleted" event.
     *
     * @param  \App\Models\PaymentPlan  $paymentPlan
     * @return void
     */
    public function deleted(PaymentPlan $paymentPlan)
    {
        // Detach all benefits from the payment_plan
        $payment_plan->benefits()->detach();
    }

    /**
     * Handle the payment plan "restored" event.
     *
     * @param  \App\Models\PaymentPlan  $paymentPlan
     * @return void
     */
    public function restored(PaymentPlan $paymentPlan)
    {
        // Check if the previous benefits are still valid
        $payment_plan_benefits = collect($payment_plan->payment_plan_benefits ?? [])->keys();
        $payment_plan_benefits = Benefit::where('user_id',$payment_plan->user_id)->whereIn('id', $payment_plan_benefits)->get();

        // Attach valid benefits to the payment plan
        $payment_plan->benefits()->attach($payment_plan_benefits);

        // Update payment plan benefits
        PaymentPlan::withoutEvents(function () use ($payment_plan, $payment_plan_benefits) { 
            PaymentPlan::where('id',$payment_plan->id)->update(['payment_plan_benefits'=>$payment_plan_benefits->pluck('id')->toArray()]);
        });
    }

    /**
     * Handle the payment plan "force deleted" event.
     *
     * @param  \App\Models\PaymentPlan  $paymentPlan
     * @return void
     */
    public function forceDeleted(PaymentPlan $paymentPlan)
    {
        // Detach all benefits from the payment_plan
        $payment_plan->benefits()->detach();
    }
}

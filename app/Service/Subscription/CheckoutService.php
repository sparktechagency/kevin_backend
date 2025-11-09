<?php

namespace App\Service\Subscription;

use App\Models\Plan;
use App\Models\Subscription;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class CheckoutService
{
    use ResponseHelper;
    public function checkout($planId)
    {
        $user = Auth::user();
        $plan = Plan::find($planId);
        if (!$plan) {
            return $this->errorResponse('Plan not found.');
        }
        $existing = Subscription::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return $this->errorResponse('You are already subscribed to this plan.');
        }
        if ($plan->price == 0) {
            // Cancel all active subscriptions
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'canceled']);

            // Create new free subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => 'free_' . uniqid(),
                'status' => 'active',
                'ends_at' => now()->addMonth(),
            ]);
            return $this->successResponse( $subscription,'Successfully subscribed to Starter plan!');
        }
        // Paid plan handling (pseudo checkout)
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'stripe_subscription_id' => 'manual_' . uniqid(),
            'status' => 'trial', // trial period
            'trial_ends_at' => now()->addDays(7), // 7-day free trial
            'ends_at' => now()->addMonth(), // optional, will update after payment
        ]);
        if($subscription->trial_ends_at){
            $user->trial_ends_at = now()->addDays(7);
            $user->save();
        }
        return $this->successResponse( $subscription,'Subscription created. Awaiting payment confirmation.');
    }
}

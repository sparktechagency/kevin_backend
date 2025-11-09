<?php

namespace App\Service\Subscription;

use App\Models\Plan;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class PaymentIntentService
{
    use ResponseHelper;

    public function paymentIntent($plan)
    {
        $planId = $plan;
        if (!$planId) {
            return $this->errorResponse('Plan ID is required.');
        }
        $plan = Plan::find($planId);
        if (!$plan) {
            return $this->errorResponse('Plan not found.');
        }
       $stripe = new StripeClient(env('STRIPE_SECRET'));
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $plan->price * 100,
            'currency' => 'usd',
        ]);
        return $this->successResponse($paymentIntent->client_secret, 'PaymentIntent created successfully.');
    }
}

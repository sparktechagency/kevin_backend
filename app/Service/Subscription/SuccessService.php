<?php

namespace App\Service\Subscription;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\DB;

class SuccessService
{
   use ResponseHelper;

   public function success($request, $planId)
    {
        $plan = Plan::find($planId);
        if (!$plan) {
            return $this->errorResponse("Plan not found.");
        }
        if ($plan->name === 'Starter') {
            return $this->errorResponse('You cannot cancel the Starter plan.');
        }
        $user = auth()->user();
        $subscription = Subscription::where('user_id',$user->id)->where('plan_id',$planId)->first();
        DB::beginTransaction();
        try {
            // Transaction create
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'subscription_id' => $subscription->id,
                'stripe_payment_intent_id' => $request->stripe_payment_intent_id ?? null,
                'type' => 'subscription',
                'status' => 'completed',
                'total_amount' => $plan->price,
                'currency' => 'usd',
                'payment_method' => $request->payment_method ?? 'card',
                'paid_at' => now(),
            ]);

            // Subscription update
            $subscription->update([
                'status' => 'active',
                'trial_ends_at' => null,
                'ends_at' => now()->addMonth(),
            ]);
             Subscription::where('user_id', $user->id)
                ->where('id', '!=', $subscription->id)
                ->update(['status' => 'inactive','trial_ends_at'=>null]);

            // User update
            $user->update([
                'trial_ends_at' => null,
                'ends_at' => now()->addMonth(),
            ]);

            DB::commit();

            return $this->successResponse([
                'transaction' => $transaction,
                'plan' => $plan,
            ], "Payment successful and plan activated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Something went wrong!");
        }
    }
}

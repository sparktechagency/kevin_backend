<?php

namespace App\Service\Subscription;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Service\Notification\NotificationService;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuccessService
{
    use ResponseHelper;

    public function success($request, $planId)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return $this->errorResponse('User not authenticated.');
            }

            $plan = Plan::find($planId);
            if (!$plan) {
                return $this->errorResponse("Plan not found.");
            }

            $isFreePlan = $plan->id == 1;

            /** -----------------------------
             *  Trial subscription
             * ------------------------------*/
            if ($request->status === 'trial') {

                $trialEndsAt = $isFreePlan
                    ? now()->addMonths(3)
                    : now()->addDays(7);

                $endsAt = $isFreePlan
                    ? now()->addMonths(3)
                    : now()->addMonth();

                $subscription = Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id' => $plan->id,
                        'status' => 'trial',
                        'trial_ends_at' => $trialEndsAt,
                        'ends_at' => $endsAt,
                        'stripe_subscription_id' => 'manual_' . uniqid(),
                    ]
                );

                $user->update([
                    'is_subscribed' => 'trial',
                    'trial_ends_at' => $trialEndsAt,
                    'ends_at' => $endsAt,
                ]);

                return $this->successResponse(
                    $subscription,
                    'You are now on a trial plan.'
                );
            }

            /** -----------------------------
             *  Upgrade subscription
             * ------------------------------*/
            if ($request->status === 'upgrade') {

                DB::beginTransaction();

                $subscription = Subscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id' => $plan->id,
                        'status' => 'active',
                        'trial_ends_at' => null,
                        'ends_at' => now()->addMonth(),
                        'stripe_subscription_id' => 'manual_' . uniqid(),
                    ]
                );

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'subscription_id' => $subscription->id,
                    'stripe_payment_intent_id' => $request->stripe_payment_intent_id,
                    'type' => 'subscription',
                    'status' => 'completed',
                    'total_amount' => $plan->price,
                    'currency' => 'usd',
                    'payment_method' => $request->payment_method ?? 'card',
                    'paid_at' => now(),
                ]);

                // Cancel other subscriptions
                Subscription::where('user_id', $user->id)
                    ->where('id', '!=', $subscription->id)
                    ->update([
                        'status' => 'cancelled',
                        'trial_ends_at' => null
                    ]);

                $user->update([
                    'is_subscribed' => 'active',
                    'trial_ends_at' => null,
                    'ends_at' => now()->addMonth(),
                ]);

                DB::commit();
            }

            /** -----------------------------
             *  Notifications
             * ------------------------------*/
            $notificationData = [
                'name' => 'Subscription Update',
                'message' => "{$user->name} subscribed to {$plan->name} plan.",
                'type' => 'SUBSCRIPTION',
                'plan_id' => $plan->id
            ];

            $notificationService = new NotificationService();
            $notificationService->send($user, $notificationData);

            $admins = User::where('role', 'ADMIN')->get();
            foreach ($admins as $admin) {
                $notificationService->send($admin, $notificationData);
            }

            return $this->successResponse([
                'transaction' => $transaction ?? null,
                'subscription' => $subscription,
                'plan' => $plan
            ], "Subscription processed successfully.");

        } catch (\Exception $e) {

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Subscription activation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse("Something went wrong! Please try again.");
        }
    }
}

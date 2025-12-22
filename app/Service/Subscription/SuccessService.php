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
            $plan = Plan::find($planId);
            if (!$plan) {
                return $this->errorResponse("Plan not found.");
            }

            // Starter plan cannot be purchased
            if ($plan->id == 1) {
                return $this->errorResponse('Starter plan is free and cannot be purchased.');
            }

            $user = auth()->user();
            if (!$user) {
                return $this->errorResponse('User not authenticated.');
            }

            // Create OR fetch subscription
            $subscription = Subscription::UpdateOrCreate(
                ['user_id' => $user->id, 'plan_id' => $plan->id],
                [
                    'status' => 'trial',
                    'trial_ends_at' => now()->addDays(7),
                    'ends_at' => now()->addMonth(),
                    'stripe_subscription_id' => 'manual_' . uniqid(),
                ]
            );

            // return $subscription;
            if ($subscription->status === 'trial' && $request->status == 'trial') {

                // Update user info
                $user->update([
                    'is_subscribed' => $subscription->status,
                    'trial_ends_at' => $subscription->trial_ends_at,
                    'ends_at' => now()->addMonth(),
                ]);

                return $this->successResponse([], 'You are now on a trial plan. Enjoy your free access!');
            }
            // return $request;
            if ($request->status === 'upgrade') {

                DB::beginTransaction();

                // Create transaction
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

                // Activate subscription
                $subscription->update([
                    'status' => 'active',
                    'trial_ends_at' => null,
                    'ends_at' => now()->addMonth(),
                ]);

                // Cancel all other subscriptions
                Subscription::where('user_id', $user->id)
                    ->where('id', '!=', $subscription->id)
                    ->update([
                        'status' => 'cancelled',
                        'trial_ends_at' => null
                    ]);

                // Update user info
                $user->update([
                    'is_subscribed' => $subscription->status,
                    'trial_ends_at' => null,
                    'ends_at' => now()->addMonth(),
                ]);

                DB::commit();
            } else {
                // Avoid "Undefined variable $transaction"
                $transaction = null;
            }

            /**
             * --------------------------------------
             * 3️⃣ Notifications
             * --------------------------------------
             */
            $notificationData = [
                'name' => 'Subscription Update',
                'message' => "{$user->name} has switched to the {$plan->name} plan.",
                'type' => 'SUBSCRIPTION',
                'plan_id' => $plan->id
            ];

            $notificationService = new NotificationService();

            // Notify User
            $notificationService->send($user, $notificationData);

            // Notify Admins
            $admins = User::where('role', 'ADMIN')->get();
            foreach ($admins as $admin) {
                $notificationService->send($admin, $notificationData);
            }

            return $this->successResponse([
                'transaction' => $transaction,
                'subscription' => $subscription,
                'plan' => $plan
            ], "Payment successful and plan activated.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription activation error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse("Something went wrong! Please try again.");
        }
    }
}

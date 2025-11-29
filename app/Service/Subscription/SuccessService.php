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

            if ($plan->name === 'Starter') {
                return $this->errorResponse('You cannot cancel the Starter plan.');
            }

            $user = auth()->user();
            if (!$user) {
                return $this->errorResponse('User not authenticated.');
            }

            $subscription = Subscription::where('user_id', $user->id)
                ->where('plan_id', $planId)
                ->first();

            if (!$subscription) {
                return $this->errorResponse("Subscription not found.");
            }

            DB::beginTransaction();

            // 1️⃣ Create transaction
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

            // 2️⃣ Update current subscription
            $subscription->update([
                'status' => 'active',
                'trial_ends_at' => null,
                'ends_at' => now()->addMonth(),
            ]);

            // 3️⃣ Deactivate other subscriptions
            Subscription::where('user_id', $user->id)
                ->where('id', '!=', $subscription->id)
                ->update(['status' => 'inactive', 'trial_ends_at' => null]);

            // 4️⃣ Update user subscription info
            $user->update([
                'trial_ends_at' => null,
                'ends_at' => now()->addMonth(),
                'is_subscribed' => $plan->name,
            ]);

            DB::commit();

            // 5️⃣ Send notifications
            $notificationService = new NotificationService();

            // Notify the user
            $notificationService->send($user, [
                'name' => 'Subscription Activated',
                'message' => "Your {$plan->name} plan has been activated successfully.",
                'type' => 'SUBSCRIPTION',
                'plan_id' => $plan->id,
            ]);

            // Notify all admins
            $admins = User::where('role', 'ADMIN')->get();
            if ($admins->isNotEmpty()) {
                foreach ($admins as $admin) {
                    $notificationService->send($admin, [
                        'name' => 'New Subscription Payment',
                        'message' => "{$user->name} has successfully subscribed to the {$plan->name} plan.",
                        'type' => 'SUBSCRIPTION',
                        'plan_id' => $plan->id,
                    ]);
                }
            }

            return $this->successResponse([
                'transaction' => $transaction,
                'plan' => $plan,
            ], "Payment successful and plan activated.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription success error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse("Something went wrong! Please try again.");
        }
    }
}

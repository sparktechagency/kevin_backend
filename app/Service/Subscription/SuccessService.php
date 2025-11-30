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

        // Get or create subscription for this plan
        $subscription = Subscription::firstOrCreate(
            ['user_id' => $user->id, 'plan_id' => $plan->id],
            [
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(7),
                'ends_at' => now()->addMonth(),
                'stripe_subscription_id' => 'manual_' . uniqid(),
            ]
        );

        DB::beginTransaction();

        // 1️⃣ Create transaction record
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

        // 2️⃣ Activate this subscription
        $subscription->update([
            'status' => 'active',
            'trial_ends_at' => null,
            'ends_at' => now()->addMonth(),
        ]);

        // 3️⃣ Cancel all other subscriptions
        Subscription::where('user_id', $user->id)
            ->where('id', '!=', $subscription->id)
            ->update(['status' => 'cancelled', 'trial_ends_at' => null]);

        // 4️⃣ Update user subscription info
        $user->update([
            'is_subscribed' => $subscription->status,
            'trial_ends_at' => null,
            'ends_at' => now()->addMonth(),
        ]);

        DB::commit();

        // 5️⃣ Send notifications in your preferred format
        $notificationData = [
            'name' => 'Subscription Update',
            'message' => "{$user->name} has switched to the {$plan->name} plan.",
            'type' => 'SUBSCRIPTION',
            'plan_id' => $plan->id
        ];

        $notificationService = new NotificationService();

        // Send notification to user
        $notificationService->send($user, $notificationData);

        // Send notification to all admins
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

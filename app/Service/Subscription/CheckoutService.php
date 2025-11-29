<?php

namespace App\Service\Subscription;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Service\Notification\NotificationService;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class CheckoutService
{
    use ResponseHelper;

    public function checkout($planId)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User not authenticated.');
        }

        $plan = Plan::find($planId);
        if (!$plan) {
            return $this->errorResponse('Plan not found.');
        }

        // Check if already subscribed to this plan
        $existing = Subscription::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->first();

        if ($existing && $existing->status === 'active') {
            return $this->errorResponse('You are already subscribed to this plan.');
        }

        // Cancel all active subscriptions
        Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'canceled']);

        if ($plan->price == 0) {
            // Free plan
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => 'free_' . uniqid(),
                'status' => 'active',
                'ends_at' => now()->addMonth(),
            ]);

            $user->is_subscribed = $plan->name;
            $user->trial_ends_at = null;
            $user->save();

            $message = "You have successfully subscribed to the {$plan->name} plan.";

        } else {
            // Paid plan (trial)
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => 'manual_' . uniqid(),
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(7),
                'ends_at' => now()->addMonth(),
            ]);

            $user->is_subscribed = $plan->name;
            $user->trial_ends_at = now()->addDays(7);
            $user->save();

            $message = "Trial subscription for {$plan->name} plan created. Awaiting payment confirmation.";
        }

        // Prepare notification data
        $notificationData = [
            'name' => 'New Subscription',
            'message' => "{$user->name} has subscribed to the {$plan->name} plan.",
            'type' => 'SUBSCRIPTION',
            'plan_id' => $plan->id
        ];

        $notificationService = new NotificationService();

        // Notify the user
        $notificationService->send($user, $notificationData);

        // Notify all admins
        $admins = User::where('role', 'ADMIN')->get();
        if ($admins->isNotEmpty()) {
            foreach ($admins as $admin) {
                $notificationService->send($admin, $notificationData);
            }
        }

        return $this->successResponse($subscription, $message);
    }
}

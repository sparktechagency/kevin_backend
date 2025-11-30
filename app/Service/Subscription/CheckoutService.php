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

        // Check if user already has active subscription or trial
        $currentSub = Subscription::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere(function ($q) {
                        $q->where('status', 'trial')
                            ->where(function ($subQ) {
                                $subQ->whereNull('trial_ends_at')
                                    ->orWhere('trial_ends_at', '>', now());
                            });
                    });
            })
            ->first();

        // If the current subscription is for the same plan, prevent duplicate
        if ($currentSub && $currentSub->plan_id == $plan->id) {
            return $this->errorResponse('You are already on this plan.');
        }

        // Determine trial length
        $trialEnds = $plan->id == 1 ? now()->addMonths(3) : now()->addDays(7);

        if ($currentSub) {
            // Update existing subscription instead of creating a new one
            $currentSub->update([
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $plan->id == 1 ? 'free_' . uniqid() : 'manual_' . uniqid(),
                'status' => 'trial',
                'trial_ends_at' => $trialEnds,
                'ends_at' => now()->addMonth(),
            ]);

            $subscription = $currentSub;
            $message = $plan->id == 1
                ? "Your subscription has been updated. You are now on a 3-month free trial of the {$plan->name} plan."
                : "Your subscription has been updated. Trial for {$plan->name} plan created.";
        } else {
            // Create new subscription if user had no previous one
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $plan->id == 1 ? 'free_' . uniqid() : 'manual_' . uniqid(),
                'status' => 'trial',
                'trial_ends_at' => $trialEnds,
                'ends_at' => now()->addMonth(),
            ]);

            $message = $plan->id == 1
                ? "You are now on a 3-month free trial of the {$plan->name} plan."
                : "Trial subscription for {$plan->name} plan created. Awaiting payment confirmation.";
        }

        // Update user info
        $user->is_subscribed = $subscription->status;
        $user->trial_ends_at = $trialEnds;
        $user->save();

        // Notifications
        $notificationData = [
            'name' => 'Subscription Update',
            'message' => "{$user->name} has switched to the {$plan->name} plan.",
            'type' => 'SUBSCRIPTION',
            'plan_id' => $plan->id
        ];

        $notificationService = new NotificationService();
        $notificationService->send($user, $notificationData);

        $admins = User::where('role', 'ADMIN')->get();
        foreach ($admins as $admin) {
            $notificationService->send($admin, $notificationData);
        }

        return $this->successResponse($subscription, $message);
    }



}

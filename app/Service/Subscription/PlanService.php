<?php

namespace App\Service\Subscription;

use App\Models\Plan;
use App\Traits\ResponseHelper;

class PlanService
{
  use ResponseHelper;
  public function plans()
    {
        $user = auth()->user(); // Authenticated user
        $plans = Plan::where('is_active', true)->get();
        // Get user's current subscription (active or trial)
        $userSubscription = $user ? $user->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->latest()
            ->first() : null;
        // Build response
        $response = [
            'plans' => $plans->map(function ($plan) use ($userSubscription) {
                $isCurrent = $userSubscription && $userSubscription->plan_id === $plan->id;
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'interval' => $plan->interval,
                    'features' => $plan->features,
                    'is_active' => $plan->is_active,
                    'is_current' => $isCurrent,
                    'is_trial_active' => $isCurrent && $userSubscription->status === 'trial',
                ];
            }),
            'user_subscription' => $userSubscription ? [
                'plan_id' => $userSubscription->plan_id,
                'status' => $userSubscription->status,
                'trial_ends_at' => $userSubscription->trial_ends_at,
                'ends_at' => $userSubscription->ends_at
            ] : null
        ];
        return $this->successResponse($response, "Plans retrieved successfully.");
    }
}

<?php

namespace App\Service\Subscription;

use App\Models\Subscription;
use App\Traits\ResponseHelper;

class CancelService
{
    use ResponseHelper;

    public function cancelSubscription($planId)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->errorResponse('User not authenticated.');
        }

        // Find active subscription or trial for this plan
        $subscription = Subscription::where('user_id', $user->id)
            ->where('plan_id', $planId)
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

        if (!$subscription) {
            return $this->errorResponse('No active subscription or trial found for this plan.');
        }

        // Update status to cancelled
        $subscription->update(['status' => 'cancelled']);

        return $this->successResponse($subscription, 'Subscription cancelled successfully.');
    }
}

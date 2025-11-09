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
        $subscription = Subscription::where('user_id', $user->id)
            ->where('plan_id', $planId)
            ->where('status', 'active')
            ->first();
        if (!$subscription) {
            return $this->errorResponse('No active subscription found.');
        }
        $subscription->update(['status' => 'cancelled']);
        return $this->successResponse( $subscription,'Subscription cancelled successfully.');
    }
}

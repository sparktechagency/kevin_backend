<?php

namespace App\Service\Subscription;

use App\Models\Subscription;
use App\Traits\ResponseHelper;

class RemuseService
{
   use ResponseHelper;
    public function resumeSubscription($planId)
    {
        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('plan_id', $planId)
            ->where('status', 'cancelled')
            ->first();
        if (!$subscription) {
            return $this->errorResponse('No cancelled subscription found.');
        }
        $subscription->update(['status' => 'active']);
        Subscription::where('user_id', $user->id)
            ->where('id', '!=', $subscription->id)
            ->update(['status' => 'inactive']);
        return $this->successResponse( $subscription,'Subscription resumed successfully.');
    }
}

<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Models\Subscription;
use App\Traits\ResponseHelper;
use Carbon\Carbon;

class StoreService
{
    use ResponseHelper;

    public function store($data)
    {
        $user = auth()->user();

        // Get the user's active subscription (active or trial)
        $subscription = Subscription::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 'trial');
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->first();

        if (!$subscription) {
            return $this->errorResponse("You need an active subscription or trial to create a dream.");
        }

        // Allowed frequencies based on plan
        $allowedFrequencies = [];
        if ($subscription->plan->id == 1) {
            $allowedFrequencies = ['Monthly'];
        } elseif ($subscription->plan->id == 2) {
            $allowedFrequencies = ['Weekly', 'Monthly'];
        } else {
            // Plan 3 (Master) → all frequencies
            $allowedFrequencies = ['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Yearly'];
        }

        // Check if submitted frequency is allowed
        if (!isset($data['frequency']) || !in_array($data['frequency'], $allowedFrequencies)) {
            return $this->errorResponse("You are not allowed to create {$data['frequency']} for your plan.");
        }

        // Count existing dreams in current month for Plan 1 & 2 if needed
        if ($subscription->plan->id == 1 || $subscription->plan->id == 2) {
            $currentMonthDreams = Dream::where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            // Limit Plan 1 & 2 users to max 3 dreams per month
            if ($currentMonthDreams >= 3) {
                return $this->errorResponse("You are on the {$subscription->plan->name} plan. Maximum 3 dreams per month allowed.");
            }
        }

        $data['user_id'] = $user->id;

        // Encode goals if provided
        if (isset($data['goal'])) {
            $data['goal'] = json_encode($data['goal']);
        }

        // Store subscription ID with the dream
        $data['subscription_id'] = $subscription->id;

        $dream = Dream::create($data);

        return $this->successResponse($dream, 'Dream created successfully.');
    }
}

<?php

namespace App\Service\Auth;

use App\Models\Subscription;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    use ResponseHelper;

    public function getProfile()
    {
        $user = Auth::user();
        // User's subscription (active or trial)
        $subscription = Subscription::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 'trial');
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->first();

        $data = [
            "id"              => $user->id,
            "name"            => $user->name,
            "email"           => $user->email,
            "employee_pin"    => $user->employee_pin,
            "email_verified_at" => $user->email_verified_at,
            "contact_number"  => $user->contact_number,
            "avatar"          => $user->avatar,
            "is_banned"       => $user->is_banned,
            "is_notification" => $user->is_notification,

            // Dynamic Subscription Status
            "is_subscribed"   => $subscription->status ?? "none",

            // Subscription Dates
            "trial_ends_at"   => $subscription->trial_ends_at ?? null,
            "ends_at"         => $subscription->ends_at ?? null,

            "role"            => $user->role,
            "created_at"      => $user->created_at,
            "updated_at"      => $user->updated_at,

            // Optional - full subscription details
            "subscription"    => $subscription,
        ];

        return $this->successResponse($data, "Profile retrieved successfully.");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class UpdateSubscriptionStatus extends Command
{
    protected $signature = 'subscriptions:update-status';
    protected $description = 'Check and update subscription statuses every minute';

    public function handle()
    {
        $now = Carbon::now();

        // 1️ End trial subscriptions
        $trialSubscriptions = Subscription::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', $now)
            ->get();

        foreach ($trialSubscriptions as $subscription) {
            $subscription->update([
                'status' => 'expired',
            ]);

            $this->info("Trial ended for subscription ID: {$subscription->id}");
        }

        // 2️ End active subscriptions
        $activeSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', $now)
            ->get();

        foreach ($activeSubscriptions as $subscription) {
            $subscription->update([
                'status' => 'expired',
            ]);

            $this->info("Subscription expired ID: {$subscription->id}");
        }

        $this->info('All expired subscriptions updated successfully.');
    }
}

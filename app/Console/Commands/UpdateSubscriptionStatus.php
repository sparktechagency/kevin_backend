<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateSubscriptionStatus extends Command
{
    protected $signature = 'subscriptions:update-status';
    protected $description = 'Check and update subscription statuses every minute';

    public function handle()
    {
        try {
            $now = Carbon::now();

            // 1️⃣ Expire trial subscriptions
            $trialUpdated = Subscription::where('status', 'trial')
                ->whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '<', $now)
                ->update(['status' => 'expired']);

            if ($trialUpdated) {
                $this->info("Expired {$trialUpdated} trial subscriptions.");
            }

            // 2️⃣ Expire active subscriptions
            $activeUpdated = Subscription::where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '<', $now)
                ->update(['status' => 'expired']);

            if ($activeUpdated) {
                $this->info("Expired {$activeUpdated} active subscriptions.");
            }

            $this->info('All expired subscriptions updated successfully.');

        } catch (\Exception $e) {
            Log::error('UpdateSubscriptionStatus error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Something went wrong while updating subscriptions.');
        }
    }
}

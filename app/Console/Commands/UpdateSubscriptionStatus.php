<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Service\Notification\NotificationService;

class UpdateSubscriptionStatus extends Command
{
    protected $signature = 'subscriptions:update-status';
    protected $description = 'Check and update subscription statuses every minute';

    public function handle()
    {
        try {
            $now = Carbon::now();
            $notificationService = new NotificationService();

            // -------------------------------
            // 1️⃣ Expire trial subscriptions
            // -------------------------------
            $trialSubs = Subscription::where('status', 'trial')
                ->whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '<', $now)
                ->get();

            foreach ($trialSubs as $sub) {

                $sub->update(['status' => 'expired']);

                $user = User::find($sub->user_id);
                if ($user) {
                    $notificationData = [
                        'name' => 'Subscription Update',
                        'message' => "Your trial period has expired.",
                        'type' => 'SUBSCRIPTION',
                        'plan_id' => $sub->plan_id,
                    ];

                    // Notify USER ONLY
                    $notificationService->send($user, $notificationData);
                }
            }

            if ($trialSubs->count()) {
                $this->info("Expired {$trialSubs->count()} trial subscriptions.");
            }


            // -------------------------------
            // 2️⃣ Expire active subscriptions
            // -------------------------------
            $activeSubs = Subscription::where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '<', $now)
                ->get();

            foreach ($activeSubs as $sub) {

                $sub->update(['status' => 'expired']);

                $user = User::find($sub->user_id);
                if ($user) {

                    $notificationData = [
                        'name' => 'Subscription Update',
                        'message' => "Your subscription has expired.",
                        'type' => 'SUBSCRIPTION',
                        'plan_id' => $sub->plan_id,
                    ];

                    // Notify USER ONLY
                    $notificationService->send($user, $notificationData);
                }
            }

            if ($activeSubs->count()) {
                $this->info("Expired {$activeSubs->count()} active subscriptions.");
            }

            $this->info('All expired subscription statuses updated successfully.');

        } catch (\Exception $e) {
            Log::error('UpdateSubscriptionStatus error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Something went wrong while updating subscriptions.');
        }
    }
}

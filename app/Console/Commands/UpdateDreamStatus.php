<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dream;
use Carbon\Carbon;

class UpdateDreamStatus extends Command
{
    protected $signature = 'dreams:update-status';
    protected $description = 'Update dream end_date and status automatically based on start_date and frequency.';

    public function handle()
    {
        $now = Carbon::now();
        $dreams = Dream::all();

        foreach ($dreams as $dream) {
            $updated = false;

            $startDate = $dream->start_date ? Carbon::parse($dream->start_date) : null;
            $endDate   = $dream->end_date   ? Carbon::parse($dream->end_date)   : null;
            $currentStatus = $dream->status;

            /**
             * 1️⃣ Set end_date if missing
             */
            if (!$endDate && $startDate) {

                switch (strtolower($dream->frequency)) {

                    case 'weekly':
                        $endDate = $startDate->copy()->addDays(6); // Weekly = 7 days total
                        break;

                    case 'monthly':
                        $endDate = $startDate->copy()->addDays(29); // Monthly = 30 days
                        break;

                    case 'quarterly':
                        $endDate = $startDate->copy()->addDays(89); // Quarterly = 90 days
                        break;

                    case 'yearly':
                        $endDate = $startDate->copy()->addDays(364); // Yearly = 365 days
                        break;

                    case 'daily':
                    default:
                        $endDate = $startDate->copy()->addDay(); // Daily = 1 day
                        break;
                }

                $dream->end_date = $endDate->toDateString();
                $updated = true;
            }

            /**
             * 2️⃣ Determine expected status
             */
            if ($endDate && $endDate->lt($now)) {
                $expectedStatus = 'Completed';
            } elseif ($startDate && $startDate->gt($now)) {
                $expectedStatus = 'Upcoming';
            } elseif ($startDate && $endDate && $startDate->lte($now) && $endDate->gte($now)) {
                $expectedStatus = 'Active';
            } else {
                $expectedStatus = $currentStatus;
            }

            /**
             * 3️⃣ Update status if changed
             */
            if ($currentStatus !== $expectedStatus) {
                $dream->status = $expectedStatus;
                $updated = true;
            }

            /**
             * 4️⃣ Save only if needed
             */
            if ($updated) {
                $dream->save();
            }
        }

        $this->info("Dream statuses updated successfully.");
    }
}

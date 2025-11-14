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

        $allDreams = Dream::all();

        foreach ($allDreams as $dream) {
            $updated = false; // flag to check if we need to save

            $startDate = $dream->start_date ? Carbon::parse($dream->start_date) : null;
            $endDate = $dream->end_date ? Carbon::parse($dream->end_date) : null;
            $currentStatus = $dream->status;

            // 1️⃣ Fill missing end_date based on frequency (if null)
            if (!$endDate && $startDate) {
                switch (strtolower($dream->frequency)) {
                    case 'weekly':
                        $endDate = $startDate->copy()->addDays(6);
                        break;

                    case 'monthly':
                        $endDate = $startDate->copy()->addMonth();
                        break;

                    case 'daily':
                    default:
                        $endDate = $startDate;
                        break;
                }

                $dream->end_date = $endDate->toDateString();
                $updated = true;
            }

            // 2️⃣ Determine expected status
            $expectedStatus = $currentStatus; // default: no change

            if ($endDate && $endDate->lt($now)) {
                $expectedStatus = 'Completed';
            } elseif ($startDate && $startDate->gt($now)) {
                $expectedStatus = 'Upcoming';
            } elseif ($startDate && $endDate && $startDate->lte($now) && $endDate->gte($now)) {
                $expectedStatus = 'Active';
            }

            // 3️⃣ Update status only if different
            if ($currentStatus !== $expectedStatus) {
                $dream->status = $expectedStatus;
                $updated = true;
            }

            // 4️⃣ Save only if something changed
            if ($updated) {
                $dream->save();
            }
        }

        $this->info("Dreams updated successfully (only changed records were saved).");
    }
}

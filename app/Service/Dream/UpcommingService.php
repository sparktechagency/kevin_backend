<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class UpcommingService
{
    use ResponseHelper;

    public function upcoming($request)
    {
        $userId = auth()->id();
        $dreams = Dream::where('user_id', $userId)
            ->where('status', 'Upcoming')
            ->get();

        $dailyActivities = [];
        $weeklyActivities = [];
        $monthlyActivities = [];
        $quarterlyActivities = [];
        $yearlyActivities = [];

        $completedDreams = [];
        $upcomingDreams = [];

        $today = Carbon::today();

        foreach ($dreams as $dream) {

            $activityStatus = [];
            $completedActivities = 0;
            $totalActivities = 0;

            $startDate = Carbon::parse($dream->start_date);

            // -------------------------
            // END DATE LOGIC UPDATED
            // -------------------------
            $endDate = match ($dream->frequency) {
                'Weekly'     => $startDate->copy()->addDays(6),      // 7 days
                'Monthly'    => $startDate->copy()->addDays(29),     // 30 days
                'Quarterly'  => $startDate->copy()->addDays(89),     // 90 days
                'Yearly'     => $startDate->copy()->addDays(364),    // 365 days
                default      => $dream->end_date
                                ? Carbon::parse($dream->end_date)
                                : $startDate->copy()->addDays(29),
            };

            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            $activities = DreamActivity::where('user_id', $userId)
                ->where('dream_id', $dream->id)
                ->where('type', $dream->frequency)
                ->pluck('created_at')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->toArray();

            // Activity Status Loop
            foreach ($period as $date) {

                $key = $date->format('Y-m-d');

                if ($date->gt($today)) {
                    $activityStatus[$key] = 'locked';
                } else {
                    $done = in_array($key, $activities);
                    $activityStatus[$key] = $done;
                    if ($done) $completedActivities++;
                }

                $totalActivities++;
            }

            // -------------------------
            // PROGRESS CALCULATION
            // -------------------------
            $progress = 0;

            if ($dream->frequency === 'Weekly') {
                $required = $dream->per_week ?? 7;
                $progress = $required > 0 ? round(($completedActivities / $required) * 100, 2) : 0;

            } elseif ($dream->frequency === 'Monthly') {
                $required = $dream->per_month ?? 30;
                $progress = $required > 0 ? round(($completedActivities / $required) * 100, 2) : 0;

            } elseif ($dream->frequency === 'Quarterly') {
                $required = $dream->per_quarter ?? 90;
                $progress = $required > 0 ? round(($completedActivities / $required) * 100, 2) : 0;

            } elseif ($dream->frequency === 'Yearly') {
                $required = $dream->per_year ?? 365;
                $progress = $required > 0 ? round(($completedActivities / $required) * 100, 2) : 0;

            } else {
                // Daily default
                $progress = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 2) : 0;
            }


            // Remaining Status
            if ($today->lt($startDate)) {
                $remainingStatus = 'Upcoming';
            } elseif ($today->gt($endDate)) {
                $remainingStatus = 'Completed';
            } elseif ($today->eq($endDate)) {
                $remainingStatus = 'Today';
            } else {
                $remainingStatus = $today->diffInDays($endDate) . " days left";
            }

            // BUILD DATA
            $data = [
                'id' => $dream->id,
                'name' => $dream->name,
                'start_date' => $dream->start_date,
                'end_date' => $endDate->format('Y-m-d'),
                'frequency' => $dream->frequency,
                'activity_status' => $activityStatus,
                'status' => $dream->status,
                'category' => $dream->category,
                'progress' => $progress,
                'remaining_status' => $remainingStatus,
                'total_units' => $totalActivities,
                'completed_units' => $completedActivities,
            ];

            // CATEGORIZE BY FREQUENCY
            match ($dream->frequency) {
                'Daily'    => $dailyActivities[] = $data,
                'Weekly'   => $weeklyActivities[] = $data,
                'Monthly'  => $monthlyActivities[] = $data,
                'Quarterly'=> $quarterlyActivities[] = $data,
                'Yearly'   => $yearlyActivities[] = $data,
            };

            // Separate completed and upcoming
            if ($today->gt($endDate)) {
                $completedDreams[] = $data;
            } elseif ($today->lt($startDate)) {
                $upcomingDreams[] = $data;
            }
        }

        return response()->json([
            'message' => 'Upcoming dreams fetched successfully.',
            'data'    => [
                'daily_activities'     => $dailyActivities,
                'weekly_activities'    => $weeklyActivities,
                'monthly_activities'   => $monthlyActivities,
                'quarterly_activities' => $quarterlyActivities,
                'yearly_activities'    => $yearlyActivities,
                'completed_dreams'     => $completedDreams,
                'upcoming_dreams'      => $upcomingDreams,
            ]
        ]);
    }
}

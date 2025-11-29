<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class IndexService
{
    use ResponseHelper;

    public function index($request)
    {
        $userId = auth()->id();
        $dreams = Dream::where('user_id', $userId)
            ->whereIn('status', ['Active', 'Pending'])
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

            // ---------------------------------------------------
            // DEFINE END DATE BY FREQUENCY
            // ---------------------------------------------------
            switch ($dream->frequency) {
                case 'Weekly':
                    $endDate = $startDate->copy()->addDays(6);
                    break;

                case 'Monthly':
                    $endDate = $startDate->copy()->addDays(29);
                    break;

                case 'Quarterly':
                    $endDate = $startDate->copy()->addDays(89);
                    break;

                case 'Yearly':
                    $endDate = $startDate->copy()->addDays(364);
                    break;

                default: // Daily
                    $endDate = $dream->end_date
                        ? Carbon::parse($dream->end_date)
                        : $startDate->copy()->addDays(30);
                    break;
            }

            $period = CarbonPeriod::create($startDate, '1 day', $endDate);

            // ---------------------------------------------------
            // FETCH USER ACTIVITY RECORDS
            // ---------------------------------------------------
            $activities = DreamActivity::where('user_id', $userId)
                ->where('dream_id', $dream->id)
                ->where('type', $dream->frequency)
                ->pluck('created_at')
                ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
                ->toArray();

            // ---------------------------------------------------
            // FILL ACTIVITY STATUS
            // ---------------------------------------------------
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');

                if ($date->gt($today)) {
                    $activityStatus[$key] = 'locked';
                } else {
                    $activityStatus[$key] = in_array($key, $activities);
                    if ($activityStatus[$key]) {
                        $completedActivities++;
                    }
                }

                $totalActivities++;
            }

            // ---------------------------------------------------
            // CALCULATE PROGRESS
            // ---------------------------------------------------
            switch ($dream->frequency) {
                case 'Weekly':
                    $required = $dream->per_week ?? 7;
                    break;

                case 'Monthly':
                    $required = $dream->per_month ?? 30;
                    break;

                case 'Quarterly':
                    $required = $dream->per_quarter ?? 90;
                    break;

                case 'Yearly':
                    $required = $dream->per_year ?? 365;
                    break;

                default: // Daily
                    $required = $totalActivities;
                    break;
            }

            $progress = $required > 0
                ? round(($completedActivities / $required) * 100, 2)
                : 0;

            // ---------------------------------------------------
            // REMAINING STATUS
            // ---------------------------------------------------
            if ($today->lt($startDate)) {
                $remainingStatus = 'Upcoming';
            } elseif ($today->gt($endDate)) {
                $remainingStatus = 'Completed';
            } elseif ($today->eq($endDate)) {
                $remainingStatus = 'Today';
            } else {
                $remainingStatus = $today->diffInDays($endDate) . " days left";
            }

            // ---------------------------------------------------
            // FORMAT OUTPUT
            // ---------------------------------------------------
            $data = [
                'id' => $dream->id,
                'name' => $dream->name,
                'start_date' => $dream->start_date,
                'end_date' => $endDate->format('Y-m-d'),
                'frequency' => $dream->frequency,
                'from' => $dream->from,
                'to' => $dream->to,
                'activity_status' => $activityStatus,
                'status' => $dream->status,
                'progress' => $progress,
                'remaining_status' => $remainingStatus,
                'total_units' => $totalActivities,
                'completed_units' => $completedActivities,
            ];

            // ---------------------------------------------------
            // CATEGORIZE BY FREQUENCY
            // ---------------------------------------------------
            match ($dream->frequency) {
                'Daily' => $dailyActivities[] = $data,
                'Weekly' => $weeklyActivities[] = $data,
                'Monthly' => $monthlyActivities[] = $data,
                'Quarterly' => $quarterlyActivities[] = $data,
                'Yearly' => $yearlyActivities[] = $data,
            };

            // COMPLETED & UPCOMING
            if ($today->gt($endDate)) {
                $completedDreams[] = $data;
            } elseif ($today->lt($startDate)) {
                $upcomingDreams[] = $data;
            }
        }

        // ---------------------------------------------------
        // FINAL RESPONSE
        // ---------------------------------------------------
        return response()->json([
            'message' => 'Dreams and activities fetched successfully.',
            'data' => [
                'daily_activities' => $dailyActivities,
                'weekly_activities' => $weeklyActivities,
                'monthly_activities' => $monthlyActivities,
                'quarterly_activities' => $quarterlyActivities,
                'yearly_activities' => $yearlyActivities,
                'completed_dreams' => $completedDreams,
                'upcoming_dreams' => $upcomingDreams,
            ]
        ]);
    }
}

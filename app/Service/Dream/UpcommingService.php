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
        $dreams = Dream::where('user_id', $userId)->where('status','Upcoming')->get();

        $dailyActivities = [];
        $weeklyActivities = [];
        $monthlyActivities = [];
        $completedDreams = [];
        $upcomingDreams = [];

        $today = Carbon::today();

        foreach ($dreams as $dream) {
            $activityStatus = [];
            $completedActivities = 0;
            $totalActivities = 0;

            $startDate = Carbon::parse($dream->start_date);

            // Set end_date based on frequency
            if ($dream->frequency === 'Weekly') {
                $endDate = $startDate->copy()->addDays(6); // total 7 days
            } elseif ($dream->frequency === 'Monthly') {
                $endDate = $startDate->copy()->addDays(30); // total 30 days
            } else { // Daily
                $endDate = $dream->end_date ? Carbon::parse($dream->end_date) : $startDate->copy()->addDays(30);
            }

            $interval = '1 day';
            $period = CarbonPeriod::create($startDate, $interval, $endDate);

            // Fetch user activity records
            $activities = DreamActivity::where('user_id', $userId)
                ->where('dream_id', $dream->id)
                ->where('type', $dream->frequency)
                ->pluck('created_at')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->toArray();

            // Activity status tracking
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                if ($date->gt($today)) {
                    $activityStatus[$key] = 'locked';
                } else {
                    $activityStatus[$key] = in_array($key, $activities);
                    if ($activityStatus[$key]) $completedActivities++;
                }
                $totalActivities++;
            }

            $progress = $totalActivities ? round(($completedActivities / $totalActivities) * 100, 2) : 0;

            // Determine remaining status
            if ($today->lt($startDate)) {
                $remainingStatus = 'Upcoming';
            } elseif ($today->gt($endDate)) {
                $remainingStatus = 'Completed';
            } elseif ($today->eq($endDate)) {
                $remainingStatus = 'Today';
            } else {
                $remainingStatus = $today->diffInDays($endDate) . " days left";
            }

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
                'category' => $dream->category,
                'progress' => $progress,
                'remaining_status' => $remainingStatus,
                'total_units' => $totalActivities,
                'completed_units' => $completedActivities,
            ];

            // Categorize by frequency
            match ($dream->frequency) {
                'Daily' => $dailyActivities[] = $data,
                'Weekly' => $weeklyActivities[] = $data,
                'Monthly' => $monthlyActivities[] = $data,
            };

            // Separate completed and upcoming
            if ($today->gt($endDate)) {
                $completedDreams[] = $data;
            } elseif ($today->lt($startDate)) {
                $upcomingDreams[] = $data;
            }
        }

        return response()->json([
            'message' => 'Dreams and activities fetched successfully.',
            'data' => [
                'daily_activities' => $dailyActivities,
                'weekly_activities' => $weeklyActivities,
                'monthly_activities' => $monthlyActivities,
                'completed_dreams' => $completedDreams,
                'upcoming_dreams' => $upcomingDreams,
            ]
        ]);
    }
}

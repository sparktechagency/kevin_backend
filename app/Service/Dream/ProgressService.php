<?php

namespace App\Service\Dream;

use App\Models\Category;
use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ProgressService
{
    use ResponseHelper;

    public function dreamProgress()
    {
        $userId = auth()->id();
        $today = Carbon::today();

        // Fetch all user's dreams with related category and user
        $dreams = Dream::with(['user', 'category'])
            ->where('user_id', $userId)
            ->get();

        $dreamData = [];

        foreach ($dreams as $dream) {
            $startDate = Carbon::parse($dream->start_date);

            // Set end_date based on frequency
            $endDate = match ($dream->frequency) {
                'Daily' => $dream->end_date ? Carbon::parse($dream->end_date) : $startDate->copy()->addDays(30),
                'Weekly' => $startDate->copy()->addDays(6),
                'Monthly' => $startDate->copy()->addDays(30),
                'Quarterly' => $startDate->copy()->addDays(90),
                'Yearly' => $startDate->copy()->addDays(365),
                default => $dream->end_date ? Carbon::parse($dream->end_date) : $startDate->copy()->addDays(30),
            };

            // Create the date range
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            $totalUnits = iterator_count($period);

            // Fetch all activity dates for this dream
            $activities = DreamActivity::where('user_id', $userId)
                ->where('dream_id', $dream->id)
                ->where('type', $dream->frequency)
                ->pluck('created_at')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->toArray();

            // Track completed units
            $completedUnits = count(array_intersect($activities, collect($period)->map->format('Y-m-d')->toArray()));

            // Progress calculation based on frequency
            $requiredUnits = match ($dream->frequency) {
                'Daily' => $totalUnits,
                'Weekly' => $dream->per_week ?? 7,
                'Monthly' => $dream->per_month ?? 30,
                'Quarterly' => $dream->per_quarter ?? 90,
                'Yearly' => $dream->per_year ?? 365,
                default => $totalUnits,
            };

            $progress = $requiredUnits > 0 ? round(($completedUnits / $requiredUnits) * 100, 2) : 0;

            // Activity status mapping
            $activityStatus = [];
            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                if ($date->gt($today)) {
                    $activityStatus[$dateKey] = 'locked';
                } else {
                    $activityStatus[$dateKey] = in_array($dateKey, $activities);
                }
            }

            // Remaining status
            $remainingStatus = match(true) {
                $today->lt($startDate) => 'Upcoming',
                $today->gt($endDate) => 'Completed',
                $today->eq($endDate) => 'Today',
                default => $today->diffInDays($endDate) . " days left",
            };

            // Calculate streak
            $streak = 0;
            $currentDate = $today->copy();
            while ($currentDate->gte($startDate)) {
                if (in_array($currentDate->format('Y-m-d'), $activities)) {
                    $streak++;
                    $currentDate->subDay();
                } else {
                    break;
                }
            }

            $dreamData[] = [
                'id' => $dream->id,
                'name' => $dream->name,
                'description' => $dream->description,
                'category' => $dream->category->name ?? null,
                'user' => $dream->user,
                'status' => $dream->status,
                'frequency' => $dream->frequency,
                'from' => $dream->from,
                'to' => $dream->to,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'activity_status' => $activityStatus,
                'total_units' => $totalUnits,
                'completed_units' => $completedUnits,
                'progress' => $progress,
                'remaining_status' => $remainingStatus,
                'streak_days' => $streak,
            ];
        }

        // Category-wise dream counts
        $categories = Category::withCount(['dreams' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->get();

        // Overall dream progress
        $totalDreams = $dreams->count();
        $completed = $dreams->where('status', 'Completed')->count();
        $dream_progress = $totalDreams > 0 ? round(($completed / $totalDreams) * 100, 2) : 0;

        $data = [
            'total_dream' => $totalDreams,
            'completed' => $completed,
            'progress' => $dream_progress,
        ];

        return $this->successResponse([
            'dream_progress' => $data,
            'categories' => $categories,
            'dreams' => $dreamData,
        ], 'Dreams and streak data fetched successfully.');
    }
}

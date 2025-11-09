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

        // Fetch dreams with user and category
        $dreams = Dream::with(['user', 'category'])
            ->where('user_id', $userId)
            ->get();

        $dreamData = [];

        foreach ($dreams as $dream) {
            $startDate = Carbon::parse($dream->start_date);

            // Set end_date based on frequency
            if ($dream->frequency === 'Weekly') {
                $endDate = $startDate->copy()->addDays(6);
            } elseif ($dream->frequency === 'Monthly') {
                $endDate = $startDate->copy()->addDays(30);
            } else { // Daily
                $endDate = $dream->end_date ? Carbon::parse($dream->end_date) : $startDate->copy()->addDays(30);
            }

            // Create the date range
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            $totalActivities = iterator_count($period);

            // Fetch user activities
            $activities = DreamActivity::where('user_id', $userId)
                ->where('dream_id', $dream->id)
                ->where('type', $dream->frequency)
                ->orderBy('created_at', 'desc')
                ->pluck('created_at')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->toArray();

            $completedActivities = count(array_intersect($activities, collect($period)->map->format('Y-m-d')->toArray()));
            $progress = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 2) : 0;

            // Remaining status
            if ($today->lt($startDate)) {
                $remainingStatus = 'Upcoming';
            } elseif ($today->gt($endDate)) {
                $remainingStatus = 'Completed';
            } elseif ($today->eq($endDate)) {
                $remainingStatus = 'Today';
            } else {
                $remainingStatus = $today->diffInDays($endDate) . " days left";
            }

            // --- Calculate Streak for this Dream ---
            $streak = 0;
            $currentDate = Carbon::today();

            while (true) {
                $hasActivity = DreamActivity::where('user_id', $userId)
                    ->where('dream_id', $dream->id)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->exists();

                if ($hasActivity) {
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
                'progress' => $progress,
                'streak_days' => $streak,
                'remaining_status' => $remainingStatus,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'frequency' => $dream->frequency,
            ];
        }

        // Categories with dream counts
        $categories = Category::withCount(['dreams' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->get();

        // Overall dream progress
        $totalDreams = $dreams->count();
        $completed = $dreams->where('status', 'completed')->count();
        $dream_progress = $totalDreams > 0 ? round(($completed / $totalDreams) * 100, 2) : 0;

        $data = [
            'total_dream' => $totalDreams,
            'completed' => $completed,
            'progress' => $dream_progress
        ];

        return $this->successResponse([
            'dream_progress' => $data,
            'categories' => $categories,
            'dreams' => $dreamData
        ], 'Dreams and streak data fetched successfully.');
    }
}

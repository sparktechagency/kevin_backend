<?php

namespace App\Service\Dream;

use App\Models\Category;
use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BoostService
{
    use ResponseHelper;

    public function productivityBoost($request)
    {
        $userId = auth()->id();
        $today = Carbon::today();
        $type = $request->input('type', 'weekly'); // default weekly

        $chartData = collect();

        if ($type === 'weekly') {
            $start = $today->copy()->startOfWeek(Carbon::SATURDAY);
            $end = $today->copy()->endOfWeek(Carbon::FRIDAY);
            $period = CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $score = DreamActivity::where('user_id', $userId)
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('log_checkin_in');

                $chartData->push([
                    'day' => $date->format('l'),
                    'date' => $date->format('Y-m-d'),
                    'score' => (int)$score
                ]);
            }

            $chartName = 'weekly_chart';

        } elseif ($type === 'monthly') {
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->endOfMonth();
            $period = CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $score = DreamActivity::where('user_id', $userId)
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('log_checkin_in');

                $chartData->push([
                    'day' => $date->format('l'),
                    'date' => $date->format('Y-m-d'),
                    'score' => (int)$score
                ]);
            }

            $chartName = 'monthly_chart';
        } else {
            return $this->errorResponse('Invalid type. Use "weekly" or "monthly".', 400);
        }

        // ---- CATEGORY PROGRESS ----
        $categories = Category::with(['dreams' => fn($q) => $q->where('user_id', $userId)])->get();
        $categoryProgress = $categories->map(function ($category) {
            $totalDreams = $category->dreams->count();
            $completed = $category->dreams->where('status', 'Completed')->count();
            $progressPercent = $totalDreams > 0 ? round(($completed / $totalDreams) * 100, 2) : 0;
            return [
                'category' => $category->name,
                'total_dreams' => $totalDreams,
                'completed_dreams' => $completed,
                'ongoing_dreams' => $totalDreams - $completed,
                'progress_percent' => $progressPercent
            ];
        });

        // ---- OVERALL PROGRESS ----
        $overallDreams = Dream::where('user_id', $userId)->count();
        $overallCompleted = Dream::where('user_id', $userId)->where('status', 'Completed')->count();
        $overallProgress = $overallDreams ? round(($overallCompleted / $overallDreams) * 100, 2) : 0;

        // ---- STREAK CALCULATION ----
        $streak = 0;
        $currentDate = $today->copy();
        while (DreamActivity::where('user_id', $userId)
            ->whereDate('created_at', $currentDate->toDateString())
            ->exists()) {
            $streak++;
            $currentDate->subDay();
        }

        return $this->successResponse([
            $chartName => $chartData,
            'category_progress' => $categoryProgress,
            'overall' => [
                'total_dreams' => $overallDreams,
                'completed_dreams' => $overallCompleted,
                'progress_percent' => $overallProgress,
                'streak_days' => $streak
            ]
        ], ucfirst($type) . ' productivity, category progress, and streak retrieved successfully.');
    }
}

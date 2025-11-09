<?php

namespace App\Service\User;

use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;
use Illuminate\Support\Carbon;

class MyProfileService
{
   use ResponseHelper;
   public function myProfile()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        // Day Streak — consecutive days with check-ins
        $dayStreakDates = DreamActivity::where('user_id', $user->id)
            ->where('type', 'Daily')
            ->where('log_checkin_in', '>', 0)
            ->whereDate('created_at', '<=', $today)
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->toArray();
        $streak = 0;
        $currentDay = $today;
        foreach ($dayStreakDates as $date) {
            if (Carbon::parse($date)->toDateString() == $currentDay->toDateString()) {
                $streak++;
                $currentDay->subDay();
            } else {
                break;
            }
        }
        // Dreams Done
        $dreamsDone = Dream::where('user_id', $user->id)
            ->where('status', 'Completed')
            ->count();
        // Career Top Focus
        $topFocus = DreamActivity::where('user_id', $user->id)
            ->select('dream_id')
            ->selectRaw('SUM(log_checkin_in) as total')
            ->groupBy('dream_id')
            ->orderByDesc('total')
            ->with('dream')
            ->first();
        $careerFocus = $topFocus?->dream?->name ?? null;
        // Achievements
        $achievements = [
            [
                'name' => 'First Dream',
                'earned' => $dreamsDone > 0,
            ],
            [
                'name' => '7-Day Streak',
                'earned' => $streak >= 7,
            ],
            [
                'name' => 'Master',
                'earned' => $dreamsDone >= 10, // example condition
            ],
        ];
        // Perfect Month Progress (daily check-ins this month)
        $daysWithCheckins = DreamActivity::where('user_id', $user->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('log_checkin_in', '>', 0)
            ->distinct()
            ->selectRaw('DATE(created_at) as date')
            ->count();
        $daysInMonth = $today->daysInMonth;
        $perfectMonthProgress = round(($daysWithCheckins / $daysInMonth) * 100, 1);
        return $this->successResponse([
            'user'=> $user,
            'day_streak' => $streak,
            'dreams_done' => $dreamsDone,
            'career_top_focus' => $careerFocus,
            'achievements' => $achievements,
            'next_reward' => [
                'title' => 'Perfect Month',
                'progress_percent' => $perfectMonthProgress,
            ],
        ], 'Profile data retrieved successfully.');
    }
}

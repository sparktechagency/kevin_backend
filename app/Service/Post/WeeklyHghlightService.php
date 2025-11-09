<?php

namespace App\Service\Post;

use App\Models\DreamActivity;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WeeklyHghlightService
{
    use ResponseHelper;

    public function weeklyHghlight($request)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        // Most Consistent Dreamer
        $mostConsistentUserId = DreamActivity::where('type', 'Weekly')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select('user_id', DB::raw('SUM(log_checkin_in) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->value('user_id');
        $mostConsistent = $this->getUserDetails($mostConsistentUserId);
        // Biggest Growth
        $lastWeekStart = $startOfWeek->copy()->subWeek();
        $lastWeekEnd = $endOfWeek->copy()->subWeek();
        $lastWeekTotals = DreamActivity::where('type', 'Weekly')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->select('user_id', DB::raw('SUM(log_checkin_in) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');
        $currentWeekTotals = DreamActivity::where('type', 'Weekly')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select('user_id', DB::raw('SUM(log_checkin_in) as total'))
            ->groupBy('user_id')
            ->get();
        $biggestGrowthUserId = null;
        $maxGrowth = 0;
        foreach ($currentWeekTotals as $week) {
            $growth = $week->total - ($lastWeekTotals[$week->user_id] ?? 0);
            if ($growth > $maxGrowth) {
                $maxGrowth = $growth;
                $biggestGrowthUserId = $week->user_id;
            }
        }
        $biggestGrowth = $this->getUserDetails($biggestGrowthUserId);
        // Top Encourager
        $topEncouragerUserId = DreamActivity::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->value('user_id');
        $topEncourager = $this->getUserDetails($topEncouragerUserId);
        $users = [
            'most_consistent' => $mostConsistent,
            'biggest_growth' => $biggestGrowth,
            'top_encourager' => $topEncourager,
        ];
        return $this->successResponse($users, 'Weekly highlight users retrieved successfully.');
    }
    private function getUserDetails($userId)
    {
        if (!$userId) {
            return null;
        }
        $user = User::find($userId);
        if (!$user) {
            return null;
        }
        return [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? null,
        ];
    }
}

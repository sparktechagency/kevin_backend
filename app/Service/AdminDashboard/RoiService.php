<?php

namespace App\Service\AdminDashboard;

use App\Models\Category;
use App\Models\Dream;
use App\Models\DreamActivity;
use App\Models\User;
use App\Traits\ResponseHelper;
use Carbon\Carbon;

class RoiService
{
    use ResponseHelper;

    /**
     * Main ROI method
     */
    public function roi($request)
    {
        // 1️⃣ Total Active Employees
        $totalEmployees = $this->getActiveEmployeesCount();

        // 2️⃣ Dream Overall Stats
        $totalDreams = Dream::count();
        $completedDreams = Dream::where('status', 'Completed')->count();
        $avgCompletedRate = $totalDreams > 0
            ? round(($completedDreams / $totalDreams) * 100, 2)
            : 0;

        // 3️⃣ Engagement Score
        $totalCheckins = DreamActivity::sum('log_checkin_in');
        $engagementScore = $totalEmployees > 0
            ? round($totalCheckins / $totalEmployees, 2)
            : 0;

        // 4️⃣ ROI Impact
        $roiImpact = $totalDreams > 0
            ? round($completedDreams / $totalDreams, 2)
            : 0;

        // 5️⃣ Category Insights
        $categoryData = $this->getCategoryInsights();

        // 6️⃣ ROI Trend (Jan–Dec)
        $roiTrend = $this->getRoiTrend();

        // 7️⃣ Employee Impact Leaderboard
        $leaderboard = $this->getLeaderboard();

        // Final Response
        return $this->success([
            'summary' => [
                'total_employees'      => $totalEmployees,
                'total_dreams'         => $totalDreams,
                'completed_dreams'     => $completedDreams,
                'avg_completion_rate'  => $avgCompletedRate,
                'engagement_score'     => $engagementScore,
                'roi_impact'           => $roiImpact,
            ],
            'categories'  => $categoryData,
            'roi_trend'   => $roiTrend,
            'leaderboard' => $leaderboard,
        ]);
    }

    /**
     * Get total active employees (not banned)
     */
    private function getActiveEmployeesCount(): int
    {
        return User::where('is_banned', 0)->count();
    }

    /**
     * Category Insights
     */
    private function getCategoryInsights()
    {
        $categories = Category::with(['dreams', 'dreams.activities'])->get();

        return $categories->map(function ($cat) {
            $total = $cat->dreams->count();
            $completed = $cat->dreams->where('status', 'Completed')->count();
            $engagement = $cat->dreams->sum(fn($dream) =>
                $dream->activities->sum('log_checkin_in')
            );

            return [
                'category_id'   => $cat->id,
                'category_name' => $cat->name,
                'total_dreams'  => $total,
                'completed'     => $completed,
                'engagement'    => $engagement,
                'roi_score'     => $total > 0 ? round($completed / $total, 2) : 0,
            ];
        });
    }

    /**
     * ROI Trend (Jan–Dec)
     */
    private function getRoiTrend(): array
    {
        $roiTrend = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyCompleted = Dream::whereMonth('updated_at', $m)
                ->whereYear('updated_at', date('Y'))
                ->where('status', 'Completed')
                ->count();

            $monthlyEngagement = DreamActivity::whereMonth('updated_at', $m)
                ->sum('log_checkin_in');

            $monthlyTotalDreams = Dream::whereMonth('created_at', $m)->count();

            $roiTrend[] = [
                'month'      => Carbon::create()->month($m)->format('F'),
                'completed'  => $monthlyCompleted,
                'engagement' => $monthlyEngagement,
                'roi_score'  => $monthlyTotalDreams > 0
                    ? round($monthlyCompleted / $monthlyTotalDreams, 2)
                    : 0,
            ];
        }

        return $roiTrend;
    }

    /**
     * Employee Impact Leaderboard
     */
    private function getLeaderboard()
    {
        return User::with(['dreams.activities'])
            ->take(20)
            ->get()
            ->map(function ($user) {
                $totalDreams = $user->dreams->count();
                $completedDreams = $user->dreams->where('status', 'Completed')->count();
                $engagement = $user->dreams->sum(fn($dream) =>
                    $dream->activities->sum('log_checkin_in')
                );

                return [
                    'employee_id' => $user->id,
                    'name'        => $user->name,
                    'role'        => $user->role ?? 'Employee',
                    'total_goals' => $totalDreams,
                    'completed'   => $completedDreams,
                    'engagement'  => $engagement,
                    'roi_impact'  => $totalDreams > 0
                        ? round($completedDreams / $totalDreams, 2)
                        : 0,
                ];
            })
            ->sortByDesc('roi_impact')
            ->values();
    }
}

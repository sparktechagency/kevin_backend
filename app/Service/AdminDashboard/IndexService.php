<?php

namespace App\Service\AdminDashboard;

use App\Models\Category;
use App\Models\Department;
use App\Models\Dream;
use App\Models\ManagerUser;
use App\Models\User;
use App\Traits\ResponseHelper;
use Carbon\Carbon;

class IndexService
{
    use ResponseHelper;

   public function index($request)
    {
        $monthFilter = $request->month; // month = 1–12

        // === TOP SUMMARY CARDS ===
        $activeEmployee = User::where('is_banned', 0)
            ->where('role', 'EMPLOYEE')
            ->count();

        $goalCompletingRate = Dream::where('status', 'Completed')->count();

        $engagementScore = Dream::where('status', 'Active')->count();

        $managerActivity = User::where('is_banned', 0)
            ->where('role', 'MANAGER')
            ->count();

        // === CATEGORY-WISE COMPLETED DREAMS (MONTHLY CHART) ===
        $categoryStats = Category::withCount([
            'dreams as completed_count' => function ($q) use ($monthFilter) {
                $q->where('status', 'Completed');

                if (!empty($monthFilter)) {
                    $q->whereMonth('created_at', $monthFilter)
                    ->whereYear('created_at', now()->year);
                } else {
                    $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                }
            }
        ])
        ->get()
        ->map(fn($item) => [
            'label' => $item->name,
            'value' => $item->completed_count,
        ]);

        // === COMPLETED DREAMS LAST 24 HOURS ===
        $recentCompletedDreams = Dream::where('status', 'Completed')
            ->where('updated_at', '>=', Carbon::now()->subDay())
            ->with('user')
            ->get();

        // === DEPARTMENT-WISE EMPLOYEE COUNT ===
        $departments = Department::withCount([
            'managerUsers as employee_count' => function ($q) use ($monthFilter) {
                $q->join('users', 'manager_users.user_id', '=', 'users.id')
                    ->where('users.role', 'EMPLOYEE')
                    ->where('users.is_banned', 0)
                    ->where('manager_users.status', 'Active');

                if (!empty($monthFilter)) {
                    $q->whereMonth('manager_users.created_at', $monthFilter)
                    ->whereYear('manager_users.created_at', now()->year);
                }
            }
        ])
        ->get(['id', 'name']);

        $departmentStats = $departments->map(function ($item) {
            return [
                'name' => $item->name,
                'value'           => $item->employee_count,
            ];
        });

        // === TOP 5 DEPARTMENTS ===
        $topDepartments = $departments
            ->sortByDesc('employee_count')
            ->take(5)
            ->values()
            ->map(fn($item) => [
                'name' => $item->name,
                'value'           => $item->employee_count,
            ]);

        // === TOP 5 CATEGORIES ===
        $topCategories = Category::withCount([
            'dreams as completed_count' => function ($q) use ($monthFilter) {
                $q->where('status', 'Completed');

                if (!empty($monthFilter)) {
                    $q->whereMonth('created_at', $monthFilter)
                    ->whereYear('created_at', now()->year);
                } else {
                    $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                }
            }
        ])
        ->orderByDesc('completed_count')
        ->take(5)
        ->get()
        ->map(fn($item) => [
            'category_name' => $item->name,
            'value'         => $item->completed_count,
        ]);

        // === FINAL DATA RESPONSE ===
        $data = [
            'summary' => [
                'active_employee'      => $activeEmployee,
                'goal_completion_rate' => $goalCompletingRate,
                'engagement_score'     => $engagementScore,
                'manager_activity'     => $managerActivity,
            ],
            'top_5_categories'          => $topCategories,
            'top_5_departments'         => $topDepartments,
            'department_employee_stats' => $departmentStats,

        ];

        return $this->successResponse($data, 'Dashboard data fetched successfully.');
    }
}

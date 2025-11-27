<?php

namespace App\Service\AdminDashboard;

use App\Models\Department;
use App\Models\ManagerUser;
use App\Models\Category;
use App\Models\Dream;
use App\Traits\ResponseHelper;
use Illuminate\Support\Carbon;

class AnalyticsService
{
    use ResponseHelper;

    public function analytics($request)
    {
        $year = now()->year;
        $month = $request->month ? intval($request->month) : now()->month;

        // -----------------------------
        // 1. Top 3 Departments by Total User Count (YEARLY)
        // -----------------------------
        $departments = Department::withCount([
            'managerUsers as total_user_count' => function ($q) use ($year) {
                $q->join('users', 'manager_users.user_id', '=', 'users.id')
                  ->where('users.role', 'EMPLOYEE')
                  ->where('users.is_banned', 0)
                  ->where('manager_users.status', 'Active')
                  ->whereYear('manager_users.created_at', $year);
            }
        ])
        ->orderByDesc('total_user_count')
        ->take(3)
        ->get(['id', 'name']);

        $departmentData = [];
        foreach ($departments as $dept) {
            $count = $dept->managerUsers()
                ->join('users', 'manager_users.user_id', '=', 'users.id')
                ->where('users.role', 'EMPLOYEE')
                ->where('users.is_banned', 0)
                ->where('manager_users.status', 'Active')
                ->whereYear('manager_users.created_at', $year)
                ->count();

            $departmentData[] = [
                'department_name' => $dept->name,
                'value' => $count,
            ];
        }

        // -----------------------------
        // 2. Manager Impact Analysis (MONTHLY FILTER)
        // -----------------------------
        $managers = ManagerUser::with('manager')
            ->select('user_id', 'manager_id')
            ->distinct()
            ->get();

        $managerImpactAnalysis = [];
        foreach ($managers as $manager) {
            $createdCount = Dream::where('user_id', $manager->user_id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $completedCount = Dream::where('user_id', $manager->user_id)
                ->where('status', 'Completed')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $totalUsersManaged = ManagerUser::where('manager_id', $manager->manager_id)
                ->join('users', 'manager_users.user_id', '=', 'users.id')
                ->where('users.role', 'EMPLOYEE')
                ->where('users.is_banned', 0)
                ->where('manager_users.status', 'Active')
                ->whereYear('manager_users.created_at', $year)
                ->whereMonth('manager_users.created_at', $month)
                ->count();

            $managerImpactAnalysis[] = [
                'manager_name' => $manager->manager->name ?? 'Unknown',
                'manager_email' => $manager->manager->email ?? null,
                'manager_avatar' => $manager->manager->avatar ?? null,
                'dream_created' => $createdCount,
                'dream_completed' => $completedCount,
                'manager_user_count' => $totalUsersManaged,
                'progress' => $createdCount > 0 ? round(($completedCount / $createdCount) * 100, 2) : 0,
            ];
        }

        // -----------------------------
        // 3. Goal Completed by Category (MONTHLY FILTER)
        // -----------------------------
        $categories = Category::all();
        $goalCompletedByCategory = [];

        foreach ($categories as $category) {
            $createdCount = Dream::where('category_id', $category->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $completedCount = Dream::where('category_id', $category->id)
                ->where('status', 'Completed')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $goalCompletedByCategory[] = [
                'category_name' => $category->name,
                'category_icon' => $category->icon,
                'dream_created' => $createdCount,
                'dream_completed' => $completedCount,
                'progress' => $createdCount > 0 ? round(($completedCount / $createdCount) * 100, 2) : 0,
            ];
        }

        // -----------------------------
        // FINAL RESPONSE
        // -----------------------------
        return response()->json([
            'status' => true,
            'month' => Carbon::create()->month($month)->format('F'),
            'departments' => $departmentData,
            'managerImpactAnalysis' => $managerImpactAnalysis,
            'goalCompletedByCategory' => $goalCompletedByCategory,
        ]);
    }
}

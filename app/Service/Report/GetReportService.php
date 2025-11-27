<?php

namespace App\Service\Report;

use App\Models\Dream;
use App\Models\DreamActivity;
use App\Models\ManagerUser;
use App\Models\User;
use App\Models\Report;
use App\Traits\ResponseHelper;

class GetReportService
{
    use ResponseHelper;

    public function getReport()
    {
        $reports = Report::all()->map(function ($report) {

            // Decode metrics JSON
            $metrics = $report->metrics ? json_decode($report->metrics, true) : [];

            // Get all users related to this report
            $users = $this->getUsers($report->manager_id, $report->department_id);

            // Prepare final metric results
            $metricOutput = [];

            foreach ($metrics as $metric) {
                switch ($metric) {

                    case 'engagement_score':
                        $metricOutput['engagement_score'] = $this->metricEngagementScore($users);
                        break;

                    case 'goal_completion':
                        $metricOutput['goal_completion'] = $this->metricGoalCompletion($users);
                        break;

                    case 'user_activity':
                        $metricOutput['user_activity'] = $this->metricUserActivity($users);
                        break;

                    case 'goal_category':
                        $metricOutput['goal_category'] = $this->metricGoalCategory($users);
                        break;

                    case 'department_metrics':
                        $metricOutput['department_metrics'] = $this->metricDepartmentMetrics($users);
                        break;

                    case 'manager_impact':
                        $metricOutput['manager_impact'] = $this->metricManagerImpact($users, $report->manager_id);
                        break;

                    case 'user_retention':
                        $metricOutput['user_retention'] = $this->metricUserRetention($users);
                        break;

                    case 'roi_metrics':
                        $metricOutput['roi_metrics'] = $this->metricROI($users);
                        break;

                }
            }

            $report->metrics = $metricOutput;

            return $report;
        });

        return $this->successResponse($reports, "Report generated successfully.");
    }


    // ----------------------------------------------------------------
    //                  COMMON USER FETCHING
    // ----------------------------------------------------------------
    private function getUsers($managerId, $departmentId)
    {
        // Get users for manager
        if (!empty($managerId)) {
            return ManagerUser::where('manager_id', $managerId)
                ->where('status', 'Active')
                ->pluck('user_id');
        }

        // Get users for department
        if (!empty($departmentId)) {
            return User::where('department_id', $departmentId)
                ->where('role', 'EMPLOYEE')
                ->pluck('id');
        }

        return collect([]);
    }


    // ----------------------------------------------------------------
    //                  METRIC METHODS
    // ----------------------------------------------------------------

    // 1️⃣ Engagement Score
    private function metricEngagementScore($users)
    {
        return DreamActivity::whereIn('user_id', $users)->count();
    }

    // 2️⃣ Goal Completion
    private function metricGoalCompletion($users)
    {
        return Dream::whereIn('user_id', $users)
            ->where('status', 'Completed')
            ->count();
    }

    // 3️⃣ User Activity Distribution
    private function metricUserActivity($users)
    {
        return DreamActivity::whereIn('user_id', $users)
            ->selectRaw('user_id, SUM(log_checkin_in) as total_log_checkin_in, COUNT(*) as entries')
            ->groupBy('user_id')
            ->get();
    }

    // 4️⃣ Goal Category Breakdown
    private function metricGoalCategory($users)
    {
        return Dream::whereIn('user_id', $users)
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->get();
    }

    // 5️⃣ Department Metrics
    private function metricDepartmentMetrics($users)
    {
        return [
            'total_dreams' => Dream::whereIn('user_id', $users)->count(),
            'completed' => Dream::whereIn('user_id', $users)->where('status', 'Completed')->count(),
            'active_activities' => DreamActivity::whereIn('user_id', $users)->distinct('user_id')->count(),
        ];
    }

    // 6️⃣ Manager Impact
    private function metricManagerImpact($users, $managerId)
    {
        return [
            'manager_id' => $managerId,
            'total_users' => $users->count(),
            'completed_goals' => Dream::whereIn('user_id', $users)->where('status', 'Completed')->count(),
            'engagement_score' => DreamActivity::whereIn('user_id', $users)->count(),
        ];
    }

    // 7️⃣ User Retention
    private function metricUserRetention($users)
    {
        $active_users = DreamActivity::whereIn('user_id', $users)
            ->distinct('user_id')
            ->count();

        return [
            'active_users' => $active_users,
            'total_users' => $users->count(),
            'retention_rate' => $users->count() > 0
                ? round(($active_users / $users->count()) * 100, 2)
                : 0
        ];
    }

    // 8️⃣ ROI Metrics
    private function metricROI($users)
    {
        $total = Dream::whereIn('user_id', $users)->count();
        $completed = Dream::whereIn('user_id', $users)->where('status', 'Completed')->count();

        return [
            'total_goals' => $total,
            'completed' => $completed,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}

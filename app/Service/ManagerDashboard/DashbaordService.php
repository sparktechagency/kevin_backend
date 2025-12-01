<?php

namespace App\Service\ManagerDashboard;

use App\Models\User;
use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;

class DashbaordService
{
    use ResponseHelper;

    public function dashboard($request)
    {
        // Load all members with their dreams and dream activities
        $teamMembers = User::where('role', 'USER')
            ->with(['dreams.activities'])
            ->get();

        $teamData = [];
        $totalMembers = $teamMembers->count();
        $activeEngagedCount = 0;
        $totalProgress = 0;

        foreach ($teamMembers as $member) {
            $dreams = $member->dreams; // Already eager loaded

            $progressSum = 0;
            $dreamCount = $dreams->count();

            foreach ($dreams as $dream) {
                // Get the activity of the corresponding member
                $activity = $dream->activities->firstWhere('user_id', $member->id);

                $checkins = $activity ? $activity->log_checkin_in : 0;

                // Safe goal conversion
                $goalArray = is_array($dream->goal) ? $dream->goal : json_decode($dream->goal, true);
                $goalSum = $goalArray ? array_sum($goalArray) : 0;

                // Prevent divide by zero
                $goal = $goalSum > 0 ? $goalSum : 1;

                // Calculate progress %
                $progress = min(100, ($checkins / $goal) * 100);
                $progressSum += $progress;
            }

            // Average progress per user
            $avgProgress = $dreamCount > 0 ? ($progressSum / $dreamCount) : 0;
            $totalProgress += $avgProgress;

            // Engagement level
            $engagement = $avgProgress >= 75 ? 'High'
                        : ($avgProgress >= 50 ? 'Pending'
                        : 'Low');

            if ($engagement === 'High') {
                $activeEngagedCount++;
            }

            // KPI Status
            $kpiStatus = $avgProgress >= 75 ? 'Exceeds'
                        : ($avgProgress >= 50 ? 'Meet'
                        : 'Needs Improvement');

            $teamData[] = [
                'id'            => $member->id,
                'member'       => $member->name,
                'goal_progress'=> round($avgProgress, 2),
                'engagement'   => $engagement,
                'kpi_status'   => $kpiStatus,
            ];
        }

        // Team engagement %
        $teamEngagementPercent = $totalMembers > 0
            ? round(($activeEngagedCount / $totalMembers) * 100)
            : 0;

        // Dream progress %
        $dreamProgress = $totalMembers > 0
            ? round($totalProgress / $totalMembers)
            : 0;

        // At-risk employees (Low engagement)
        $atRiskEmployees = count(
            array_filter($teamData, fn($d) => $d['engagement'] === 'Low')
        );

        // Placeholder ROI
        $teamROI = '+12%'; // total created dream  and dreamactivities. come

        return $this->successResponse([
            'team_engagement'    => $teamEngagementPercent,
            'dream_progress'     => $dreamProgress,
            'at_risk_employees'  => $atRiskEmployees,
            'team_roi'           => $teamROI,
            'team_members'       => $teamData
        ]);
    }
}

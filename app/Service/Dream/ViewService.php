<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ViewService
{
    use ResponseHelper;

    public function view($id)
    {
        $userId = auth()->id();
        $dream = Dream::with('category')->where('id', $id)->first();

        if (!$dream) {
            return $this->errorResponse("Dream not found.");
        }

        $today = Carbon::today();
        $startDate = Carbon::parse($dream->start_date);

        // -----------------------------------
        // END DATE CALCULATION (UPDATED)
        // -----------------------------------
        $endDate = match ($dream->frequency) {
            'Weekly'     => $startDate->copy()->addDays(6),
            'Monthly'    => $startDate->copy()->addDays(30),
            'Quarterly'  => $startDate->copy()->addDays(89),
            'Yearly'     => $startDate->copy()->addDays(364),
            default      => $dream->end_date ? Carbon::parse($dream->end_date) : $startDate->copy()->addDays(30),
        };

        $period = CarbonPeriod::create($startDate, '1 day', $endDate);

        // Fetch completed activity dates
        $activities = DreamActivity::where('dream_id', $dream->id)
            ->where('type', $dream->frequency)
            ->pluck('created_at')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $activityStatus = [];
        $completedUnits = 0;
        $totalUnits = 0;

        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');

            if ($date->gt($today)) {
                $activityStatus[$dateKey] = "locked";
            } else {
                $done = in_array($dateKey, $activities);
                $activityStatus[$dateKey] = $done;
                if ($done) $completedUnits++;
            }

            $totalUnits++;
        }

        // -----------------------------------
        // PROGRESS CALCULATION (UPDATED)
        // -----------------------------------
        if ($dream->frequency === 'Weekly') {
            $required = $dream->per_week ?? 7;
            $progress = $required > 0 ? round(($completedUnits / $required) * 100, 2) : 0;

        } elseif ($dream->frequency === 'Monthly') {
            $required = $dream->per_month ?? 30;
            $progress = $required > 0 ? round(($completedUnits / $required) * 100, 2) : 0;

        } elseif ($dream->frequency === 'Quarterly') {
            $required = $dream->per_quarter ?? 90;
            $progress = $required > 0 ? round(($completedUnits / $required) * 100, 2) : 0;

        } elseif ($dream->frequency === 'Yearly') {
            $required = $dream->per_year ?? 365;
            $progress = $required > 0 ? round(($completedUnits / $required) * 100, 2) : 0;

        } else { // Daily
            $progress = $totalUnits > 0 ? round(($completedUnits / $totalUnits) * 100, 2) : 0;
        }

        // Remaining status
        if ($today->lt($startDate)) {
            $remainingStatus = "Upcoming";
        } elseif ($today->gt($endDate)) {
            $remainingStatus = "Completed";
        } else {
            $remainingStatus = $today->diffInDays($endDate) . " days left";
        }

        // Streak calculation
        $streakDays = 0;
        $checkDate = $today->copy();
        while ($checkDate->gte($startDate)) {
            if (in_array($checkDate->format('Y-m-d'), $activities)) {
                $streakDays++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        $data = [
            'id' => $dream->id,
            'name' => $dream->name,
            'description' => $dream->description,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'frequency' => $dream->frequency,
            'goal' => json_decode($dream->goal),
            'category' => $dream->category,
            'activity_status' => $activityStatus,
            'status' => $dream->status,
            'progress' => $progress,
            'remaining_status' => $remainingStatus,
            'total_units' => $totalUnits,
            'completed_units' => $completedUnits,
            'streak_days' => $streakDays,
        ];

        return $this->successResponse($data, "Dream fetched successfully.");
    }
}

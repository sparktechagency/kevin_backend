<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Models\DreamActivity;
use App\Traits\ResponseHelper;
use Illuminate\Support\Carbon;

class CheckinService
{
    use ResponseHelper;

    public function checkIn($id)
    {
        $dream = Dream::find($id);
        if (!$dream) {
            return $this->errorResponse("Dream not found.");
        }

        $userId = auth()->id();
        $currentDate = Carbon::now();
        $existingCheckIn = DreamActivity::where('user_id', $userId)
            ->where('dream_id', $id)
            ->latest()
            ->first();

        switch ($dream->frequency) {
            case 'Daily':
                return $this->handleDailyCheckIn($dream, $existingCheckIn, $currentDate, $userId);

            case 'Weekly':
                return $this->handleWeeklyCheckIn($dream, $existingCheckIn, $currentDate, $userId);

            case 'Monthly':
                return $this->handleMonthlyCheckIn($dream, $existingCheckIn, $currentDate, $userId);

            default:
                return $this->errorResponse('Invalid dream frequency.');
        }
    }

    private function handleDailyCheckIn($dream, $existingCheckIn, $currentDate, $userId)
    {
        if ($dream->status) {
            return $this->successResponse($dream, "You already completed the dream.");
        }

        $startDate = Carbon::parse($dream->start_date);
        $endDate = Carbon::parse($dream->end_date);

        if (!$currentDate->between($startDate, $endDate)) {
            return $this->errorResponse("The dream is not within the valid date range.");
        }

        $currentTime = $currentDate->format('H:i');
        if ($currentTime < $dream->from || $currentTime > $dream->to) {
            return $this->errorResponse("You cannot check-in outside the allowed time range.");
        }

        DreamActivity::create([
            'user_id' => $userId,
            'dream_id' => $dream->id,
            'type' => 'Daily',
            'log_checkin_in' => 1,
        ]);

        // Mark dream as completed if end date passed
        if ($currentDate->greaterThanOrEqualTo($endDate)) {
            $dream->status = true;
            $dream->save();
        }

        return $this->successResponse([], 'Successfully checked in to the dream.');
    }

    private function handleWeeklyCheckIn($dream, $existingCheckIn, $currentDate, $userId)
    {
        if ($dream->status) {
            return $this->successResponse($dream, "You already completed the dream.");
        }

        $weekStart = Carbon::parse($dream->start_date);
        $weekEnd = $weekStart->copy()->addDays(7);

        if (!$currentDate->between($weekStart, $weekEnd)) {
            return $this->errorResponse("This check-in is not within the valid weekly range.");
        }

        $weeklyCount = DreamActivity::where('user_id', $userId)
            ->where('dream_id', $dream->id)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        if ($weeklyCount >= $dream->per_week) {
            $dream->status = true;
            $dream->save();
            return $this->successResponse([], "You have successfully completed this weekly dream.");
        }

        DreamActivity::create([
            'user_id' => $userId,
            'dream_id' => $dream->id,
            'type' => 'Weekly',
            'log_checkin_in' => 1,
        ]);

        return $this->successResponse([], 'Successfully checked in to the dream.');
    }

    private function handleMonthlyCheckIn($dream, $existingCheckIn, $currentDate, $userId)
    {
        if ($dream->status) {
            return $this->successResponse($dream, "You already completed the dream.");
        }

        $monthStart = Carbon::parse($dream->start_date);
        $monthEnd = $monthStart->copy()->addDays(30);

        if (!$currentDate->between($monthStart, $monthEnd)) {
            return $this->errorResponse("This check-in is not within the valid monthly range.");
        }

        $monthlyCount = DreamActivity::where('user_id', $userId)
            ->where('dream_id', $dream->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        if ($monthlyCount >= $dream->per_month) {
            $dream->status = true;
            $dream->save();
            return $this->successResponse([], "You have successfully completed this monthly dream.");
        }

        DreamActivity::create([
            'user_id' => $userId,
            'dream_id' => $dream->id,
            'type' => 'Monthly',
            'log_checkin_in' => 1,
        ]);

        return $this->successResponse([], 'Successfully checked in to the dream.');
    }
}

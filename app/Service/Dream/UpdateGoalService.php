<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Traits\ResponseHelper;

class UpdateGoalService
{
   use ResponseHelper;
   public function updateGoal($request, $dream_id)
   {
        // $dream = Dream::find($dream_id);
        // $dream->goal = $request->goal
   }
}

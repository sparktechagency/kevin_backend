<?php

namespace App\Service\ManagerDashboard;

use App\Models\GoalGenerate;
use App\Traits\ResponseHelper;

class GoalGenerateService
{
    use ResponseHelper;

    public function goalGenerate($data)
    {
        // Automatically set the user who is assigning the goal
        $data['assign_by'] = auth()->id();

        // Create the goal
        $goal = GoalGenerate::create($data);

        // Return a success response
        return $this->successResponse($goal, 'Goal generated successfully.');
    }
}

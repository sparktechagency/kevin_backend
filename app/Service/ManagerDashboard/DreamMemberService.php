<?php

namespace App\Service\ManagerDashboard;

use App\Models\GoalGenerate;
use App\Traits\ResponseHelper;

class DreamMemberService
{
    use ResponseHelper;

     public function dreamMember($request)
    {
        // Build the query with relations
        $query = GoalGenerate::with([
            'employee:id,name,email,avatar', // only select necessary fields
            'mentor:id,name,email,avatar'
        ]);

        // Apply search across goal and related user names
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('goal_name', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mentor', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Optional pagination
        $perPage = $request->get('per_page', 10);
        $goals = $query->paginate($perPage);

        return $this->successResponse($goals, 'Goals fetched successfully with relation data.');
    }
}

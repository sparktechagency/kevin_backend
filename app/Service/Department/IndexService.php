<?php

namespace App\Service\Department;

use App\Models\Department;
use App\Traits\ResponseHelper;

class IndexService
{
     use ResponseHelper;

    public function index()
    {
        $departments = Department::where('user_id',auth()->id())->orderBy('created_at', 'desc')->get();

        if ($departments->isEmpty()) {
            return $this->successResponse([], "No departments found.");
        }

        return $this->successResponse($departments, "Departments retrieved successfully.");
    }
}

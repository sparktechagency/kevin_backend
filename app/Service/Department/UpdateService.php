<?php

namespace App\Service\Department;

use App\Models\Department;
use App\Traits\ResponseHelper;

class UpdateService
{
   use ResponseHelper;
    public function update($data,$id)
    {
        $department = Department::find($id);

        if (!$department) {
            return $this->errorResponse("Department not found", [], 404);
        }
        $data['user_id'] = auth()->id();
        $department->update($data);
        return $this->successResponse($department, "Department updated successfully.");
    }
}

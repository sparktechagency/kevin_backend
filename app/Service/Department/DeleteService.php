<?php

namespace App\Service\Department;

use App\Models\Department;
use App\Traits\ResponseHelper;

class DeleteService
{
       use ResponseHelper;
    public function delete(int $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return $this->errorResponse("Department not found", [], 404);
        }

        $department->delete();

        return $this->successResponse([], "Department deleted successfully.");
    }
}

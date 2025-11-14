<?php

namespace App\Service\Department;

use App\Models\Department;
use App\Models\ManagerUser;
use App\Traits\ResponseHelper;

class StoreService
{
   use ResponseHelper;
   public function store($data)
   {
        $data['user_id'] = auth()->id();
        $department = Department::create($data);
        return $this->successResponse($department,"User added successfully.");
   }
}

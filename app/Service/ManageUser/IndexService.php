<?php

namespace App\Service\ManageUser;

use App\Models\ManagerUser;
use App\Traits\ResponseHelper;

class IndexService
{
    use  ResponseHelper;
    public function index($request)
    {
        $users = ManagerUser::with(['user','manager:id,name,email','department:id,name'])->orderBy('id', 'desc')
                    ->paginate($request->per_page ?? 10);

        return $this->successResponse($users, 'Users retrieved successfully.');
    }


}

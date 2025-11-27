<?php

namespace App\Service\ManageUser;

use App\Models\ManagerUser;
use App\Traits\ResponseHelper;

class DeleteService
{
   use ResponseHelper;

    public function destroy($id)
    {
        $managerUser = ManagerUser::find($id);

        if (!$managerUser) {
            return $this->errorResponse('User not found.');
        }

        $managerUser->delete();

        return $this->successResponse([], 'User deleted successfully.');
    }

}

<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Traits\ResponseHelper;

class ViewService
{
   use ResponseHelper;

   public function view($id)
    {
        $dream = Dream::with(['user:id,name,email,avatar','category:id,name,icon'])->find($id);
        if(!$dream){
            return $this->errorResponse("Dream not found.");
        }
        return $this->successResponse($dream, 'Dream view successfully.');
    }
}

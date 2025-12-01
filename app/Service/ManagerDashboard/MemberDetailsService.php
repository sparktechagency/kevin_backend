<?php

namespace App\Service\ManagerDashboard;

use App\Models\User;
use App\Traits\ResponseHelper;

class MemberDetailsService
{
   use ResponseHelper;
   public function  memberDetails($id)
   {
        $user = User::find($id);
        
   }
}

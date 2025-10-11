<?php

namespace App\Service\Auth;

use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    use ResponseHelper;
    public function getProfile()
    {
        $user = Auth::user();
        return $this->successResponse($user,"Profile retrived successful.");
    }
}

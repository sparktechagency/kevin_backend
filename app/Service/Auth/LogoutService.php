<?php

namespace App\Service\Auth;

use App\Traits\ResponseHelper;

class LogoutService
{
    use ResponseHelper;
    public function logout()
    {
        auth()->logout(); // invalidate token
        return $this->successResponse([],"Successfully logged out.");
    }
}

<?php

namespace App\Service\Auth;

use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class LogoutService
{
    use ResponseHelper;
     public function logout()
    {

        $user = Auth::user();

            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete(); // Delete current token
            }

            return $this->successResponse([], "Successfully logged out.");

    }
}

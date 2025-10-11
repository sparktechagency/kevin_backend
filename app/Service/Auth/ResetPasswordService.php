<?php

namespace App\Service\Auth;

use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetPasswordService
{
    use ResponseHelper;
    public function resetPassword(array $data)
    {
        $user= Auth::user();
         if (!Hash::check($data['current_password'], $user->password)) {
            return $this->errorResponse("The current password is incorrect.");
        }
        $user->password = Hash::make($data['password']);
        $user->save();
        return $this->successResponse($user,"Password reset successfully.");
    }
}

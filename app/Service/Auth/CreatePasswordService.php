<?php

namespace App\Service\Auth;

use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreatePasswordService
{
     use ResponseHelper;
    public function createPassword(array $data)
    {
        $user= Auth::user();
        $user->password = Hash::make($data['password']);
        $user->save();
        return $this->successResponse($user,"Password update successfully.");
    }
}

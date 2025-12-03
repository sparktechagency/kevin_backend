<?php

namespace App\Service\Auth;

use App\Models\User;
use App\Traits\ResponseHelper;

class EmployeeLoginService
{
   use ResponseHelper;
   public function employeeLogin($data)
   {
        $user = User::where('employee_pin', $data->pin)->first();
        if (!$user) {
            return $this->errorResponse('User PIN is not correct.');
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->successResponse([
            'user'  => $user,
            'token' => $token,
        ], 'Login successful');
    }
}

<?php

namespace App\Service\Auth;

use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Hash;

class LoginService
{
   use ResponseHelper;
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        if (!Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Invalid Password.', 401);
        }
         if (!$user->email_verified_at) {
            return $this->errorResponse('Your email is not verified. Please verify your email to login.');
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->successResponse([
            'user'  => $user,
            'token' => $token,
        ], 'Login successful');
    }
}

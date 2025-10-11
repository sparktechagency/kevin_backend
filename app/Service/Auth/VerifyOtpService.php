<?php

namespace App\Service\Auth;

use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Redis;

class VerifyOtpService
{
    use ResponseHelper;
    public function verifyOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return $this->errorResponse("User not found.", 404);
        }
        $userId = $user->id;
        $otp = $data['otp'];
        $storedOtp = Redis::get('otp_' . $userId);
        if (!$storedOtp || $storedOtp != $otp) {
            return $this->errorResponse('Invalid or expired OTP. Please request a new OTP.', 400);
        }
        $user->email_verified_at = now();
        $user->save();
        Redis::del('otp_' . $userId);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            ['user' => $user, 'token' => $token],
            'OTP verified successfully.'
        );
    }
}

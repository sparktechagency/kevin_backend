<?php

namespace App\Service\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class ResendOtpService
{
   use ResponseHelper;

    public function resendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        $otp = rand(100000, 999999);
        Redis::setex('otp_' . $user->id, 600, $otp);
        $otpInfo = [
            'otp' => $otp,
            'name' => $user->name,
        ];
        Mail::to($user->email)->queue(new OtpMail($otpInfo));
        return $this->successResponse([], 'OTP has been resent to your email.');
    }
}

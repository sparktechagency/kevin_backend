<?php

namespace App\Service\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class RegisterService
{
    use ResponseHelper;
    public function register(array $data)
    {
        $user = User::create($data);
        $otp = rand(100000, 999999);
        $user['otp'] =$otp;
        Redis::setex('otp_' . $user->id, 600, $otp);
        $opt_info= [
            'otp'=> $otp,
            'name'=> $user->name,
        ];
        Mail::to($user->email)->queue(new OtpMail($opt_info));
        return $this->successResponse($user,"Registered successfully, check your email for OTP.");
    }
}

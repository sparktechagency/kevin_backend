<?php

namespace App\Service\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use App\Service\Notification\NotificationService;
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
         $admins = User::where('role', 'ADMIN')->get();
        $notificationData = [
            'name' => 'New User Registered',
            'message' => $user->name . ' has just registered.',
            'type' => $user->role ?? 'USER'
        ];
        $notificationService = new NotificationService();
        $notificationService->send($admins, $notificationData);
        return $this->successResponse($user,"Registered successfully, check your email for OTP.");
    }
}

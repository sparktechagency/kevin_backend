<?php

namespace App\Service\User;

use App\Mail\SupportMail;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportRequestService
{
   use ResponseHelper;
   public function supportRequest($data)
   {
      $adminEmail = env('MAIL_USERNAME');

        if (!$adminEmail) {
             return $this->errorResponse('Mail configuration error: MAIL_USERNAME not set.');
        }
        $user = Auth::user();
        $data['name']=$user->name;
        $data['email']=$user->email;
        $data['contact_number']=$user->contact_number;
        $data['employee_pin']=$user->employee_pin;
        Mail::to($adminEmail)->queue(new SupportMail($data));
        return $this->successResponse([], 'Support request sent successfully.');
   }
}

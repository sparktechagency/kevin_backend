<?php

namespace App\Service\Company;

use App\Mail\WelcomeEmail;
use App\Models\Company;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class StoreService
{
   use ResponseHelper;

   public function store($data)
    {
        if (isset($data['company_logo'])) {
            $logoPath = $data['company_logo']->store('company_logos', 'public');
            $data['company_logo'] = 'storage/' . $logoPath;
        }
        // return $data['password'];
        $company = Company::create([
            'company_name'=>$data['company_name'],
            'company_email'=>$data['company_email'],
            'company_phone'=>$data['company_phone'],
            'company_address'=>$data['company_address'],
            'manager_full_name'=>$data['manager_full_name'],
            'manager_email'=>$data['manager_email'],
            'manager_phone'=>$data['manager_phone'],
            'send_welcome_email'=>$data['send_welcome_email'],
            'manager_code'=>$data['password'],
        ]);
        if($company){
            User::create([
                'name' =>$data['manager_full_name'],
                'email' =>$data['manager_email'],
                'contact_number' =>$data['manager_phone'],
                'password' =>Hash::make($data['password']),
                'email_verified_at'=> now(),
                'role' =>'MANAGER',
            ]);
        }
        if (!empty($data['send_welcome_email']) && $data['send_welcome_email'] == true) {
            Mail::to($company->company_email)->queue(new WelcomeEmail($company));
        }

        return $this->successResponse($company, 'Company created successfully.');
    }
}

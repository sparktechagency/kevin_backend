<?php

namespace App\Service\Company;

use App\Mail\WelcomeEmail;
use App\Models\Company;
use App\Models\User;
use App\Traits\ResponseHelper;
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
        $company = Company::create($data);
        if($company){
            User::create([
                'name' =>$data['manager_full_name'],
                'email' =>$data['manager_email'],
                'contact_number' =>$data['manager_phone'],
                'employee_pin' =>$data['manager_code'],
                'role' =>'MANAGER',
            ]);
        }
        if (!empty($data['send_welcome_email']) && $data['send_welcome_email'] == true) {
            Mail::to($company->company_email)->queue(new WelcomeEmail($company));
        }
        return $this->successResponse($company, 'Company created successfully.');
    }
}

<?php

namespace App\Service\Company;

use App\Models\Company;
use App\Models\User;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UpdateService
{
   use ResponseHelper;
    public function update(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {

            $company = Company::find($id);
            if (!$company) {
                return $this->errorResponse('Company not found');
            }

            // ✅ Logo update
            if (!empty($data['company_logo'])) {
                if ($company->company_logo && Storage::exists(str_replace('storage/', 'public/', $company->company_logo))) {
                    Storage::delete(str_replace('storage/', 'public/', $company->company_logo));
                }

                $path = $data['company_logo']->store('company_logos', 'public');
                $data['company_logo'] = 'storage/' . $path;
            }

            // ✅ Company update
            $company->update([
                'company_name' => $data['company_name'],
                'company_email' => $data['company_email'],
                'company_phone' => $data['company_phone'],
                'company_address' => $data['company_address'] ?? null,
                'company_logo' => $data['company_logo'] ?? $company->company_logo,
            ]);

            // ✅ Manager (User) update
            $user = User::where('email', $company->manager_email)->first();
            if ($user) {
                $user->name = $data['manager_full_name'];
                $user->email = $data['manager_email'];
                $user->contact_number = $data['manager_phone'];
                $user->email_verified_at = now();

                if (!empty($data['password'])) {
                    $user->password = Hash::make($data['password']);
                }

                $user->save();
            }

            return $this->successResponse($company, 'Company updated successfully');
        });
    }
}

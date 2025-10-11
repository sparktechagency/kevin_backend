<?php

namespace App\Service\Company;

use App\Models\Company;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class UpdateService
{
   use ResponseHelper;
    public function update($data, $id)
    {
        $company = Company::find($id);
        if (!$company) {
          return $this->errorResponse("Company not found.");
        }
        if (isset($data['company_logo'])) {
            if ($company->company_logo && Storage::exists('public/' . $company->company_logo)) {
                Storage::delete('public/' . $company->company_logo);
            }
            $logoPath = $data['company_logo']->store('company_logos', 'public');
            $data['company_logo'] = 'storage/' . $logoPath;
        }
        $company->update($data);
        return $this->successResponse($company,"Company updated successfully.");
    }
}

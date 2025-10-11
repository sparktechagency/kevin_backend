<?php

namespace App\Service\Company;

use App\Models\Company;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class DeleteService
{
   use ResponseHelper;
   public function delete($id)
    {
        $company = Company::find($id);
        if(!$company){
            return $this->errorResponse("Company not found.");
        }
        if ($company->company_logo && Storage::exists('public/' . $company->company_logo)) {
            Storage::delete('public/' . $company->company_logo);
        }
        $company->delete();
        return $this->successResponse([], 'Company delete successfully.');
    }
}

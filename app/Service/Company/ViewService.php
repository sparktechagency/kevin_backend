<?php

namespace App\Service\Company;

use App\Models\Company;
use App\Traits\ResponseHelper;

class ViewService
{
  use ResponseHelper;

   public function view($id)
    {
        $company = Company::find($id);
        if(!$company){
            return $this->errorResponse("Company not found.");
        }
        return $this->successResponse($company, 'Company view successfully.');
    }
}

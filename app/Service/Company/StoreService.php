<?php

namespace App\Service\Company;

use App\Models\Company;
use App\Traits\ResponseHelper;

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
        return $this->successResponse($company, 'Company created successfully.');
    }
}

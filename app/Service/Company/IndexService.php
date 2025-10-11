<?php

namespace App\Service\Company;

use App\Models\Company;
use App\Traits\ResponseHelper;

class IndexService
{
    use ResponseHelper;
    public function index($request)
    {
        $searchTerm = $request->get('search', '');
        $query = Company::query();
        if (!empty($searchTerm)) {
            $query->where('company_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('company_email', 'like', '%' . $searchTerm . '%')
                ->orWhere('company_phone', 'like', '%' . $searchTerm . '%')
                ->orWhere('company_address', 'like', '%' . $searchTerm . '%')
                ->orWhere('manager_full_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('manager_email', 'like', '%' . $searchTerm . '%');
        }
        $companies = $query->orderBy('created_at', 'desc')
                           ->paginate($request->per_page ?? 10);
        return $this->successResponse($companies, 'Search results fetched successfully.');
    }
}

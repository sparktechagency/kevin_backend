<?php

namespace App\Service\PrivacyPolicy;

use App\Models\PrivacyPolicy;
use App\Traits\ResponseHelper;

class IndexService
{
   use ResponseHelper;
    public function index()
    {
        $policy = PrivacyPolicy::first(); // get the first (or only) policy

        if (!$policy) {
            return $this->errorResponse('Privacy policy not found.');
        }

        return $this->successResponse($policy, 'Privacy policy retrieved successfully.');
    }
}

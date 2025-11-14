<?php

namespace App\Service\TermCondition;

use App\Models\TermCondition;
use App\Traits\ResponseHelper;

class IndexService
{
   use ResponseHelper;
    public function index()
    {
        $termCondition = TermCondition::latest()->first();

        if (!$termCondition) {
            return $this->errorResponse('No terms and conditions found.');
        }

        return $this->successResponse(
            $termCondition,
            'Terms and conditions retrieved successfully.'
        );
    }

}

<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Traits\ResponseHelper;

class StoreService
{
   use ResponseHelper;
   public function store($data)
    {
        $data['user_id'] = auth()->id();
        $dream = Dream::create($data);
        return $this->successResponse($dream, 'Dream created successfully.');
    }
}

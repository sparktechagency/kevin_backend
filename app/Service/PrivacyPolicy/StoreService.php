<?php

namespace App\Service\PrivacyPolicy;

use App\Models\PrivacyPolicy;
use App\Traits\ResponseHelper;

class StoreService
{
   use ResponseHelper;
   public function store($data)
   {
        $policy = PrivacyPolicy::updateOrCreate(
            [],
            ['content' => $data['content'] ?? null]
        );

        return $this->successResponse($policy, 'Privacy policy saved successfully.');
   }
}

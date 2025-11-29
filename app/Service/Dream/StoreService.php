<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Models\Subscription;
use App\Traits\ResponseHelper;

class StoreService
{
   use ResponseHelper;
   public function store($data)
    {
        $user = auth()->user();
         $subscription = Subscription::where('user_id',$user->id)->where('status','active')->first();
        $data['user_id'] = auth()->id();
        $dream = Dream::create($data);
        return $this->successResponse($dream, 'Dream created successfully.');
    }
}

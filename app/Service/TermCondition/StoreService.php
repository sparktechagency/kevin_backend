<?php

namespace App\Service\TermCondition;

use App\Models\TermCondition;
use App\Traits\ResponseHelper;

class StoreService
{
   use ResponseHelper;
   public function store($data)
   {
      $termCondition = TermCondition::updateOrCreate($data);
      return $this->successResponse($termCondition, 'Terms and conditions saved successfully.');
   }
}


<?php

namespace App\Service\Subscription;

use App\Models\Plan;
use App\Traits\ResponseHelper;

class UpdatePlanService
{
   use ResponseHelper;
   public function updatePlan( $data, $plan)
    {
        // return $data;
        $plan = Plan::find($plan);
        if (!$plan) {
            return $this->errorResponse("Plan not found.");
        }
        $slug = strtolower(str_replace(' ', '_', $plan->name));
        $data ['stripe_price_id'] = 'price_' . $slug;
        $data['stripe_product_id'] = 'prod_' . $slug;

        $plan->update($data);
        return $this->successResponse($plan, "Plan successfully updated.");
    }
}

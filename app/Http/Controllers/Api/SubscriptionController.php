<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Service\Subscription\CancelService;
use App\Service\Subscription\CheckoutService;
use App\Service\Subscription\PaymentIntentService;
use App\Service\Subscription\PlanService;
use App\Service\Subscription\RemuseService;
use App\Service\Subscription\SuccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $planService;
    protected $checkinService;
    protected $paymentIntentService;
    protected $successService;
    protected $cancelService;
    protected $resumeService;
    public function __construct(
        PlanService $planService,
        CheckoutService $checkoutService,
        PaymentIntentService $paymentIntentService,
        SuccessService $successService,
        CancelService $cancelService,
        RemuseService $remuseService,
    ){
        $this->planService = $planService;
        $this->checkinService = $checkoutService;
        $this->paymentIntentService = $paymentIntentService;
        $this->successService = $successService;
        $this->cancelService = $cancelService;
        $this->resumeService = $remuseService;
    }
    public function plans()
    {
        return $this->execute(function(){
            return $this->planService->plans();
        });
    }
    public function checkout($plan)
    {
        return $this->execute(function()use($plan){
            return $this->checkinService->checkout($plan);
        });
    }
    public function paymentIntent($plan)
    {
        return $this->execute(function()use($plan){

            return $this->paymentIntentService->paymentIntent($plan);
        });
    }
    public function success(Request $request, $plan)
    {
        return $this->execute(function()use($request,$plan){
            return $this->successService->success($request,$plan);
        });
    }
    public function cancelSubscription($plan)
    {
        return $this->execute(function()use($plan){
            return $this->cancelService->cancelSubscription($plan);
        });
    }
    public function resumeSubscription($plan)
    {
       return $this->execute(function()use($plan){
            return $this->resumeService->resumeSubscription($plan);
        });
    }
}

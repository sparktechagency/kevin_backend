<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\RequestSupport;
use App\Service\User\MyProfileService;
use App\Service\User\NotificationStatusService;
use App\Service\User\SupportRequestService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $myProfileService;
    protected $supportRequestService;
    protected $notificationStatusService;
    public function __construct(
        MyProfileService $myProfileService,
        SupportRequestService $supportRequestService,
        NotificationStatusService $notificationStatusService,
    ){
        $this->myProfileService = $myProfileService;
        $this->supportRequestService = $supportRequestService;
        $this->notificationStatusService = $notificationStatusService;
    }
    public function myProfile()
    {
        return $this->execute(function(){
            return $this->myProfileService->myProfile();
        });
    }
    public function supportRequest(RequestSupport $requestSupport)
    {
        return $this->execute(function()use($requestSupport){
            $data = $requestSupport->validated();
            return $this->supportRequestService->supportRequest($data);
        });
    }
    public function notificationStatus(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->notificationStatusService->notificationStatus($request);
        });
    }
}

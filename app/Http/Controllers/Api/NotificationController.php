<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\CreateRequest;
use App\Service\Notification\CreateService;
use App\Service\Notification\DestroyService;
use App\Service\Notification\GetNotificationService;
use App\Service\Notification\IndexService;
use App\Service\Notification\MarkAllReadService;
use App\Service\Notification\MarkAsReadService;
use App\Service\Notification\UpdateService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $indexService;
    protected $createService;
    protected $updateService;
    protected $readService;
    protected $readallService;
    protected $deleteService;
    protected $getNotificaitonService;
    public function __construct(
        CreateService $createService,
        IndexService $indexService,
        MarkAllReadService $markAllReadService,
        MarkAsReadService $markAsReadService,
        DestroyService $destroyService,
        UpdateService $updateService,
        GetNotificationService $getNotificationService,
    ){
        $this->createService = $createService;
        $this->indexService = $indexService;
        $this->readService = $markAsReadService;
        $this->readallService = $markAllReadService;
        $this->deleteService = $destroyService;
        $this->updateService = $updateService;
        $this->getNotificaitonService = $getNotificationService;
    }
    public function index(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->indexService->index($request);
        });
    }
    public function create(CreateRequest $createRequest)
    {
        return $this->execute(function()use($createRequest){
            $data = $createRequest->validated();
            return $this->createService->create($data);
        });
    }
    public function update(CreateRequest $createRequest,$id)
    {
        return $this->execute(function()use($createRequest,$id){
            $data = $createRequest->validated();
            return $this->updateService->update($data,$id);
        });
    }
    public function destroy($id)
    {
        return $this->execute(function()use($id){
            return $this->deleteService->destroy($id);
        });
    }
    public function getNotification(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->getNotificaitonService->getNotification($request);
        });
    }
    public function markAsRead($id)
    {
        return $this->execute(function()use($id){
            return $this->readService->markAsRead($id);
        });
    }
    public function markAllAsRead()
    {
        return $this->execute(function(){
            return $this->readallService->markAllAsRead();
        });
    }
}

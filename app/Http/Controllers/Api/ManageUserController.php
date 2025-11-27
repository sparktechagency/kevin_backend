<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManageUser\ManageUserRequst;
use App\Http\Requests\ManageUser\UpdateManageUserRequest;
use App\Service\ManageUser\ViewService;
use App\Service\ManageUser\DeleteService;
use App\Service\ManageUser\StoreService;
use App\Service\ManageUser\UpdateService;
use App\Service\ManageUser\IndexService;
use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    protected $indexService;
    protected $storeService;
    protected $updateService;
    protected $deleteService;
    protected $viewService;
    public function __construct(
        IndexService $indexService,
        StoreService $storeService,
        UpdateService $updateService,
        DeleteService $deleteService,
        ViewService $viewService,
    ){
        $this->indexService = $indexService;
        $this->storeService = $storeService;
        $this->updateService = $updateService;
        $this->deleteService = $deleteService;
        $this->viewService = $viewService;
    }
    public function index(Request $request)
    {
        return $this->execute(function() use ($request) {
            return $this->indexService->index($request);
        });
    }
    public function store(ManageUserRequst $manageUserRequst)
    {
        return $this->execute(function()use($manageUserRequst) {
            $data = $manageUserRequst->validated();
            return $this->storeService->store($data);
        });
    }

    public function update(UpdateManageUserRequest $updateManageUserRequest, $id)
    {
        return $this->execute(function() use ($updateManageUserRequest, $id) {
             $data = $updateManageUserRequest->validated();
            return $this->updateService->update($data, $id);
        });
    }
    public function destroy($id)
    {
        return $this->execute(function() use ($id) {
            return $this->deleteService->destroy($id);
        });
    }

}

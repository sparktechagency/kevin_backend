<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\DepartmentRequest;
use App\Service\Department\DeleteService;
use App\Service\Department\IndexService;
use App\Service\Department\StoreService;
use App\Service\Department\UpdateService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $indexService;
    protected $storeService;
    protected $updateService;
    protected $deleteService;
    public function __construct(
        IndexService $indexService,
        StoreService $storeService,
        UpdateService $updateService,
        DeleteService $deleteService,
    ){
        $this->indexService = $indexService;
        $this->storeService = $storeService;
        $this->updateService = $updateService;
        $this->deleteService = $deleteService;
    }
    public function index(Request $request)
    {
        return $this->execute(function() use ($request) {
            return $this->indexService->index($request);
        });
    }
    public function store(DepartmentRequest $createRequest)
    {
        return $this->execute(function()use($createRequest) {
            $data = $createRequest->validated();
            return $this->storeService->store($data);
        });
    }

    public function update(DepartmentRequest $updateRequest, $id)
    {
        return $this->execute(function() use ($updateRequest, $id) {
             $data = $updateRequest->validated();
            return $this->updateService->update($data, $id);
        });
    }
    public function destroy($id)
    {
        return $this->execute(function() use ($id) {
            return $this->deleteService->delete($id);
        });
    }
}

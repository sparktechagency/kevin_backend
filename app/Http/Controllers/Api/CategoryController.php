<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Service\Category\DeleteService;
use App\Service\Category\IndexService;
use App\Service\Category\StoreService;
use App\Service\Category\UpdateService;
use App\Service\Category\ViewService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $indexService;
    protected $storeService;
    protected $updateService;
    protected $viewService;
    protected $deleteService;
    public function __construct(
        IndexService $indexService,
        StoreService $storeService,
        UpdateService $updateService,
        ViewService $viewService,
        DeleteService $deleteService,
    ){
        $this->indexService = $indexService;
        $this->storeService = $storeService;
        $this->updateService = $updateService;
        $this->viewService = $viewService;
        $this->deleteService = $deleteService;
    }
    public function index(Request $request)
    {
        return $this->execute(function() use ($request) {
            return $this->indexService->index($request);
        });
    }
    public function store(CreateRequest $createRequest)
    {
        return $this->execute(function()use($createRequest) {
            $data = $createRequest->validated();
            return $this->storeService->store($data);
        });
    }

    public function update(UpdateRequest $updateRequest, $id)
    {
        return $this->execute(function() use ($updateRequest, $id) {
             $data = $updateRequest->validated();
            return $this->updateService->update($data, $id);
        });
    }
    public function view($id)
    {
        return $this->execute(function() use ($id) {
            return $this->viewService->view($id);
        });
    }
    public function destroy($id)
    {
        return $this->execute(function() use ($id) {
            return $this->deleteService->delete($id);
        });
    }

}

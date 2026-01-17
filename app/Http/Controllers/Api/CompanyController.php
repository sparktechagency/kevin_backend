<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Service\Company\DeleteService;
use App\Service\Company\IndexService;
use App\Service\Company\StoreService;
use App\Service\Company\UpdateService;
use App\Service\Company\ViewService;
use Illuminate\Http\Request;

class CompanyController extends Controller
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
    public function store(CompanyRequest $companyRequest)
    {
        return $this->execute(function()use($companyRequest) {
             $data = $companyRequest->validated();
            return $this->storeService->store($data);
        });
    }

    public function update(UpdateCompanyRequest $companyRequest, $id)
    {
        return $this->execute(function() use ($companyRequest, $id) {
               $data = $companyRequest->validated();
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


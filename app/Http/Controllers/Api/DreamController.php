<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dream\CreateRequest;
use App\Service\Dream\BoostService;
use App\Service\Dream\CheckinService;
use App\Service\Dream\IndexService;
use App\Service\Dream\ProgressService;
use App\Service\Dream\StoreService;
use App\Service\Dream\ViewService;
use Illuminate\Http\Request;

class DreamController extends Controller
{
     protected $indexService;
    protected $storeService;
    protected $checkinService;
    protected $viewService;
    protected $deleteService;
    protected $progressService;
    protected $boostService;
    public function __construct(
        IndexService $indexService,
        StoreService $storeService,
        ViewService $viewService,
        CheckinService $checkinService,
        ProgressService $progressService,
        BoostService $boostService,
    ){
        $this->indexService = $indexService;
        $this->storeService = $storeService;
        $this->viewService = $viewService;
        $this->checkinService = $checkinService;
        $this->progressService = $progressService;
        $this->boostService = $boostService;
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
    public function view($id)
    {
        return $this->execute(function() use ($id) {
            return $this->viewService->view($id);
        });
    }
     public function checkIn($id)
    {
        return $this->execute(function() use ($id) {
            return $this->checkinService->checkIn($id);
        });
    }
    public function dreamProgress()
    {
         return $this->execute(function() {
            return $this->progressService->dreamProgress();
        });
    }
    public function productivityBoost(Request $request)
    {
         return $this->execute(function()use ($request) {
            return $this->boostService->productivityBoost($request);
        });
    }
}

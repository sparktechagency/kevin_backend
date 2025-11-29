<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dream\CreateRequest;
use App\Service\Dream\AiFeedbackService;
use App\Service\Dream\BoostService;
use App\Service\Dream\CheckinService;
use App\Service\Dream\IndexService;
use App\Service\Dream\ProgressService;
use App\Service\Dream\ReflectionServic;
use App\Service\Dream\SmartSuggestionService;
use App\Service\Dream\StoreService;
use App\Service\Dream\UpcommingService;
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
    protected $upcommingService;
    protected $reflectionServic;
    protected $aiFeedbackService;
    protected $smartSuggestionService;
    public function __construct(
        IndexService $indexService,
        StoreService $storeService,
        ViewService $viewService,
        CheckinService $checkinService,
        ProgressService $progressService,
        BoostService $boostService,
        UpcommingService $upcommingService,
        ReflectionServic $reflectionServic,
        AiFeedbackService $aiFeedbackService,
        SmartSuggestionService $smartSuggestionService,
    ){
        $this->indexService = $indexService;
        $this->storeService = $storeService;
        $this->viewService = $viewService;
        $this->checkinService = $checkinService;
        $this->progressService = $progressService;
        $this->boostService = $boostService;
        $this->upcommingService = $upcommingService;
        $this->reflectionServic = $reflectionServic;
        $this->aiFeedbackService = $aiFeedbackService;
        $this->smartSuggestionService = $smartSuggestionService;
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
    public function upcoming(Request $request)
    {
        return $this->execute(function()use ($request){
            return $this->upcommingService->upcoming($request);
        });
    }
    public function note($dream_id)
    {
        return $this->execute(function()use($dream_id){
            return $this->reflectionServic->note($dream_id);
        });
    }
     public function aiFeedback(Request $request)
    {
        return $this->execute(function()use ($request){
            return $this->aiFeedbackService->aiFeedback($request);
        });
    }
     public function smartSuggestion(Request $request)
    {
        return $this->execute(function()use ($request){
            return $this->smartSuggestionService->smartSuggestion($request);
        });
    }
}

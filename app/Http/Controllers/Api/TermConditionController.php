<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TermCondition\ContentRequest;
use App\Models\TermCondition;
use App\Service\TermCondition\IndexService;
use App\Service\TermCondition\StoreService;
use Illuminate\Http\Request;

class TermConditionController extends Controller
{
    protected $indexService;
    protected $storeService;
    public function __construct(
        IndexService $indexService,
        StoreService $storeService,
    ){
        $this->indexService = $indexService;
        $this->storeService = $storeService;
    }
    public function index()
    {
        return $this->execute(function(){
            return $this->indexService->index();
        });
    }
    public function store(ContentRequest $contentRequest)
    {
        return $this->execute(function()use ($contentRequest){
             $data = $contentRequest->validated();
            return $this->storeService->store($data);
        });
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrivacyPolicy\PolicyRequest;
use App\Service\PrivacyPolicy\IndexService;
use App\Service\PrivacyPolicy\StoreService;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    protected $storeService;
    protected $indexService;

    public function __construct(
        StoreService $storeService,
        IndexService $indexService,
    )
    {
        $this->storeService = $storeService;
        $this->indexService = $indexService;
    }
    public function index()
    {
         return $this->execute(function(){
            return $this->indexService->index();
        });
    }
    public function store(PolicyRequest $policyRequest)
    {
        return $this->execute(function() use($policyRequest){
            $data = $policyRequest->validated();
            return $this->storeService->store($data);
        });
    }
}

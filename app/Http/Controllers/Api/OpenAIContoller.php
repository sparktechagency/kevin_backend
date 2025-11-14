<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coach\CoachRequest;
use App\Service\Coach\ChatHistoryService;
use App\Service\Coach\ChatHistoryViewService;
use App\Service\Coach\IndexService;
use App\Service\Coach\StoreService;
use App\Service\Coach\ViewService;
use Illuminate\Http\Request;

class OpenAIContoller extends Controller
{
    protected $indexService;
    protected $storeService;
    protected $viewService;
    protected $chatHistoryService;
    protected $chatHistoryViewService;

    public function __construct(
        StoreService $storeService,
        ChatHistoryService $chatHistoryService,
        ChatHistoryViewService $chatHistoryViewService,
        IndexService $indexService,
        ViewService $viewService
    ) {
        $this->storeService = $storeService;
        $this->indexService = $indexService;
        $this->viewService = $viewService;
        $this->chatHistoryService = $chatHistoryService;
        $this->chatHistoryViewService = $chatHistoryViewService;
    }
    public function index($chat_id,Request $request)
    {
         return $this->execute(function() use($chat_id, $request){
             return $this->indexService->index($chat_id,$request);
        });
    }
    public function store(CoachRequest $request)
    {
        return $this->execute(function()use($request){
            $data = $request->validated();
            return $this->storeService->store($data);
        });
    }
    public function view($chat_id,$coach_id)
    {
        return $this->execute(function()use($chat_id,$coach_id){
            return $this->viewService->view($chat_id,$coach_id);
        });
    }
    public function history()
    {
        return $this->execute(function(){

            return $this->chatHistoryService->history();
        });
    }
     public function chatHistoryView($chat_id)
    {
        return $this->execute(function()use($chat_id){
            return $this->chatHistoryViewService->chatHistoryView($chat_id);
        });
    }
}

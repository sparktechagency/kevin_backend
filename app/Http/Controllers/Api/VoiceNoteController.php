<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoiceNote\VoiceNoteRequest;
use App\Service\VoiceNote\IndexService;
use App\Service\VoiceNote\StoreService;
use App\Service\VoiceNote\ViewService;
use Illuminate\Http\Request;

class VoiceNoteController extends Controller
{
    protected $storeService;
    protected $indexService;
    protected $viewService;

    public function __construct(
        StoreService $storeService,
        IndexService $indexService,
        ViewService $viewService,
    ) {
        $this->storeService = $storeService;
        $this->indexService = $indexService;
        $this->viewService = $viewService;
    }

    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            return $this->indexService->index($request);
        });
    }
    public function store(VoiceNoteRequest $voiceNoteRequest)
    {
        return $this->execute(function () use ($voiceNoteRequest) {
            $data = $voiceNoteRequest->validated();
            return $this->storeService->store($data);
        });
    }
    public function view($note_id)
    {
        return $this->execute(function () use ($note_id) {
            return $this->viewService->view($note_id);
        });
    }
}


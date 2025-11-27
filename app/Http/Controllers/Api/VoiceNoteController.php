<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoiceNote\VoiceNoteRequest;
use App\Service\VoiceNote\IndexService;
use App\Service\VoiceNote\StoreService;
use App\Service\VoiceNote\ViewService;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

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
    public function voiceToText(Request $request)
    {
        // Validate the uploaded audio
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,m4a',
        ]);

        try {
            $audioFile = $request->file('audio');

            // Call OpenAI Whisper API
            $response = OpenAI::audio()->transcriptions()->create([
                'file' => $audioFile->getRealPath(),
                'model' => 'whisper-1', // Whisper speech-to-text model
            ]);

            return response()->json([
                'success' => true,
                'text' => $response->text ?? '',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error converting audio: ' . $e->getMessage(),
            ]);
        }
    }
}


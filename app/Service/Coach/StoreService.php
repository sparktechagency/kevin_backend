<?php

namespace App\Service\Coach;

use App\Models\Chat;
use App\Models\Coach;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;
class StoreService
{
    use ResponseHelper;
    public function store($data)
    {
        $userId = $data['user_id'] ?? Auth::id();

        if (empty($data['user_message'])) {
            return $this->errorResponse('Message cannot be empty.', [], 422);
        }

        $userMessage = trim($data['user_message']);
        // return $data['chat_id'];
        // Use existing chat if chat_id provided, otherwise create new
        if (!empty($data['chat_id'])) {
            $chat = Chat::find($data['chat_id']);
            if (!$chat) {
                return $this->errorResponse('Chat not found.', [], 404);
            }
        } else {
            // Create new chat
            $chat = Chat::create([
                'user_id' => $userId,
                'title'   => substr($userMessage, 0, 50),
            ]);
        }

        $chatId = $chat->id;

        // Save user's message
        $userMessageRecord = Coach::create([
            'chat_id' => $chatId,
            'user_id' => $userId,
            'message' => $userMessage,
            'sender'  => 'user',
        ]);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-5-nano',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI personal coach. Provide motivational, supportive, and encouraging responses.',
                ],
                [
                    'role' => 'user',
                    'content' => $data['user_message'],
                ],
            ],
        ]);

        $aiResponse = $response->choices[0]->message->content ?? 'No response received.';

        Coach::create([
            'chat_id' => $chatId,
            'user_id' => $userId,
            'message' => $aiResponse,
            'sender'  => 'ai',
        ]);

        return $this->successResponse([
            'chat_id'     => $chatId,
            'user_message'=> $userMessage,
            'ai_response' => $aiResponse,
        ], 'Message processed successfully.');
    }
}

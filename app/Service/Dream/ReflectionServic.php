<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Traits\ResponseHelper;
use OpenAI\Laravel\Facades\OpenAI;

class ReflectionServic
{
   use ResponseHelper;
    public function note($dream_id)
    {
        $dream = Dream::find($dream_id);

        if (!$dream) {
            return $this->errorResponse("Dream not found.");
        }

            $response = OpenAI::chat()->create([
            'model' => 'gpt-5-nano',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI personal coach. Respond in a motivational, supportive way, max 400 characters.'
                ],
                [
                    'role' => 'user',
                    'content' => $dream->name ?? "Hi dream!"
                ],
            ],
        ]);

        $reflections = $response->choices[0]->message->content ?? 'No response received.';

        return $this->successResponse(['reflection' => $reflections]);

    }
}

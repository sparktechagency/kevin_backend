<?php

namespace App\Service\Dream;

use App\Traits\ResponseHelper;
use OpenAI\Laravel\Facades\OpenAI;

class AiFeedbackService
{
    use ResponseHelper;

    public function aiFeedback($request)
    {
        $request->validate([
            'selected_text' => 'required|string',
        ]);

        $selectedText = $request->input('selected_text');

        // Generate AI feedback using GPT-5 Nano
        $response = OpenAI::chat()->create([
            'model' => 'gpt-5-nano',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI personal coach. Provide motivational, supportive, and encouraging responses, no longer than 300 characters.',
                ],
                [
                    'role' => 'user',
                    'content' => $selectedText,
                ],
            ],
        ]);

        // Extract text from response safely
        $feedback = $response->choices[0]->message->content ?? "I'm here to help! Keep going!";

        // Truncate to 300 characters
        $feedback = mb_substr($feedback, 0, 300);

        return $this->successResponse([
            'feedback' => $feedback
        ], 'AI coach feedback generated successfully.');
    }
}

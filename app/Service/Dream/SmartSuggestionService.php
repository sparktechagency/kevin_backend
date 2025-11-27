<?php

namespace App\Service\Dream;

use App\Models\Dream;
use App\Traits\ResponseHelper;
use OpenAI\Laravel\Facades\OpenAI;

class SmartSuggestionService
{
    use ResponseHelper;

    public function smartSuggestion($request)
    {
        // Get latest active dream
        $dream = Dream::where('user_id', auth()->id())
            ->where('status', 'Active')
            ->latest()
            ->first();

        if (!$dream) {
            return $this->errorResponse("No active dream found.");
        }

        // Prompt for optimal study time
        $promptStudy = "
        Dream Name: {$dream->name}
        Dream Description: {$dream->description}

        Based on this dream, suggest the best 1–3 hour daily optimal study/work time.
        Respond motivationally. Max 200 characters.
        ";

        // Prompt for dream spacing
        $promptSpacing = "
        Dream Name: {$dream->name}
        Dream Description: {$dream->description}

        Give a weekly dream spacing plan (how to break the dream into small steps).
        Respond motivationally. Max 200 characters.
        ";

        // AI Call 1 — Optimal Study Time
        $optimalResponse = OpenAI::chat()->create([
            'model' => 'gpt-5-nano',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI personal coach. Respond in a motivational, supportive way, max 200 characters.'
                ],
                [
                    'role' => 'user',
                    'content' => $promptStudy
                ],
            ],
        ]);

        // AI Call 2 — Dream Spacing
        $spacingResponse = OpenAI::chat()->create([
            'model' => 'gpt-5-nano',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI personal coach. Respond in a motivational, supportive way, max 200 characters.'
                ],
                [
                    'role' => 'user',
                    'content' => $promptSpacing
                ],
            ],
        ]);

        $optimalStudy = $optimalResponse['choices'][0]['message']['content'] ?? "";
        $dreamSpacing = $spacingResponse['choices'][0]['message']['content'] ?? "";

        return $this->successResponse([
            'dream_name' => $dream->name,
            'dream_description' => $dream->description,
            'suggestions' => [
                'optimal_study_time' => $optimalStudy,
                'dream_spacing' => $dreamSpacing,
            ]
        ], "Smart suggestions generated successfully.");
    }
}

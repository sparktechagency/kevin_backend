<?php

namespace App\Service\Post;

use App\Models\Post;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class StoreService
{
    use ResponseHelper;

    public function store($data)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->errorResponse('User not found.');
        }

        $photos = [];

        // ✅ Handle photo uploads
        if (!empty($data['photos']) && is_array($data['photos'])) {
            foreach ($data['photos'] as $photo) {
                $path = $photo->store('post/photos', 'public');
                $photos[] = 'storage/' . $path; 
        }

        // ✅ Create post
        $post = Post::create([
            'user_id' => $user->id,
            'content' => $data['content'] ?? null,
            'privacy' => $data['privacy'] ?? 'public',
            'photos' => $photos ? json_encode($photos) : null,
        ]);

        // ✅ Decode photos before sending response
        $post->photos = $photos ?: [];

        return $this->successResponse($post, 'Post created successfully.');
    }
}

}

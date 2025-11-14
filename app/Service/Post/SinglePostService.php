<?php

namespace App\Service\Post;

use App\Models\Post;
use App\Traits\ResponseHelper;

class SinglePostService
{
       use ResponseHelper;

    public function singlePost($postId)
    {
        $user = auth()->user();

        $post = Post::with([
                'user',
                'comments.user',
                'comments.replies.user',
            ])
            ->withCount([
                'comments',
                'likes as like_type_like_count' => fn($q) => $q->where('type', 'like'),
                'likes as like_type_love_count' => fn($q) => $q->where('type', 'love'),
                'likes as like_type_fire_count' => fn($q) => $q->where('type', 'fire'),
            ])
            ->find($postId);

        if (!$post) {
            return $this->errorResponse([], 'Post not found.', 404);
        }
        // Transform post
        $post->photos = $post->photos ? json_decode($post->photos) : [];

        $userLike = $post->likes()
            ->where('user_id', $user->id)
            ->first();

        $post->user_reacted = $userLike ? true : false;
        $post->user_reaction_type = $userLike?->type ?? null;

        return $this->successResponse($post, 'Post retrieved successfully.');
    }
}

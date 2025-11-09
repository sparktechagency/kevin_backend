<?php

namespace App\Service\Post;

use App\Models\Post;
use App\Traits\ResponseHelper;

class IndexService
{
    use ResponseHelper;
    public function index($request)
    {
        $user = auth()->user();
        $perPage = $request->per_page ?? 10;

        $posts = Post::with([
                'user',
                'comments.user',
                'comments.replies.user',
            ])
            ->where('privacy', 'public')
            ->withCount([
                'comments',
                'likes as like_type_like_count' => fn($q) => $q->where('type', 'like'),
                'likes as like_type_love_count' => fn($q) => $q->where('type', 'love'),
                'likes as like_type_fire_count' => fn($q) => $q->where('type', 'fire'),
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $posts->getCollection()->transform(function ($post) use ($user) {
            // Decode photos
            $post->photos = $post->photos ? json_decode($post->photos) : [];

            // Check if the auth user reacted
            $userLike = $post->likes()
                ->where('user_id', $user->id)
                ->first();

            $post->user_reacted = $userLike ? true : false;  // true or false
            $post->user_reaction_type = $userLike?->type ?? null; // like/love/fire or null

            return $post;
        });

        return $this->successResponse($posts, 'Posts retrieved successfully.');
    }

}

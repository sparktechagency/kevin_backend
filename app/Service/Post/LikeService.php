<?php

namespace App\Service\Post;

use App\Models\Like;
use App\Models\Post;
use App\Service\Notification\NotificationService;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class LikeService
{
    use ResponseHelper;

    public function like($request, $post_id)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse("User not found.");
        }

        $post = Post::find($post_id);
        if (!$post) {
            return $this->errorResponse("Post not found.");
        }

        // Allowed reaction types
        $allowedTypes = ['like', 'love', 'fire'];
        $type = $request->type ?? null;

        if (!in_array($type, $allowedTypes)) {
            return $this->errorResponse("Invalid reaction type.");
        }

        // Retrieve or create a Like record
        $like = Like::firstOrNew([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'type'    => $type,
        ]);

        // Toggle status
        $like->status = !$like->status;
        $like->save();

        $message = $like->status ? "Post reacted '{$type}' successfully." : "Reaction '{$type}' removed.";

        // Notify post owner if reaction is new and user is not the post owner
        if ($like->status && $post->user_id != $user->id) {
            $notificationData = [
                'title'   => 'New Reaction',
                'message' => "{$user->name} reacted '{$type}' on your post.",
                'type'    => 'POST_LIKE',
                'post_id' => $post->id,
            ];

            $notificationService = new NotificationService();
            $notificationService->send($post->user, $notificationData); // pass User model
        }

        return $this->successResponse($like, $message);
    }
}

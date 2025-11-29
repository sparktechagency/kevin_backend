<?php

namespace App\Service\Post;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use App\Service\Notification\NotificationService;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class ReplyService
{
    use ResponseHelper;

    public function reply($data, $post_id, $comment_id)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse("User not found.");
        }

        $post = Post::find($post_id);
        if (!$post) {
            return $this->errorResponse("Post not found.");
        }

        $comment = Comment::find($comment_id);
        if (!$comment) {
            return $this->errorResponse("Comment not found.");
        }

        // Handle reply image if provided
        if (!empty($data['image'])) {
            $imagePath = $data['image']->store('reply/images', 'public');
            $data['image'] = 'storage/' . $imagePath;
        }

        // Assign user and comment
        $data['user_id'] = $user->id;
        $data['comment_id'] = $comment_id;

        // Create reply
        $reply = Reply::create($data);

        // Notify the original comment owner (avoid notifying self)
        if ($comment->user_id != $user->id) {
            $notificationData = [
                'title'      => 'New Reply',
                'message'    => "{$user->name} replied to your comment.",
                'type'       => 'COMMENT_REPLY',
                'post_id'    => $post->id,
                'comment_id' => $comment_id,
            ];

            $notificationService = new NotificationService();
            $notificationService->send($comment->user, $notificationData); // pass User model
        }

        return $this->successResponse($reply, "Reply added successfully.");
    }
}

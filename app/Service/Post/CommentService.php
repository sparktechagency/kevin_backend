<?php

namespace App\Service\Post;

use App\Models\Comment;
use App\Models\Post;
use App\Service\Notification\NotificationService;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    use ResponseHelper;

    public function comment($data, $post_id)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse("User not found.");
        }

        $post = Post::find($post_id);
        if (!$post) {
            return $this->errorResponse("Post not found.");
        }

        // Handle comment image if provided
        if (!empty($data['image'])) {
            $image = $data['image'];
            $imagePath = $image->store('comment/images', 'public'); // store publicly
            $data['image'] = 'storage/' . $imagePath;
        }

        // Assign user and post
        $data['user_id'] = $user->id;
        $data['post_id'] = $post_id;

        // Create comment
        $comment = Comment::create($data);

        // Send notification to post owner (avoid notifying self)
        if ($post->user_id != $user->id) {
            $notificationData = [
                'title'   => 'New Comment',
                'message' => "{$user->name} commented on your post.",
                'type'    => 'POST_COMMENT',
                'post_id' => $post->id,
            ];

            $notificationService = new NotificationService();
            $notificationService->send($post->user, $notificationData); // pass User model
        }

        return $this->successResponse($comment, "Comment added successfully.");
    }
}

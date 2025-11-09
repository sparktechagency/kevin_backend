<?php

namespace App\Service\Post;

use App\Models\Comment;
use App\Models\Post;
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
        if (isset($data['image']) && $data['image']) {
            $image = $data['image'];
            $imagePath = $image->store('comment/images');
            $data['image'] = 'storage/' . $imagePath;
        }
        $data['user_id'] = $user->id;
        $data['post_id'] = $post_id;
        $comment = Comment::create($data);
        return $this->successResponse($comment, "Comment added successfully.");
    }
}

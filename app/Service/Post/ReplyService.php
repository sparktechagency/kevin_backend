<?php

namespace App\Service\Post;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class ReplyService
{
     use ResponseHelper;
    public function reply($data,$post_id,$comment_id)
    {
         $user = Auth::user();
        if (!$user) {
            return $this->errorResponse("User not found.");
        }
        $post = Post::find($post_id);
        if (!$post) {
            return $this->errorResponse("Post not found.");
        }
        $commment = Comment::find($comment_id);
         if (!$commment) {
            return $this->errorResponse("Comment not found.");
        }
        if (isset($data['image']) && $data['image']) {
            $image = $data['image'];
            $imagePath = $image->store('reply/images');
            $data['image'] = 'storage/' . $imagePath;
        }
        $data['user_id'] = $user->id;
        $data['comment_id'] = $comment_id;
        $comment = Reply::create($data);
        return $this->successResponse($comment, "Reply comment successfully.");
    }
}

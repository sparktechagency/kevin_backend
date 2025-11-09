<?php

namespace App\Service\Post;

use App\Models\Like;
use App\Models\Post;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class LikeService
{
    use ResponseHelper;
    public function like($request,$post_id)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse("User not found.");
        }
        $post = Post::find($post_id);
        if (!$post) {
            return $this->errorResponse("Post not found.");
        }
        $allowedTypes = ['like', 'love', 'fire'];
        if (!in_array($request->type, $allowedTypes)) {
            return $this->errorResponse("Invalid like type.");
        }
        $like = Like::where('user_id', $user->id)
                    ->where('post_id', $post->id)
                    ->where('type', $request->type)
                    ->first();
        if ($like) {
            if ($like->status == false) {
                $like->status = true;
                $like->save();
                return $this->successResponse($like, "Post liked successfully.");
            } elseif ($like->status == true) {
                $like->status = false;
                $like->save();
                return $this->successResponse($like, "Post unliked successfully.");
            }
        }

        $newLike = Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'type' => $request->type,
            'status' => true,
        ]);
        return $this->successResponse($newLike, "Post liked successfully.");
    }
}

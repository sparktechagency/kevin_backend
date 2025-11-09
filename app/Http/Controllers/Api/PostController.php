<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CommentRequest;
use App\Http\Requests\Post\PostRequest;
use App\Service\Post\CommentService;
use App\Service\Post\IndexService;
use App\Service\Post\LikeService;
use App\Service\Post\ReplyService;
use App\Service\Post\SearchTopicesService;
use App\Service\Post\StoreService;
use App\Service\Post\WeeklyHghlight;
use App\Service\Post\WeeklyHghlightService;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $storeService;
    protected $indexService;
    protected $likeService;
    protected $commentService;
    protected $replyService;
    protected $searchTopicesService;
    protected $weeklyHighlitService;
    public function __construct(
        StoreService $storeService,
        IndexService $indexService,
        LikeService $likeService,
        CommentService $commentService,
        ReplyService $replyService,
        SearchTopicesService $searchTopicesService,
        WeeklyHghlightService $weeklyHghlightService,
    )
    {
        $this->storeService = $storeService;
        $this->indexService = $indexService;
        $this->likeService = $likeService;
        $this->commentService = $commentService;
        $this->replyService = $replyService;
        $this->searchTopicesService = $searchTopicesService;
        $this->weeklyHighlitService = $weeklyHghlightService;
    }
    public function index(Request $request)
    {
        return $this->execute(function() use ($request){
            return $this->indexService->index($request);
        });
    }
    public function store(PostRequest $postRequest)
    {
        return $this->execute(function() use ($postRequest){
            $data = $postRequest->validated();
            return $this->storeService->store($data);
        });
    }
    public function Like(Request $request,$post_id)
    {
        return $this->execute(function() use ($request,$post_id){
            return $this->likeService->like($request,$post_id);
        });
    }
    public function comment(CommentRequest $commentRequest, $post_id)
    {
        return $this->execute(function() use ($commentRequest,$post_id){
            $data = $commentRequest->validated();
            return $this->commentService->comment($data,$post_id);
        });
    }
    public function reply(CommentRequest $commentRequest,$post_id,$comment_id)
    {
        return $this->execute(function()use($commentRequest,$post_id,$comment_id){
            $data = $commentRequest->validated();
            return $this->replyService->reply($data,$post_id,$comment_id);
        });
    }
    public function searchTopices(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->searchTopicesService->searchTopices($request);
        });
    }
    public function weeklyHghlight(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->weeklyHighlitService->weeklyHghlight($request);
        });
    }
}

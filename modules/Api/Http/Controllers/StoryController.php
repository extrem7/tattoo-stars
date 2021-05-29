<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Api\Http\Resources\StoryResource;
use Modules\Api\Repositories\StoryRepository;
use Modules\Api\Services\StoryService;

final class StoryController extends Controller
{
    protected StoryService $service;
    protected StoryRepository $repository;

    public function __construct(StoryService $service, StoryRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @api {get} /stories All stories
     * @apiDescription Latest stories.
     * @apiName IndexStories
     * @apiGroup Stories
     *
     * @apiUse Token
     * @apiSuccess {Object[]} stories Stories collection.
     * @apiSuccess {Number} stories.id Story id.
     * @apiSuccess {Object} stories.post Story post (see IndexPosts).
     * @apiSuccess {Number} stories.rating stories current rating.
     * @apiSuccess {Number} stories.likes stories likes count.
     * @apiSuccess {Number} stories.dislikes stories dislikes count.
     * @apiUse Pagination
     */
    public function index(): JsonResponse
    {
        $stories = $this->repository->getForIndex();

        return response()->json([
            'stories' => StoryResource::collection($stories),
            'hasMorePages' => $stories->hasMorePages()
        ]);
    }

    /**
     * @api {post} /stories/:post Create story
     * @apiName CreateStory
     * @apiGroup Stories
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Is story has been published.
     */
    public function store(Post $post): JsonResponse
    {
        $user = \Auth::user();

        if ($user->stories()->whereDate('stories.created_at', '=', today())->exists()) {
            abort(Response::HTTP_PAYMENT_REQUIRED, __('tattoo.stories.daily_limit'));
        }

        if ($post->user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Вы не можете добавлять чужие публикации.');
        }

        $this->repository->store($post);

        return response()->json(['message' => 'Story has been published.'], 201);
    }

    /**
     * @api {post} /stories/:id/view Mark story as viewed
     * @apiName StoryView
     * @apiGroup Stories
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message View status.
     */
    public function view(Story $story): JsonResponse
    {
        if (\Auth::user()->views()->find($story->id)) {
            abort(Response::HTTP_CONFLICT, 'Вы уже просмотрели эту запись.');
        }

        $this->service->view($story);

        return response()->json(['message' => 'Story has been viewed.']);
    }

    /**
     * @api {post} /stories/:id/like Toggle like
     * @apiName ToggleLike
     * @apiGroup Stories
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle status message.
     * @apiSuccess {Boolean} liked Like status.
     */
    public function like(Story $story): JsonResponse
    {
        $liked = $this->service->toggleLike($story);
        $action = $liked ? 'liked' : 'unliked';

        return response()->json([
            'message' => "Story has been $action.",
            'liked' => $liked
        ]);
    }

    /**
     * @api {post} /stories/:id/dislike Toggle dislike
     * @apiName ToggleDislike
     * @apiGroup Stories
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle status message.
     * @apiSuccess {Boolean} disliked Dislike status.
     */
    public function dislike(Story $story): JsonResponse
    {
        $disliked = $this->service->toggleDislike($story);
        $action = $disliked ? 'disliked' : 'undisliked';

        return response()->json([
            'message' => "Story has been $action.",
            'disliked' => $disliked
        ]);
    }
}

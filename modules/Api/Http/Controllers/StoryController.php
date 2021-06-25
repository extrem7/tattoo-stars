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
     *
     * @apiSuccess {Number} storyBalance Story publications balance.
     * @apiSuccess {Object[]} stories Stories collection.
     * @apiSuccess {Number} stories.id Story id.
     * @apiSuccess {Object} stories.post Story post (see IndexPosts).
     * @apiSuccess {Number} stories.rating Story current rating.
     * @apiSuccess {Number} stories.likes Story likes count.
     * @apiSuccess {Number} stories.dislikes Story dislikes count.
     * @apiSuccess {Enum} stories.likeStatus Story like status: {like|dislike|null}.
     * @apiSuccess {Boolean} stories.viewed Is story was viewed.
     * @apiUse Pagination
     */
    public function index(): JsonResponse
    {
        $stories = $this->repository->getForIndex();

        return response()->json([
            'storyBalance' => \Auth::user()->story_balance,
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

        abort_unless($post->user_id === $user->id, Response::HTTP_FORBIDDEN, 'Вы не можете добавлять чужие публикации.');
        abort_if($user->story_balance < 1, Response::HTTP_PAYMENT_REQUIRED, __('tattoo.stories.daily_limit'));

        if ($this->repository->store($post) !== null) {
            $this->service->makeTranslation($user, -1);
        }

        return response()->json(['message' => 'Story has been published.'], Response::HTTP_CREATED);
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

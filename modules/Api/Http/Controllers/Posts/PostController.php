<?php

/**
 * @apiDefine Posts
 * @apiSuccess {Object[]} posts Posts collection.
 * @apiSuccess {Number} posts.id Post id.
 * @apiSuccess {Number} posts.user_id Post author id.
 * @apiSuccess {Object} posts.user Post author.
 * @apiSuccess {String} posts.user.name Post author name.
 * @apiSuccess {String} posts.user.avatar Post author avatar.
 * @apiSuccess {String} posts.description Post description.
 * @apiSuccess {String[]} posts.images Post images.
 * @apiSuccess {Object} posts.video Post video.
 * @apiSuccess {String} posts.video.thumbnail Video thumbnail.
 * @apiSuccess {String} posts.video.url Video url.
 * @apiSuccess {String} posts.date Post date.
 * @apiSuccess {Number} posts.likes Post likes.
 * @apiSuccess {Boolean} posts.like_status Post like status.
 * @apiSuccess {Boolean} posts.bookmark_status Post bookmark status.
 */

namespace Modules\Api\Http\Controllers\Posts;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Requests\PostRequest;
use Modules\Api\Http\Resources\PostResource;
use Modules\Api\Repositories\PostRepository;
use Modules\Api\Services\PostService;

final class PostController extends Controller
{
    protected PostService $service;
    protected PostRepository $repository;

    public function __construct(PostService $service, PostRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @api {get} /posts All posts
     * @apiDescription Latest posts based on user subscriptions or just latest posts.
     * @apiName IndexPosts
     * @apiGroup Posts
     *
     * @apiUse Token
     * @apiUse Posts
     * @apiUse Pagination
     */
    public function index(): JsonResponse
    {
        $posts = $this->repository->getForIndex(\Auth::user());

        return response()->json([
            'posts' => PostResource::collection($posts),
            'hasMorePages' => $posts->hasMorePages()
        ]);
    }

    /**
     * @api {get} /posts/search Search posts
     * @apiDescription Use #hashtag to search in tags and regular string to search in description.
     * @apiName SearchPosts
     * @apiGroup Posts
     *
     * @apiUse Token
     * @apiUse Search
     * @apiUse Posts
     * @apiUse Pagination
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['query' => ['required', 'string255']]);

        $posts = $this->repository->search($request->input('query'));

        return response()->json([
            'posts' => PostResource::collection($posts),
            'hasMorePages' => $posts->hasMorePages()
        ]);
    }

    /**
     * @api {post} /posts/:id Show post
     * @apiName ShowPost
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiSuccess {Number} posts.id Post id.
     * @apiSuccess {Number} posts.user_id Post author id.
     * @apiSuccess {Object} posts.user Post author.
     * @apiSuccess {String} posts.user.name Post author name.
     * @apiSuccess {String} posts.user.avatar Post author avatar.
     * @apiSuccess {String} posts.description Post description.
     * @apiSuccess {String[]} posts.images Post images.
     * @apiSuccess {Object} posts.video Post video.
     * @apiSuccess {String} posts.video.thumbnail Video thumbnail.
     * @apiSuccess {String} posts.video.url Video url.
     * @apiSuccess {String} posts.date Post date.
     * @apiSuccess {Number} posts.likes Post likes.
     * @apiSuccess {Boolean} posts.like_status Post like status.
     * @apiSuccess {Boolean} posts.bookmark_status Post bookmark status.
     */
    public function show(Post $post): JsonResponse
    {
        $post->load(['user', 'likes', 'bookmarkers']);
        return response()->json(new PostResource($post));
    }

    /**
     * @api {post} /posts/create Create post
     * @apiName CreatePost
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiParam {File} images[] Array of images 500x500 less 2mb. May contains to 6 files.
     * @apiParam {File} video Video no longer that 30 second and less 10mb.
     * @apiParam {String} [description] Post description.
     *
     * @apiSuccess {String} message Is post published message.
     * @apiSuccess {Number} id Created post id.
     */
    public function store(PostRequest $request, PostService $service): JsonResponse
    {
        $post = $service->store(
            $request->input('description'), $request->file('images'), $request->file('video')
        );

        return response()->json([
            'message' => 'Post has been published.',
            'id' => $post->id
        ], Response::HTTP_CREATED);
    }

    /**
     * @api {post} /posts/:id/like Toggle like
     * @apiName ToggleLike
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle status message.
     * @apiSuccess {Boolean} liked Like status.
     */
    public function like(Post $post): JsonResponse
    {
        $liked = $this->service->toggleLike($post);
        $action = $liked ? 'liked' : 'unliked';

        return response()->json([
            'message' => "Post has been $action.",
            'liked' => $liked
        ]);
    }

    /**
     * @api {delete} /posts/:id Delete own post
     * @apiName DeletePost
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Is post deleted message.
     */
    public function destroy(Post $post): JsonResponse
    {
        abort_unless(
            $post->user_id === \Auth::id(), Response::HTTP_FORBIDDEN, __('Your are not allowed to delete this post.')
        );

        $post->forceDelete();

        return response()->json([
            'message' => 'Post has been deleted.'
        ]);
    }
}

<?php

/**
 * @apiDefine Posts
 * @apiSuccess {Object[]} posts User's posts.
 * @apiSuccess {Number} posts.id Post id.
 * @apiSuccess {String} posts.description Post description.
 * @apiSuccess {String[]} posts.images Post images.
 * @apiSuccess {Object} posts.video Post video.
 * @apiSuccess {String} posts.video.thumbnail Video thumbnail.
 * @apiSuccess {String} posts.video.url Video url.
 */

namespace Modules\Api\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Api\Http\Requests\PostRequest;
use Modules\Api\Http\Resources\PostResource;
use Modules\Api\Repositories\PostRepository;
use Modules\Api\Services\PostService;

class PostController extends Controller
{
    protected PostRepository $repository;

    public function __construct()
    {
        $this->repository = app(PostRepository::class);
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
     * @api {post} /posts/create Create post
     * @apiName CreatePost
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiParam {File} images[] Array of images 500x500 less 2mb. May contains to 6 files.
     * @apiParam {File} Video no longer that 30 second and less 10mb.
     * @apiParam {String} [description] Post description.
     *
     * @apiSuccess {String} message Is post published message.
     * @apiSuccess {Number} id Created post id.
     */
    public function store(PostRequest $request, PostService $service): JsonResponse
    {
        $description = $request->input('description');
        $tags = $description ? $service->parseTags($description) : null;
        /* @var $post Post */
        $post = \Auth::user()->posts()->create(compact('description', 'tags'));

        if ($images = $request->file('images')) {
            foreach ($request->file('images') as $file) {
                $service->compressImage($file);
                $post->addMedia($file)->toMediaCollection('images');
            }
        }

        if ($video = $request->file('video')) {
            $media = $post->addMedia($video)->toMediaCollection('video');
            $service->compressVideo($media);
        }

        return response()->json([
            'message' => 'Post has been published.',
            'id' => $post->id
        ], 201);
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
            $post->user_id === \Auth::id(), 403, __('Your are not allowed to delete this post.')
        );

        $post->forceDelete();

        return response()->json([
            'message' => 'Post has been deleted.'
        ]);
    }
}

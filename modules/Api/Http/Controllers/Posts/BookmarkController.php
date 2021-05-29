<?php

namespace Modules\Api\Http\Controllers\Posts;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Controllers\Controller;
use Modules\Api\Http\Resources\PostResource;
use Modules\Api\Repositories\PostRepository;
use Modules\Api\Services\PostService;

final class BookmarkController extends Controller
{
    protected PostService $service;
    protected PostRepository $repository;

    public function __construct(PostService $service, PostRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @api {get} /bookmarks Bookmarks
     * @apiDescription User bookmarks.
     * @apiName IndexBookmarks
     * @apiGroup Posts
     *
     * @apiUse Token
     * @apiUse Posts
     * @apiUse Pagination
     */
    public function index(): JsonResponse
    {
        $posts = $this->repository->getBookmarks(\Auth::user());

        return response()->json([
            'posts' => PostResource::collection($posts),
            'hasMorePages' => $posts->hasMorePages()
        ]);
    }

    /**
     * @api {post} /posts/:id/bookmark Toggle bookmark
     * @apiName ToggleBookmark
     * @apiGroup Posts
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Toggle message.
     * @apiSuccess {Boolean} bookmarked Bookmarked status.
     */
    public function toggle(Post $post): JsonResponse
    {
        $bookmarked = $this->service->toggleBookmark($post);
        $action = $bookmarked ? 'saved' : 'removed from bookmarks';

        return response()->json([
            'message' => "Post has been $action.",
            'bookmarked' => $bookmarked
        ]);
    }
}

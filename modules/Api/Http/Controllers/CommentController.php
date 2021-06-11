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
use App\Models\Post\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Api\Http\Requests\CommentRequest;
use Modules\Api\Http\Resources\CommentResource;
use Modules\Api\Notifications\Push\PostCommented;
use Modules\Api\Repositories\CommentRepository;

final class CommentController extends Controller
{
    protected CommentRepository $repository;

    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @api {get} /posts/:id/comments/:parent Post comments
     * @apiDescription List of comments related to post.
     * @apiName IndexComments
     * @apiGroup Comments
     *
     * @apiUse Token
     * @apiSuccess {Object[]} comments Post comments.
     * @apiSuccess {Number} comments.id Comment id.
     * @apiSuccess {Number} comments.user_id Comment author id.
     * @apiSuccess {Object[]} comments.user Comment author.
     * @apiSuccess {String} comments.user.name Comment author name.
     * @apiSuccess {String} comments.user.avatar Comment author avatar.
     * @apiSuccess {String} comments.text Comment text.
     * @apiSuccess {String} comments.date Comment date.
     * @apiSuccess {Boolean} comments.hasReplies Indicates that comment has replies.
     * @apiSuccess {Number} comments.repliesCount Comment replies count.
     * @apiUse Pagination
     */
    public function index(Post $post, Comment $comment = null): JsonResponse
    {
        if ($comment) {
            $comments = $this->repository->getForComment($comment);
        } else {
            $comments = $this->repository->getForPost($post);
        }

        return response()->json([
            'comments' => CommentResource::collection($comments),
            'hasMorePages' => $comments->hasMorePages()
        ]);
    }

    /**
     * @api {post} /posts/:id/comments/:parent Create post
     * @apiName CreateComment
     * @apiGroup Comments
     *
     * @apiUse Token
     *
     * @apiParam {String} text Comment description.
     *
     * @apiSuccess {String} message Is comment published message.
     * @apiSuccess {Number} id Created comment id.
     */
    public function store(Post $post, Comment $parent = null, CommentRequest $request): JsonResponse
    {
        $comment = $this->repository->store($post, \Auth::id(), $request->input('text'), $parent);

        $user = \Auth::user();
        if ($comment && $post->user_id !== $user->id) {
            $post->user->notify(new PostCommented($user, $post, $comment));
        }

        return response()->json([
            'message' => 'Comment has been created.',
            'id' => $comment->id
        ], Response::HTTP_CREATED);
    }

    /**
     * @api {delete} /posts/:id/comments/:id Delete own comment
     * @apiName DeleteComment
     * @apiGroup Comments
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Is comment deleted message.
     */
    public function destroy(Post $post, Comment $comment): JsonResponse
    {
        abort_unless(
            $comment->user_id === \Auth::id(), Response::HTTP_FORBIDDEN, __('Your are not allowed to delete this comment.')
        );

        $comment->forceDelete();

        return response()->json([
            'message' => 'Comment has been deleted.'
        ]);
    }
}

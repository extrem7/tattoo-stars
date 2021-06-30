<?php

namespace Modules\Api\Repositories;

use App\Models\Post;
use App\Models\Post\Comment;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

class CommentRepository
{

    /* @return Paginator<Comment> */
    public function getForPost(Post $post): Paginator
    {
        return $post->comments()->whereNull('comment_id')->with(['user'])->withCount('replies')->latest()->simplePaginate(12);
    }

    /* @return Paginator<Comment> */
    public function getForComment(Comment $comment): Paginator
    {
        return $comment->replies()->with(['user'])->latest()->simplePaginate(12);
    }

    /* @return Comment|Model */
    public function store(Post $post, int $userId, string $text, Comment $parent = null): Comment
    {
        $data = ['user_id' => $userId, 'text' => $text];

        $parentId = $parent->id ?? null;
        if ($parentId && $parent->comment_id) {
            $parentId = $parent->comment_id;
        }

        $data['comment_id'] = $parentId;

        return $post->comments()->create($data);
    }
}

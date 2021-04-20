<?php

namespace Modules\Api\Repositories;

use App\Models\Post;
use App\Models\Post\Comment;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

class CommentRepository
{
    public function getForPost(Post $post): Paginator
    {
        return $post->comments()->with(['user'])->simplePaginate(12);
    }

    /* @return Comment|Model */
    public function store(Post $post, int $userId, string $text): Comment
    {
        return $post->comments()->create(['user_id' => $userId, 'text' => $text]);
    }
}

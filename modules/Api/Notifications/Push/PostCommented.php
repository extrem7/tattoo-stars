<?php

namespace Modules\Api\Notifications\Push;

use App\Models\Post;
use App\Models\Post\Comment;
use App\Models\User;
use Modules\Api\Http\Resources\Notifications\PostCommentedNotification;

class PostCommented extends PostNotification
{
    protected Comment $comment;

    protected string $resourceClass = PostCommentedNotification::class;

    public function __construct(User $user, Post $post, Comment $comment)
    {
        parent::__construct($user, $post);

        $this->comment = $comment;
    }

    public function toArray(User $notifiable): array
    {
        return array_merge(parent::toArray($notifiable), ['comment' => $this->comment]);
    }
}

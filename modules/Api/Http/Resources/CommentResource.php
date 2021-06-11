<?php

namespace Modules\Api\Http\Resources;

use App\Models\Post\Comment;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $comment Comment */
        $comment = $this->resource;

        return [
            'id' => $comment->id,
            'user_id' => $comment->user_id,
            'user' => $this->when($comment->relationLoaded('user'), fn() => [
                'name' => $comment->user->name,
                'avatar' => $comment->user->getAvatar('icon')
            ]),
            'text' => $comment->text,
            'date' => $comment->created_at,
            'hasReplies' => $this->when(!$comment->comment_id, (bool)$comment->replies_count),
            'repliesCount' => $this->when($comment->replies_count, $comment->replies_count)
        ];
    }
}

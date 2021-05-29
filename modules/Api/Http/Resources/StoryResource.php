<?php

namespace Modules\Api\Http\Resources;

use App\Models\Story;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $story Story */
        $story = $this->resource;

        return [
            'id' => $story->id,
            'post' => PostResource::make($story->post),
            'rating' => $story->rating,
            $this->mergeWhen($story->relationLoaded('marks'), function () use ($story) {
                $likeStatus = null;
                if ($mark = $story->marks->where('id', '=', \Auth::id())->first()) {
                    $likeStatus = $mark->pivot->is_dislike === null ? 'like' : 'dislike';
                }
                return [
                    'likes' => $story->likes_count,
                    'dislikes' => $story->dislikes_count,
                    'likeStatus' => $likeStatus
                ];
            }),
            'viewed' => $this->when($story->relationLoaded('views'), fn() => $story->views->count() !== 0)
        ];
    }
}

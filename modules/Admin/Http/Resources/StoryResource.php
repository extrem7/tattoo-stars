<?php

namespace Modules\Admin\Http\Resources;

use App\Models\Story;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /* @var Story */
    public $resource;

    public function toArray($request = null): array
    {
        $story = $this->resource;

        if ($video = $story->post->videoMedia) {
            $thumbnail = $video->getFullUrl('thumbnail');
        } else {
            $thumbnail = $story->post->imagesMedia()->first()->getFullUrl('thumb');
        }

        return [
            'id' => $story->id,
            'postId' => $story->post_id,
            'thumbnail' => $thumbnail,
            'rating' => $story->rating,
            'likes' => $story->likes_count,
            'dislikes' => $story->dislikes_count,
            'views' => $story->views_count,
            'total' => $this->when($story->total !== null, fn() => $story->total),
            'totalToday' => $this->when($story->total_today !== null, fn() => $story->total_today),
            'included' => (bool)$story->included,
            'excluded' => (bool)$story->excluded,
            'createdAt' => $story->created_at
        ];
    }
}

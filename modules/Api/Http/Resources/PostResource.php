<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Post;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $post Post */
        $post = $this->resource;

        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'user' => $this->when($post->relationLoaded('user'), fn() => [
                'name' => $post->user->name,
                'avatar' => $post->user->getAvatar('icon')
            ]),
            'description' => $post->description,
            'images' => $this->when(
                $post->imagesMedia->isNotEmpty(), fn() => $post->imagesMedia->map(fn(Media $m) => $m->getFullUrl())
            ),
            'video' => $this->when($post->videoMedia, fn() => [
                'thumbnail' => $post->videoMedia->getFullUrl('thumbnail'),
                'url' => $post->videoMedia->getFullUrl()
            ]),
            'date' => $post->created_at
        ];
    }
}

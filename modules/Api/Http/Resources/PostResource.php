<?php

namespace Modules\Api\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        /* @var $post Post */
        $post = $this->resource;
        $authUser = \Auth::user();

        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'user' => $this->when($post->relationLoaded('user'), fn() => [
                'name' => $post->user->name,
                'avatar' => $post->user->getAvatar('icon'),
                'inSubscriptions' => (bool)$authUser->subscriptions()->find($post->user->id, ['id'])
            ]),
            'description' => $post->description,
            'images' => $this->when(
                $post->imagesMedia->isNotEmpty(), fn() => $post->imagesMedia->map(fn(Media $m) => $m->getFullUrl())
            ),
            'video' => $this->when($post->videoMedia, fn() => [
                'thumbnail' => $post->videoMedia->getFullUrl('thumbnail'),
                'url' => $post->videoMedia->getFullUrl()
            ]),
            'date' => $post->created_at,
            $this->mergeWhen($post->relationLoaded('likes'), fn() => [
                'likes' => $post->likes->count(),
                'like_status' => $post->likes->where('id', '=', \Auth::id())->first() !== null
            ]),
            'hasComments' => (bool)$post->comments_count,
            'commentsCount' => $this->when($post->comments_count, $post->comments_count),
            'bookmark_status' => $this->when(
                $post->relationLoaded('bookmarkers'), fn() => $post->bookmarkers->isNotEmpty()
            ),
        ];
    }
}

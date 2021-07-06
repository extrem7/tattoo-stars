<?php

namespace Modules\Api\Http\Resources;

use App\Models\ContestWork;
use Illuminate\Http\Resources\Json\JsonResource;

class ContestWorkResource extends JsonResource
{
    protected int $totalVotes = 0;

    public function toArray($request): array
    {
        /* @var $work ContestWork */
        $work = $this->resource;
        $post = $work->post;

        if ($video = $this->post->videoMedia) {
            $thumbnail = $video->getFullUrl('thumbnail');
        } else {
            $thumbnail = $this->post->imagesMedia()->first()->getFullUrl('thumb');
        }

        return [
            'id' => $work->id,
            'date' => $this->when($work->date, $work->date),
            'post' => [
                'id' => $post->id,
                'user' => [
                    'id' => $post->user_id,
                    'name' => $post->user->name,
                    'avatar' => $post->user->getAvatar('icon'),
                ],
                'thumbnail' => $thumbnail
            ],
            'percents' => $this->when($this->totalVotes, fn() => ceil($work->votes_count / $this->totalVotes * 100)),
            'voteStatus' => $this->when($work->relationLoaded('votes'), $work->votes->isNotEmpty())
        ];
    }

    public function setTotalVotes(int $count): self
    {
        $this->totalVotes = $count;
        return $this;
    }
}

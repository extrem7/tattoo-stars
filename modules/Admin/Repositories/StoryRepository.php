<?php

namespace Modules\Admin\Repositories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Http\Resources\StoryResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StoryRepository
{
    public function getForIndex(array $search): LengthAwarePaginator
    {
        $id = \Auth::id();

        $subselect = 'select count(*) from stories s2 where s2.post_id = stories.post_id';

        return Story::daily()
            ->with([
                'post' => fn($q) => $q->with(['user', 'imagesMedia', 'videoMedia']),
                'marks' => fn($q) => $q->where('user_id', '=', $id)->withPivot('is_dislike'),
                'views' => fn($q) => $q->where('story_views.user_id', '=', $id)
            ])
            ->withCount(['views', 'likes', 'dislikes'])
            ->when($search['searchQuery'] ?? false, fn(Builder $q) => $q->search($search['searchQuery']))
            ->when(
                isset($search['sortBy']) || isset($search['sortDesc']),
                fn(Builder $q) => $q->orderBy(
                    $search['sortBy'] ?? 'created_at', $search['sortDesc'] ?? false ? 'desc' : 'asc'
                )
            )
            ->selectRaw(/* @lang MySQL */ "($subselect) as total")
            ->selectRaw(/* @lang MySQL */ "($subselect and date(s2.created_at) = current_date) as total_today")
            ->paginate(10);
    }

    public function transformStories(LengthAwarePaginator $stories): void
    {
        /* @var $promotions Collection<Story> */
        $stories->transform(function (Story $story) {
            $data = (new StoryResource($story))->toArray();
            $data['user'] = $story->user;
            $images = $story->post->imagesMedia->map(fn(Media $m) => $m->getFullUrl('thumb'));
            if ($story->post->videoMedia) {
                $images[] = $story->post->videoMedia->getFullUrl('thumbnail');
            }
            $data['images'] = $images;

            return $data;
        });
    }
}

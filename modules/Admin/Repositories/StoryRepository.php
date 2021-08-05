<?php

namespace Modules\Admin\Repositories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StoryRepository
{
    public function getForIndex(array $search): LengthAwarePaginator
    {
        $id = \Auth::id();

        $subselect = 'select count(*) from stories s2 where s2.post_id = stories.post_id';

        return Story::daily()
            ->with([
                'post.user',
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
}

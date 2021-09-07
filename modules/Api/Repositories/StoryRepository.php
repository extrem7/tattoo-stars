<?php

namespace Modules\Api\Repositories;

use App\Models\Post;
use App\Models\Story;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class StoryRepository
{
    /* @return Paginator<Story> */
    public function getForIndex(): Paginator
    {
        return Story::daily()
            ->with([
                'post.user',
                'marks' => fn($q) => $q->where('user_id', '=', \Auth::id())->withPivot('is_dislike'),
                'views' => fn($q) => $q->where('story_views.user_id', '=', \Auth::id())
            ])
            ->withCount(['likes', 'dislikes'])
            ->latest()
            ->simplePaginate(20);
    }

    public function getYesterday(): Collection
    {
        return Story::yesterday()
            ->with(['post.user'])
            ->orderByDesc('rating')
            //->limit(10)
            ->get();
    }

    public function store(Post $post): Story
    {
        return $post->stories()->create();
    }
}
